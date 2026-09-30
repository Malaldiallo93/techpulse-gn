<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Course;
use App\Models\Event;
use App\Models\Opportunity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_every_reader_page_renders(): void
    {
        $article = Article::published()->first();
        $opp = Opportunity::open()->first();
        $course = Course::first();

        foreach (['/', '/actualites', $article->url(), '/opportunites', route('opportunities.show', $opp), '/apprendre',
            route('courses.show', $course), route('courses.lesson', [$course, 1]), '/glossaire', '/ecosysteme',
            '/recherche?q=python', '/a-propos', '/hors-ligne', '/mentions-legales'] as $url) {
            $this->get($url)->assertOk()->assertSee('TECHPULSE', false);
        }
    }

    public function test_home_shows_the_daily_essentials_and_closing_opportunities(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('L’essentiel', false)
            ->assertSee('Faux SMS de mobile money', false)
            ->assertSee('Stage data analyst', false)
            ->assertSee('Tech<br>Pulse<br><em>Brief</em>', false);
    }

    public function test_drafts_in_review_are_not_public(): void
    {
        $draft = Article::where('status', 'review')->first();
        $this->get(route('articles.show', $draft))->assertNotFound();
        $this->get('/actualites')->assertDontSee($draft->title);
    }

    public function test_article_marks_glossary_terms_and_links_the_source(): void
    {
        $a = Article::where('slug', 'like', 'faux-sms%')->first();
        $this->get($a->url())->assertOk()
            ->assertSee('class="gloss" data-term="hameconnage"', false)
            ->assertSee('Lire l’original', false)
            ->assertSee('Pourquoi c’est<br>important<br>pour toi_', false);
    }

    public function test_search_index_and_glossary_json_are_available_offline_friendly(): void
    {
        $this->getJson('/recherche/index.json')->assertOk()->assertJsonFragment(['t' => 'g', 'ti' => 'Hameçonnage']);
        $this->getJson('/glossaire.json')->assertOk()->assertJsonFragment(['en' => 'Phishing']);
    }

    public function test_newsletter_validates_and_subscribes(): void
    {
        $this->postJson('/newsletter', ['email' => 'pas-une-adresse'])->assertStatus(422);
        $this->postJson('/newsletter', ['email' => 'Aminata@Exemple.com'])->assertOk();
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'aminata@exemple.com']);
    }

    public function test_contribution_form_detects_the_contact_channel(): void
    {
        $this->postJson('/contribuer', ['role' => 0, 'name' => '', 'contact' => 'x', 'domains' => []])
            ->assertStatus(422)->assertJsonValidationErrors(['name', 'contact', 'domains']);

        $this->postJson('/contribuer', ['role' => 2, 'name' => 'Aminata Keïta', 'contact' => '+224 620 00 00 00', 'domains' => ['Cyber']])
            ->assertOk()->assertJson(['first' => 'Aminata', 'via' => 'WhatsApp']);
        $this->assertDatabaseHas('contributions', ['name' => 'Aminata Keïta', 'contact_via' => 'whatsapp', 'role' => 'Relire et vérifier']);
    }

    public function test_reminder_progress_and_attendance_are_stored_per_device(): void
    {
        $this->withCredentials()->withCookie('techpulse_device', '11111111-2222-3333-4444-555555555555');
        $opp = Opportunity::open()->first();
        $this->postJson(route('opportunities.remind', $opp))->assertOk()->assertJson(['on' => true]);
        $this->assertDatabaseCount('opportunity_reminders', 1);

        $course = Course::first();
        $this->postJson(route('courses.progress', $course), ['done' => 3])->assertOk()->assertJson(['done' => 3]);
        $this->postJson(route('courses.progress', $course), ['done' => 1])->assertOk()->assertJson(['done' => 3]); // jamais en recul

        $event = Event::first();
        $this->postJson(route('events.attend', $event))->assertOk()->assertJson(['going' => true]);
    }

    public function test_suggestions_are_recorded(): void
    {
        $this->postJson('/suggestions', ['kind' => 'term', 'text' => 'Blockchain'])->assertOk();
        $this->assertDatabaseHas('suggestions', ['kind' => 'term', 'text' => 'Blockchain']);
    }
}
