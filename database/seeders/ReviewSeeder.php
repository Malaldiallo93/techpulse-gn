<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * File d'attente du back-office : résumés rédigés avec l'aide d'une IA, en attente de relecture.
 * Chaque bloc est une suite de segments ; un segment {f: 'f1'} renvoie à un passage signalé.
 */
class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $drafts = [
            [
                'time' => '8:12', 'theme' => 'ia', 'level' => 1, 'min' => 4, 'rank' => 1,
                'title' => 'Un jeu de données vocal ouvert en pular, maninka et soussou',
                'summary' => '300 heures d’enregistrements libres de droits, publiés par un laboratoire de Conakry.',
                'tags' => ['Afrique', 'Open source'],
                'source' => ['nafalab.org', 'Open speech dataset released for Pular, Maninka and Susu', 'anglais', 'https://nafalab.org/news/open-speech-dataset'],
                'paragraphs' => [
                    ['s1', 'Researchers at the Nafa Language Lab in Conakry have released an open dataset of recorded speech in Pular, Maninka and Susu, three of the most widely spoken languages in Guinea.'],
                    ['s2', 'The collection contains about 300 hours of audio from more than 900 volunteer speakers, recorded in Conakry, Labé and Kankan between 2024 and 2026.'],
                    ['s3', 'It is published under a Creative Commons licence (CC BY 4.0), which allows commercial reuse with attribution.'],
                    ['s4', '“Most voice assistants simply do not understand our languages,” said the lab’s director. “We hope developers will now be able to build tools for farmers, health workers and students.”'],
                    ['s5', 'The team says it is one of the largest open collections for these languages. Transcriptions are available for about 40% of the recordings; the rest will be added over the coming year.'],
                ],
                'blocks' => [
                    ['chapo', 'Chapô', [['t' => 'Un laboratoire de Conakry publie environ 300 heures d’enregistrements libres dans trois langues nationales : '], ['f' => 'f1'], ['t' => '.']]],
                    ['p1', 'Point clé 1', [['t' => 'Environ 300 heures, '], ['f' => 'f2'], ['t' => ', enregistrés à Conakry, Labé et Kankan.']]],
                    ['p2', 'Point clé 2', [['t' => 'Licence CC BY 4.0 : réutilisation commerciale autorisée, en citant la source.']]],
                    ['p3', 'Point clé 3', [['t' => 'Transcriptions disponibles pour environ 40 % des enregistrements, le reste d’ici un an.']]],
                    ['why', 'Pourquoi c’est important pour toi', [['t' => 'Tu peux entraîner un modèle de reconnaissance vocale dans ta langue sans payer de licence. '], ['f' => 'f3']]],
                ],
                'flags' => [
                    ['f1', 'Affirmation absente de la source', 'La source ne compare pas ce corpus à toute l’Afrique de l’Ouest. Elle parle seulement de ces trois langues.', 's5', 'one of the largest open collections for these languages', 'le plus grand corpus vocal jamais publié en Afrique de l’Ouest', 'l’une des plus grandes collections ouvertes pour ces langues'],
                    ['f2', 'Chiffre divergent', 'Le résumé annonce 1 200 locuteurs ; la source dit « plus de 900 ».', 's2', 'more than 900 volunteer speakers', '1 200 locuteurs', 'plus de 900 locuteurs bénévoles'],
                    ['f3', 'Affirmation absente de la source', 'Aucune startup n’est citée. La source exprime seulement un espoir du laboratoire.', 's4', 'We hope developers will now be able to build tools for farmers, health workers and students.', 'Des startups de Conakry l’utilisent déjà pour des assistants agricoles.', 'Le laboratoire espère voir naître des outils pour les agriculteurs, les soignants et les étudiants.'],
                ],
                'body' => [
                    'Le corpus rassemble des voix d’hommes et de femmes de tous âges, enregistrées sur téléphone dans des conditions réelles. Il servira à entraîner des outils de [[apprentissage-automatique|reconnaissance vocale]] adaptés aux langues nationales.',
                ],
            ],
            [
                'time' => '8:40', 'theme' => 'cyber', 'level' => 2, 'min' => 4,
                'title' => 'Rançongiciel : un hôpital régional paralysé deux jours',
                'summary' => 'Les dossiers patients sont restés inaccessibles 48 heures. L’établissement n’a pas payé.',
                'tags' => ['Afrique'],
                'source' => ['securite-sante.org', 'Un hôpital régional victime d’un rançongiciel', 'français', 'https://example.org/hopital-rancongiciel'],
                'paragraphs' => [
                    ['s1', 'Un hôpital régional a vu ses systèmes informatiques bloqués pendant deux jours à la suite d’une attaque par rançongiciel.'],
                    ['s2', 'Les dossiers patients et le système de rendez-vous étaient inaccessibles. Les équipes ont repris le papier pour assurer les urgences.'],
                    ['s3', 'La direction indique ne pas avoir payé la rançon et avoir restauré ses données à partir de sauvegardes réalisées la semaine précédente.'],
                    ['s4', 'L’enquête n’a pas encore établi comment les attaquants sont entrés dans le réseau.'],
                ],
                'blocks' => [
                    ['chapo', 'Chapô', [['t' => 'Un hôpital régional a été paralysé deux jours par un rançongiciel. Il n’a pas payé et a restauré ses données depuis ses sauvegardes.']]],
                    ['p1', 'Point clé 1', [['t' => 'Dossiers patients et rendez-vous inaccessibles pendant 48 heures.']]],
                    ['p2', 'Point clé 2', [['t' => 'Les urgences ont été assurées sur papier.']]],
                    ['p3', 'Point clé 3', [['t' => 'L’attaque '], ['f' => 'f1'], ['t' => '.']]],
                    ['why', 'Pourquoi c’est important pour toi', [['t' => 'Des sauvegardes régulières, c’est ce qui a permis de repartir sans payer. Le réflexe vaut aussi pour ton ordinateur.']]],
                ],
                'flags' => [
                    ['f1', 'Affirmation absente de la source', 'La source précise que le point d’entrée n’est pas encore connu.', 's4', 'L’enquête n’a pas encore établi comment les attaquants sont entrés dans le réseau.', 'a commencé par un e-mail d’hameçonnage envoyé au service comptable', 'n’a pas encore livré son point d’entrée : l’enquête est en cours'],
                ],
            ],
            [
                'time' => '9:05', 'theme' => 'data', 'level' => 1, 'min' => 3,
                'title' => 'La BCEAO publie ses données d’inclusion financière',
                'summary' => 'Comptes bancaires, mobile money, microfinance : les séries sont téléchargeables en CSV.',
                'tags' => ['Afrique', 'Open source'],
                'source' => ['bceao.int', 'Publication des indicateurs d’inclusion financière', 'français', 'https://example.org/bceao-inclusion'],
                'paragraphs' => [
                    ['s1', 'La banque centrale publie pour la première fois ses indicateurs d’inclusion financière dans un format ouvert.'],
                    ['s2', 'Les séries couvrent les comptes bancaires, la monnaie électronique et la microfinance, par pays et par année.'],
                    ['s3', 'Les fichiers sont disponibles au format CSV et mis à jour chaque trimestre.'],
                ],
                'blocks' => [
                    ['chapo', 'Chapô', [['t' => 'La banque centrale ouvre ses indicateurs d’inclusion financière : comptes bancaires, monnaie électronique et microfinance.']]],
                    ['p1', 'Point clé 1', [['t' => 'Des séries par pays et par année.']]],
                    ['p2', 'Point clé 2', [['t' => 'Fichiers CSV mis à jour chaque trimestre.']]],
                    ['p3', 'Point clé 3', [['t' => 'Une première pour ces données.']]],
                    ['why', 'Pourquoi c’est important pour toi', [['t' => 'Un jeu de données fiable et léger pour t’entraîner ou nourrir un mémoire sur le mobile money.']]],
                ],
                'flags' => [],
            ],
            [
                'time' => '9:20', 'theme' => 'opp', 'level' => 2, 'min' => 3,
                'title' => 'Appel à projets : IA pour la santé maternelle',
                'summary' => 'Un fonds finance des prototypes d’IA utiles aux sages-femmes. Candidatures ouvertes.',
                'tags' => ['Afrique'],
                'source' => ['fonds-sante.org', 'Call for proposals: AI for maternal health', 'anglais', 'https://example.org/ai-maternal-health'],
                'paragraphs' => [
                    ['s1', 'The fund invites teams from West Africa to submit prototypes using AI to support midwives and community health workers.'],
                    ['s2', 'Up to ten projects will receive a grant of 15,000 US dollars each for twelve months.'],
                    ['s3', 'Applications close on 15 December. Teams must include at least one health professional.'],
                ],
                'blocks' => [
                    ['chapo', 'Chapô', [['t' => 'Un fonds finance des prototypes d’IA pour aider les sages-femmes et les agents de santé communautaire.']]],
                    ['p1', 'Point clé 1', [['t' => 'Jusqu’à dix projets, '], ['f' => 'f1'], ['t' => '.']]],
                    ['p2', 'Point clé 2', [['t' => 'Candidatures jusqu’au 15 décembre.']]],
                    ['p3', 'Point clé 3', [['f' => 'f2']]],
                    ['why', 'Pourquoi c’est important pour toi', [['t' => 'Si tu développes et connais un soignant, c’est l’occasion de financer un premier prototype.']]],
                ],
                'flags' => [
                    ['f1', 'Chiffre divergent', 'Le résumé annonce 50 000 dollars ; la source dit 15 000 dollars par projet.', 's2', 'a grant of 15,000 US dollars each for twelve months', '50 000 dollars chacun', '15 000 dollars chacun sur douze mois'],
                    ['f2', 'Affirmation absente de la source', 'La source n’impose pas d’être diplômé en informatique : elle exige un professionnel de santé dans l’équipe.', 's3', 'Teams must include at least one health professional.', 'Les candidats doivent être diplômés en informatique.', 'L’équipe doit compter au moins un professionnel de santé.'],
                ],
            ],
        ];

        foreach ($drafts as $d) {
            [$h, $m] = explode(':', $d['time']);
            [$sname, $shead, $slang, $surl] = $d['source'];
            $article = Article::create([
                'slug' => Str::slug($d['title']),
                'status' => 'review',
                'theme' => $d['theme'],
                'level' => $d['level'],
                'suggested_level' => $d['level'],
                'title' => $d['title'],
                'summary' => $d['summary'],
                'tags' => $d['tags'],
                'body' => $d['body'] ?? [],
                'source_name' => $sname,
                'source_title' => $shead,
                'source_headline' => $shead,
                'source_url' => $surl,
                'source_lang' => $slang,
                'source_date' => now()->subDay(),
                'reading_minutes' => $d['min'],
                'daily_rank' => $d['rank'] ?? null,
                'source_paragraphs' => array_map(fn ($p) => ['id' => $p[0], 'text' => $p[1]], $d['paragraphs']),
                'draft_blocks' => array_map(fn ($b) => ['id' => $b[0], 'label' => $b[1], 'segs' => $b[2]], $d['blocks']),
                'draft_edits' => [],
                'ai_drafted_at' => now()->setTime((int) $h, (int) $m),
            ]);
            foreach ($d['flags'] as $i => [$key, $type, $why, $ref, $quote, $orig, $fix]) {
                $article->flags()->create([
                    'key' => $key, 'position' => $i + 1, 'type' => $type, 'why' => $why,
                    'source_ref' => $ref, 'quote' => $quote, 'flagged' => $orig, 'fix' => $fix,
                ]);
            }
            $article->syncTerms();
        }
    }
}
