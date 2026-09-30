<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('initials', 4)->nullable()->after('name');
            $table->string('role')->default('editor')->after('initials');
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            // review | published | rejected
            $table->string('status')->default('review')->index();
            // ia | cyber | data | opp
            $table->string('theme', 12)->index();
            // 1 débutant, 2 intermédiaire, 3 avancé
            $table->unsignedTinyInteger('level')->default(1);
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('essential')->nullable();
            $table->json('key_points')->nullable();
            $table->text('why')->nullable();
            $table->json('body')->nullable();
            $table->json('tags')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_title')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_lang', 40)->nullable();
            $table->date('source_date')->nullable();
            $table->string('reviewer')->nullable();
            $table->unsignedSmallInteger('reading_minutes')->default(3);
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedSmallInteger('size_kb')->default(14);
            $table->timestamp('published_at')->nullable()->index();
            // Place dans « L'essentiel du jour » (1 à 5), choisie par la rédaction
            $table->unsignedTinyInteger('daily_rank')->nullable();

            // Relecture (back-office)
            $table->string('source_headline')->nullable();
            $table->json('source_paragraphs')->nullable();
            $table->json('draft_blocks')->nullable();
            $table->json('draft_edits')->nullable();
            $table->timestamp('ai_drafted_at')->nullable();
            $table->boolean('source_checked')->default(false);
            $table->unsignedTinyInteger('suggested_level')->nullable();
            $table->string('reject_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('review_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->string('key', 20);
            $table->unsignedTinyInteger('position');
            $table->string('type');
            $table->text('why');
            $table->string('source_ref', 20)->nullable();
            $table->text('quote')->nullable();
            $table->text('flagged');
            $table->text('fix');
            // open | fixed | removed | ok
            $table->string('state')->default('open');
            $table->timestamps();
            $table->unique(['article_id', 'key']);
        });

        Schema::create('glossary_terms', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('term');
            $table->string('english')->nullable();
            $table->string('theme', 12)->index();
            $table->text('definition');
            $table->text('example')->nullable();
            $table->string('keywords')->nullable();
            $table->timestamps();
        });

        Schema::create('article_glossary_term', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('glossary_term_id')->constrained()->cascadeOnDelete();
            $table->primary(['article_id', 'glossary_term_id']);
        });

        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            // Bourse | Stage | Emploi | Formation | Concours
            $table->string('type', 20)->index();
            $table->string('kind_label', 30)->nullable();
            $table->string('money', 30);
            $table->string('title');
            $table->string('card_line')->nullable();
            $table->string('org');
            $table->string('place');
            $table->unsignedTinyInteger('level')->default(1);
            $table->json('countries');
            $table->timestamp('deadline_at')->index();
            $table->string('duration')->nullable();
            $table->text('description');
            $table->json('conditions');
            $table->json('documents');
            $table->string('apply_url')->nullable();
            $table->boolean('published')->default(true);
            $table->timestamps();
        });

        Schema::create('opportunity_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->string('device_id', 64);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            $table->unique(['opportunity_id', 'device_id']);
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('theme', 12)->index();
            $table->unsignedTinyInteger('level')->default(1);
            $table->string('title');
            $table->string('head1');
            $table->string('head2');
            $table->text('description');
            $table->unsignedSmallInteger('size_kb');
            $table->string('author')->nullable();
            $table->string('author_role')->nullable();
            $table->string('prerequisites')->default('Aucun');
            $table->string('format')->default('Texte, schémas, exercices');
            $table->json('outcomes')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('title');
            // Leçon | Exercice | Projet | Quiz
            $table->string('kind', 20)->default('Leçon');
            $table->unsignedSmallInteger('minutes');
            $table->text('summary');
            $table->json('body')->nullable();
            $table->timestamps();
        });

        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('device_id', 64);
            $table->unsignedSmallInteger('done')->default(0);
            $table->timestamps();
            $table->unique(['course_id', 'device_id']);
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->timestamp('starts_at')->index();
            $table->string('time_label', 20);
            $table->string('title');
            $table->string('theme', 12);
            $table->string('kind', 30);
            $table->string('place');
            $table->string('city', 30)->index();
            $table->string('price', 40);
            $table->string('url')->nullable();
            $table->timestamps();
        });

        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('device_id', 64);
            $table->timestamps();
            $table->unique(['event_id', 'device_id']);
        });

        Schema::create('startups', function (Blueprint $table) {
            $table->id();
            $table->string('mono', 3);
            $table->string('name');
            $table->text('description');
            $table->string('sector', 30);
            $table->string('city', 30)->index();
            $table->string('stage', 30);
            $table->boolean('hiring')->default(false);
            $table->string('color', 40);
            $table->string('url')->nullable();
            $table->timestamps();
        });

        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->string('mono', 3);
            $table->string('name');
            $table->string('kind', 40);
            $table->string('city', 30)->index();
            $table->unsignedInteger('members');
            $table->string('cadence');
            // WhatsApp | Telegram
            $table->string('channel', 20);
            $table->string('channel_url')->nullable();
            $table->string('theme', 12);
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('mono', 3);
            $table->string('name');
            $table->string('role');
            $table->string('color', 40);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('unsubscribe_token', 64)->unique();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('name');
            $table->string('contact');
            $table->string('contact_via', 20);
            $table->json('domains');
            $table->text('message')->nullable();
            $table->timestamps();
        });

        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            // term | topic | event | startup | community
            $table->string('kind', 20)->index();
            $table->string('text');
            $table->text('details')->nullable();
            $table->string('contact')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['suggestions', 'contributions', 'newsletter_subscribers', 'team_members', 'communities', 'startups',
            'event_attendances', 'events', 'course_progress', 'lessons', 'courses', 'opportunity_reminders',
            'opportunities', 'article_glossary_term', 'glossary_terms', 'review_flags', 'articles'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['initials', 'role']);
        });
    }
};
