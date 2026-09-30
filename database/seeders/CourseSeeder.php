<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public const OUTCOMES = [
        'analyser-des-donnees-avec-python' => ['Ouvrir et nettoyer un tableau de données', 'Tracer des graphiques lisibles', 'Présenter une analyse en 5 minutes'],
        'se-proteger-en-ligne-les-bases' => ['Repérer un message d’hameçonnage', 'Protéger tes comptes avec la double authentification', 'Réagir si ton téléphone est volé'],
        'comprendre-les-modeles-de-langage' => ['Expliquer comment un modèle génère du texte', 'Repérer une réponse inventée', 'Formuler une demande précise'],
        'sql-pour-debuter' => ['Lire et filtrer une table', 'Calculer des moyennes par groupe', 'Relier deux tables'],
        'securiser-un-site-web' => ['Corriger les failles les plus courantes', 'Mettre en place des sauvegardes fiables', 'Auditer un site existant'],
        'creer-un-assistant-conversationnel' => ['Concevoir un assistant utile et honnête', 'Le relier à tes propres documents', 'Le tester avec de vrais utilisateurs'],
    ];

    public function run(): void
    {
        $courses = [
            ['analyser-des-donnees-avec-python', 'data', 1, 'Analyser des données avec Python', 'Analyser des données', 'avec Python_', 1229,
                '8 leçons courtes, exercices sur des données guinéennes ouvertes. Téléchargeable pour travailler hors ligne.', [
                    ['Installer Python sur un vieux PC', 'Leçon', 15, 'Une installation légère qui tient sur 4 Go de RAM, et un plan B sur téléphone.'],
                    ['Lire un fichier CSV', 'Leçon', 20, 'Ouvrir un tableau, compter les lignes, repérer les colonnes utiles.'],
                    ['Nettoyer un tableau', 'Exercice', 18, 'Valeurs manquantes, doublons et dates mal écrites.'],
                    ['Premiers graphiques', 'Leçon', 22, 'Barres, courbes : choisir le bon graphique et le rendre lisible.'],
                    ['Lire une corrélation', 'Leçon', 18, 'Ce qu’un lien entre deux chiffres dit, et ce qu’il ne dit pas.'],
                    ['Grouper et résumer', 'Exercice', 17, 'Moyennes par région, par mois, par catégorie.'],
                    ['Cas pratique : prix des denrées à Conakry', 'Projet', 25, 'Une analyse complète sur des données publiques de marché.'],
                    ['Présenter ses résultats', 'Quiz', 15, 'Raconter une analyse en 5 minutes, puis un quiz final.'],
                ]],
            ['se-proteger-en-ligne-les-bases', 'cyber', 1, 'Se protéger en ligne : les bases', 'Se protéger', 'en ligne_', 640,
                'Mots de passe, arnaques au mobile money, double authentification : les réflexes à adopter dès aujourd’hui.', [
                    ['Reconnaître un hameçonnage', 'Leçon', 12, 'Les signes qui trahissent un faux message, sur SMS, WhatsApp ou e-mail.'],
                    ['Un gestionnaire de mots de passe', 'Leçon', 14, 'Un mot de passe différent par compte, sans rien retenir.'],
                    ['Activer la double authentification', 'Exercice', 12, 'Pas à pas, sur tes comptes les plus importants.'],
                    ['Sécuriser son compte WhatsApp', 'Exercice', 10, 'Code PIN, sauvegarde chiffrée, confidentialité des groupes.'],
                    ['Protéger son compte mobile money', 'Leçon', 16, 'Code secret, plafonds et réflexes en cas de vol du téléphone.'],
                    ['Les bons réflexes', 'Quiz', 16, 'Dix situations réelles : que fais-tu ?'],
                ]],
            ['comprendre-les-modeles-de-langage', 'ia', 2, 'Comprendre les modèles de langage', 'Comprendre', 'les LLM_', 820,
                'Comment un modèle comme ChatGPT produit du texte, ce qu’il sait faire, et où il se trompe.', [
                    ['Des mots aux nombres', 'Leçon', 12, 'Comment un texte devient une suite de nombres qu’une machine manipule.'],
                    ['Prédire le mot suivant', 'Leçon', 14, 'Le principe de base de tous les modèles de langage.'],
                    ['Pourquoi l’IA invente', 'Leçon', 10, 'Les « hallucinations » : d’où elles viennent, comment les repérer.'],
                    ['Bien formuler une demande', 'Exercice', 12, 'Écrire un prompt clair, en français, et vérifier la réponse.'],
                    ['Ce qu’il faut retenir', 'Quiz', 12, 'Un quiz pour faire le point.'],
                ]],
            ['sql-pour-debuter', 'data', 1, 'SQL pour débuter, sur un vrai jeu de données', 'SQL', 'pour débuter_', 900,
                'Apprends à interroger une base de données avec des exemples tirés des écoles de Guinée.', [
                    ['Qu’est-ce qu’une base de données ?', 'Leçon', 15, 'Tables, lignes, colonnes : le vocabulaire de base.'],
                    ['Lire une table avec SELECT', 'Leçon', 18, 'Afficher les colonnes utiles, et seulement elles.'],
                    ['Filtrer avec WHERE', 'Exercice', 17, 'Trouver les écoles d’une préfecture.'],
                    ['Trier et limiter', 'Leçon', 15, 'ORDER BY et LIMIT pour les classements.'],
                    ['Compter et résumer', 'Exercice', 20, 'GROUP BY : des moyennes par région.'],
                    ['Joindre deux tables', 'Leçon', 20, 'Relier les écoles et les résultats d’examen.'],
                    ['Projet : les écoles de Guinée', 'Projet', 15, 'Une analyse complète, de la question à la réponse.'],
                ]],
            ['securiser-un-site-web', 'cyber', 3, 'Sécuriser un site web', 'Sécuriser', 'un site web_', 1843,
                'Les failles les plus courantes et comment les corriger, sur un site d’exemple à auditer.', [
                    ['Passer en HTTPS', 'Leçon', 20, 'Certificat gratuit et redirections.'],
                    ['Mettre à jour sans casser', 'Leçon', 20, 'Dépendances, extensions et sauvegardes avant mise à jour.'],
                    ['Protéger les comptes administrateurs', 'Exercice', 20, 'Mots de passe, double authentification, droits minimaux.'],
                    ['Les injections SQL', 'Leçon', 30, 'Comprendre la faille et la corriger avec des requêtes préparées.'],
                    ['Le cross-site scripting', 'Leçon', 25, 'Échapper ce qui s’affiche, toujours.'],
                    ['Des formulaires robustes', 'Exercice', 25, 'Validation côté serveur et protection CSRF.'],
                    ['Des sauvegardes qui marchent', 'Leçon', 20, 'La règle 3-2-1 et le test de restauration.'],
                    ['Lire les journaux', 'Leçon', 20, 'Repérer une tentative d’intrusion.'],
                    ['Projet : auditer un site', 'Projet', 30, 'Un audit complet sur un site d’exemple.'],
                ]],
            ['creer-un-assistant-conversationnel', 'ia', 3, 'Créer un assistant conversationnel en français', 'Créer un', 'assistant_', 2150,
                'De l’idée au prototype : un assistant qui répond aux questions fréquentes d’une association, en français.', [
                    ['Définir le besoin', 'Leçon', 20, 'À quelles questions l’assistant doit-il répondre ?'],
                    ['Rassembler les connaissances', 'Exercice', 25, 'Les documents sources et leur mise en forme.'],
                    ['Choisir un modèle', 'Leçon', 25, 'Modèle hébergé ou modèle local : coûts et limites.'],
                    ['Écrire les consignes', 'Exercice', 20, 'Un prompt système clair et vérifiable.'],
                    ['Chercher dans ses documents', 'Leçon', 25, 'Retrouver le bon passage avant de répondre.'],
                    ['Gérer les erreurs', 'Leçon', 25, 'Quand l’assistant ne sait pas, il le dit.'],
                    ['Une interface sur WhatsApp', 'Exercice', 20, 'Relier l’assistant à une messagerie.'],
                    ['Tester avec de vrais utilisateurs', 'Projet', 30, 'Un protocole de test simple.'],
                    ['Protéger les données', 'Leçon', 25, 'Ce qu’il ne faut jamais envoyer au modèle.'],
                    ['Présenter son prototype', 'Quiz', 25, 'Une démo de 5 minutes et un quiz final.'],
                ]],
        ];

        foreach ($courses as $i => [$slug, $theme, $level, $title, $h1, $h2, $kb, $desc, $lessons]) {
            $course = Course::create([
                'slug' => $slug, 'theme' => $theme, 'level' => $level, 'title' => $title,
                'head1' => $h1, 'head2' => $h2, 'description' => $desc, 'size_kb' => $kb,
                'author' => 'Aïssatou Diallo', 'author_role' => 'data analyst, Conakry', 'position' => $i,
                'outcomes' => self::OUTCOMES[$slug] ?? null,
            ]);
            foreach ($lessons as $n => [$t, $kind, $min, $sum]) {
                $course->lessons()->create([
                    'position' => $n + 1, 'title' => $t, 'kind' => $kind, 'minutes' => $min, 'summary' => $sum,
                ]);
            }
        }
    }
}
