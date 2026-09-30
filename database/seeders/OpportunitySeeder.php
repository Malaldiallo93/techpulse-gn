<?php

namespace Database\Seeders;

use App\Models\Opportunity;
use App\Support\TechPulse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OpportunitySeeder extends Seeder
{
    public const DOCS = [
        'Bourse' => ['CV d’une page', 'Relevés de notes', 'Lettre de motivation', 'Copie du passeport'],
        'Stage' => ['CV d’une page', 'Lettre de motivation', 'Attestation d’inscription'],
        'Emploi' => ['CV d’une page', 'Lien vers un projet'],
        'Formation' => ['Copie du bac', 'Pièce d’identité'],
        'Concours' => ['Vidéo de 3 minutes', 'Fiche projet'],
    ];

    public function run(): void
    {
        $all = TechPulse::COUNTRIES;
        // Les dates limites partent du moment du seed, pour que la démo reste valable.
        $items = [
            ['Stage', null, 'Rémunéré', 'Stage data analyst, 6 mois', 'Guinée · Bac+3', 'Startup fintech, Conakry', 'Conakry', 2, ['Guinée'], 19 * 60 + 40, '6 mois, dès novembre',
                'Au sein de l’équipe produit, tu analyses les usages d’une application de paiement mobile et prépares les tableaux de bord hebdomadaires.',
                ['Bac+3 minimum en statistiques, informatique ou économie', 'Bases de SQL et d’un tableur', 'Disponible à temps plein à Conakry']],
            ['Formation', null, 'Gratuit', 'Formation en cybersécurité, 12 places financées', null, 'Programme régional de formation numérique', 'En ligne et Conakry', 1, $all, (6 * 24 + 14) * 60, '10 semaines, soirs et samedis',
                'Dix semaines pour apprendre les bases de la sécurité des réseaux et de la réponse aux incidents. Les frais, la connexion et un ordinateur prêté sont pris en charge pour les 12 lauréats.',
                ['Avoir entre 18 et 30 ans', 'Bac obtenu, quelle que soit la filière', 'Être disponible 8 h par semaine']],
            ['Stage', null, 'Rémunéré', 'Stage analyste SOC, cybersécurité', null, 'Cabinet de conseil, Abidjan', 'Abidjan', 3, ['Côte d’Ivoire', 'Sénégal'], (3 * 24 + 2) * 60, '6 mois',
                'Surveillance des alertes de sécurité pour des clients bancaires, au sein d’un centre opérationnel de sécurité.',
                ['Master en cours en sécurité informatique', 'Connaissance d’un outil SIEM', 'Résider en Côte d’Ivoire ou au Sénégal']],
            ['Emploi', null, 'CDI', 'Développeur·se web junior', null, 'Agence numérique, Conakry', 'Conakry', 2, ['Guinée'], (9 * 24 + 5) * 60, 'CDI, temps plein',
                'Intégration de sites et d’applications pour des clients locaux, en équipe de quatre développeurs.',
                ['Un premier projet en ligne à montrer', 'HTML, CSS et JavaScript', 'Autodidactes bienvenus']],
            ['Concours', 'Hackathon', 'Prix', 'Hackathon IA pour la santé', 'Afrique de l’Ouest · Équipes de 3', 'Réseau ouest-africain d’innovation', 'En ligne et Dakar', 2, $all, (11 * 24 + 5) * 60, '48 h, sur trois jours',
                'Des équipes de trois conçoivent un prototype d’IA utile aux centres de santé. Les finalistes sont invités à Dakar.',
                ['Équipe de 3 personnes', 'Au moins un profil technique', 'Avoir moins de 35 ans']],
            ['Concours', null, 'Prix', 'Prix jeune innovateur tech', null, 'Incubateur de Conakry', 'Conakry', 1, ['Guinée'], 16 * 24 * 60, 'Finale en novembre',
                'Présente une idée de startup tech en 3 minutes. Les lauréats rejoignent six mois d’incubation.',
                ['Projet au stade idée ou prototype', 'Porteur de moins de 30 ans', 'Vidéo de présentation de 3 min']],
            ['Bourse', null, 'Financée', 'Bourse de master en science des données', null, 'Université partenaire, programme francophone', 'Dakar', 3, $all, 24 * 24 * 60, '2 ans, rentrée 2027',
                'Frais de scolarité, logement et allocation mensuelle couverts pour deux ans de master.',
                ['Licence en maths, informatique ou statistiques', 'Moyenne de 12/20 minimum', 'Lettre de motivation en français']],
            ['Formation', null, 'Gratuit', 'Les bases de Python, en ligne et à ton rythme', null, 'Plateforme de cours en ligne', 'En ligne', 1, $all, 30 * 24 * 60, '6 semaines, à ton rythme',
                'Un cours d’initiation avec certificat, pensé pour les connexions lentes : vidéos téléchargeables et exercices hors ligne.',
                ['Aucun prérequis', '3 h par semaine', 'Un téléphone ou un ordinateur']],
        ];

        foreach ($items as [$type, $kind, $money, $title, $line, $org, $place, $level, $countries, $minutes, $dur, $desc, $conds]) {
            Opportunity::create([
                'slug' => Str::slug($title),
                'type' => $type,
                'kind_label' => $kind,
                'money' => $money,
                'title' => $title,
                'card_line' => $line,
                'org' => $org,
                'place' => $place,
                'level' => $level,
                'countries' => $countries,
                'deadline_at' => now()->addMinutes($minutes),
                'duration' => $dur,
                'description' => $desc,
                'conditions' => $conds,
                'documents' => self::DOCS[$type],
                'apply_url' => 'https://example.org/candidature/'.Str::slug($title),
            ]);
        }

        // Une offre clôturée, pour l'état « M'avertir de la prochaine édition ».
        Opportunity::create([
            'slug' => 'hackathon-ia-et-agriculture-labe',
            'type' => 'Concours', 'kind_label' => 'Hackathon', 'money' => 'Prix',
            'title' => 'Hackathon IA et agriculture, Labé', 'org' => 'Université de Labé', 'place' => 'Labé',
            'level' => 1, 'countries' => ['Guinée'], 'deadline_at' => now()->subDays(2),
            'duration' => '2 jours', 'description' => 'Des équipes imaginent des outils d’IA pour les producteurs du Fouta.',
            'conditions' => ['Équipe de 2 à 4 personnes'], 'documents' => self::DOCS['Concours'],
        ]);
    }
}
