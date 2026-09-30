<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\Event;
use App\Models\Startup;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class EcosystemSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [8, '18:00', '18 h', 'Meetup cyber : sécuriser le mobile money', 'cyber', 'Meetup', 'Conakry, Kaloum', 'Conakry', 'Gratuit'],
            [12, '10:00', '10 h', 'Atelier Python pour débutantes', 'data', 'Atelier', 'En ligne', 'En ligne', 'Gratuit'],
            [24, '09:00', '48 h', 'Hackathon IA pour la santé', 'ia', 'Hackathon', 'Dakar et en ligne', 'En ligne', 'Sur candidature'],
            [36, '09:00', '9 h', 'Journée open data : les chiffres de la région', 'data', 'Journée', 'Kankan, université', 'Kankan', 'Gratuit'],
            [45, '15:00', '15 h', 'Conférence : l’IA va-t-elle changer nos métiers ?', 'ia', 'Conférence', 'Conakry, Ratoma', 'Conakry', 'Gratuit'],
            [53, '14:00', '14 h', 'Initiation à la cybersécurité pour les lycéens', 'cyber', 'Atelier', 'Labé', 'Labé', 'Gratuit'],
        ];
        foreach ($events as [$days, $at, $label, $title, $theme, $kind, $place, $city, $price]) {
            [$h, $m] = explode(':', $at);
            Event::create([
                'starts_at' => now()->addDays($days)->setTime((int) $h, (int) $m),
                'time_label' => $label, 'title' => $title, 'theme' => $theme, 'kind' => $kind,
                'place' => $place, 'city' => $city, 'price' => $price,
            ]);
        }

        $startups = [
            ['SP', 'Sabou Pay', 'Paiement marchand par QR code pour les petits commerces.', 'Fintech', 'Conakry', 'En croissance', true, 'oklch(0.56 0.21 27)'],
            ['KA', 'Kolon Agri', 'Prix des marchés agricoles envoyés par SMS aux producteurs.', 'Agritech', 'Kankan', 'Lancée', false, 'oklch(0.45 0.12 155)'],
            ['WS', 'Wuli Santé', 'Prise de rendez-vous médical par SMS, sans smartphone.', 'Santé', 'Conakry', 'Lancée', true, 'oklch(0.42 0.13 240)'],
            ['FL', 'Faran Learn', 'Cours de lycée téléchargeables, consultables hors ligne.', 'Edtech', 'Labé', 'Idée validée', false, 'oklch(0.45 0.18 285)'],
            ['DL', 'Dembaya Logistique', 'Suivi de colis entre Conakry et les préfectures.', 'Logistique', 'Conakry', 'En croissance', false, 'oklch(0.3 0 0)'],
            ['TD', 'Tinkisso Data', 'Enquêtes terrain sur mobile pour ONG et collectivités.', 'Data', 'Kankan', 'Lancée', true, 'oklch(0.48 0.11 80)'],
        ];
        foreach ($startups as [$mono, $name, $desc, $sector, $city, $stage, $hiring, $color]) {
            Startup::create(compact('mono', 'name', 'sector', 'city', 'stage', 'hiring', 'color') + ['description' => $desc]);
        }

        $communities = [
            ['CI', 'Club IA étudiant', 'Club universitaire', 'Conakry', 240, 'Tous les jeudis, 17 h', 'WhatsApp', 'ia'],
            ['FC', 'Femmes & Code Guinée', 'Association', 'Conakry', 610, 'Ateliers mensuels', 'Telegram', 'opp'],
            ['CS', 'Cyber Sécurité Conakry', 'Meetup', 'Conakry', 380, 'Un meetup par mois', 'Telegram', 'cyber'],
            ['DK', 'Data Kankan', 'Groupe d’entraide', 'Kankan', 120, 'Défis hebdomadaires', 'WhatsApp', 'data'],
            ['PL', 'Python Labé', 'Club', 'Labé', 85, 'Samedis matin', 'WhatsApp', 'data'],
            ['OD', 'Open Data GN', 'Communauté en ligne', 'En ligne', 950, 'Discussions continues', 'Telegram', 'data'],
        ];
        foreach ($communities as [$mono, $name, $kind, $city, $members, $cadence, $channel, $theme]) {
            Community::create(compact('mono', 'name', 'kind', 'city', 'members', 'cadence', 'channel', 'theme') + [
                'channel_url' => $channel === 'WhatsApp' ? 'https://chat.whatsapp.com/' : 'https://t.me/',
            ]);
        }

        $team = [
            ['MS', 'Mariama Sow', 'Rédactrice en chef, cybersécurité', 'var(--cyber)'],
            ['AD', 'Aïssatou Diallo', 'Data et parcours d’apprentissage', 'var(--data)'],
            ['IC', 'Ibrahima Camara', 'Intelligence artificielle', 'var(--ia)'],
            ['FB', 'Fatoumata Bah', 'Opportunités et communauté', 'var(--opp)'],
            ['MC', 'Mamadou Condé', 'Développement et performance', 'var(--red)'],
            ['KT', 'Kadiatou Touré', 'Relecture et glossaire', 'oklch(0.6 0 0)'],
        ];
        foreach ($team as $i => [$mono, $name, $role, $color]) {
            TeamMember::create(compact('mono', 'name', 'role', 'color') + ['position' => $i]);
        }
    }
}
