<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Contenus de démonstration (fictifs mais réalistes), repris des maquettes.
     * Les dates sont relatives au moment du seed : relancer `php artisan migrate:fresh --seed`
     * remet les comptes à rebours et « L'essentiel du jour » à jour.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Kadiatou Touré',
            'initials' => 'KT',
            'role' => 'editor',
            'email' => env('TECHPULSE_EDITOR_EMAIL', 'redaction@techpulse.gn'),
            'password' => env('TECHPULSE_EDITOR_PASSWORD', 'techpulse-redaction'),
        ]);

        $this->call([
            GlossarySeeder::class,
            ArticleSeeder::class,
            ReviewSeeder::class,
            OpportunitySeeder::class,
            CourseSeeder::class,
            EcosystemSeeder::class,
        ]);
    }
}
