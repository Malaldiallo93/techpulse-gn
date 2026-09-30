<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function draft(): Article
    {
        return Article::where('slug', 'un-jeu-de-donnees-vocal-ouvert-en-pular-maninka-et-soussou')->firstOrFail();
    }

    public function test_back_office_requires_login(): void
    {
        $this->get('/redaction')->assertRedirect(route('login'));
        $this->post('/redaction/connexion', ['email' => 'redaction@techpulse.gn', 'password' => 'faux'])->assertSessionHasErrors('email');
        $this->post('/redaction/connexion', ['email' => 'redaction@techpulse.gn', 'password' => 'techpulse-redaction'])->assertRedirect(route('redaction.index'));
    }

    public function test_publish_is_blocked_until_flags_are_treated_and_source_checked(): void
    {
        $editor = User::first();
        $a = $this->draft();
        $this->actingAs($editor);

        $this->get(route('redaction.show', $a->id))->assertOk()->assertSee('3 passages à traiter')->assertSee('Publier · 3 restants');

        // Bouton bloqué : on est renvoyé vers le prochain passage ouvert.
        $this->post(route('redaction.publish', $a->id))->assertRedirect(route('redaction.show', ['article' => $a->id, 'passage' => 'f1', 'onglet' => 'resume']));
        $this->assertSame('review', $a->fresh()->status);

        $this->post(route('redaction.flag', [$a->id, 'f1']), ['state' => 'fixed']);
        $this->post(route('redaction.flag', [$a->id, 'f2']), ['state' => 'removed']);
        $this->post(route('redaction.flag', [$a->id, 'f3']), ['state' => 'ok']);
        $this->assertFalse($a->fresh()->load('flags')->canPublish()); // source pas encore vérifiée

        $this->post(route('redaction.source', $a->id));
        $this->post(route('redaction.publish', $a->id));

        $a = $a->fresh();
        $this->assertSame('published', $a->status);
        $this->assertStringContainsString('l’une des plus grandes collections ouvertes pour ces langues', $a->essential);
        $this->assertStringNotContainsString('1 200', implode(' ', $a->key_points));
        $this->assertSame('Environ 300 heures, enregistrés à Conakry, Labé et Kankan.', $a->key_points[0]);
        $this->get($a->url())->assertOk();
        $this->get('/')->assertSee('Un jeu de données vocal');
    }

    public function test_a_rewritten_block_counts_as_treated(): void
    {
        $this->actingAs(User::first());
        $a = $this->draft();
        $this->post(route('redaction.edit', $a->id), ['blocks' => ['p1' => 'Environ 300 heures, plus de 900 locuteurs.']]);
        $a = $a->fresh()->load('flags');
        $this->assertSame('edited', $a->effectiveFlagState($a->flags->firstWhere('key', 'f2')));
        $this->assertSame(2, $a->openFlagsCount());
    }

    public function test_reject_requires_a_reason_and_can_be_reopened(): void
    {
        $this->actingAs(User::first());
        $a = $this->draft();
        $this->post(route('redaction.reject', $a->id), ['reason' => ''])->assertSessionHasErrors('reason');
        $this->post(route('redaction.reject', $a->id), ['reason' => 'Source peu fiable']);
        $this->assertSame('rejected', $a->fresh()->status);
        $this->post(route('redaction.reopen', $a->id));
        $this->assertSame('review', $a->fresh()->status);
    }
}
