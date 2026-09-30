<?php

namespace Database\Seeders;

use App\Models\GlossaryTerm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GlossarySeeder extends Seeder
{
    /** [terme, anglais, thème, définition, exemple, mots-clés de recherche] */
    public const TERMS = [
        ['Algorithme', 'Algorithm', 'ia', 'Suite d’instructions précises qui permet à un ordinateur de résoudre un problème ou d’accomplir une tâche.', 'Le fil d’actualité d’un réseau social est trié par un algorithme.', ''],
        ['API', 'API', 'data', 'Porte d’entrée qui permet à deux logiciels d’échanger des données selon des règles définies.', 'Une appli météo récupère ses prévisions via l’API d’un service spécialisé.', ''],
        ['Apprentissage automatique', 'Machine learning', 'ia', 'Méthode où un programme apprend à partir d’exemples au lieu de suivre des règles écrites à la main.', 'Un filtre anti-spam apprend à reconnaître les messages indésirables.', 'ml'],
        ['Authentification à deux facteurs', 'Two-factor authentication', 'cyber', 'Connexion qui demande deux preuves : un mot de passe et un code reçu ou généré sur le téléphone.', 'Après le mot de passe, l’application demande un code à 6 chiffres.', '2fa double authentification'],
        ['Base de données', 'Database', 'data', 'Ensemble organisé d’informations, stocké pour être consulté et mis à jour facilement.', 'Les contacts d’une application sont rangés dans une base de données.', 'sql'],
        ['Biais algorithmique', 'Algorithmic bias', 'ia', 'Erreur systématique d’un modèle, souvent héritée de données d’entraînement déséquilibrées.', 'Un outil de reconnaissance faciale moins fiable sur les peaux foncées.', ''],
        ['Chiffrement', 'Encryption', 'cyber', 'Transformation d’un message pour qu’il ne soit lisible que par la personne qui détient la clé.', 'Les messages WhatsApp sont chiffrés de bout en bout.', ''],
        ['Cloud', 'Cloud computing', 'data', 'Utilisation de serveurs distants, via Internet, pour stocker des fichiers ou faire tourner des programmes.', 'Tes photos sauvegardées en ligne sont dans le cloud.', ''],
        ['Code à usage unique', 'One-time password (OTP)', 'cyber', 'Code temporaire envoyé par SMS ou généré par une application pour confirmer une opération. Il ne doit jamais être communiqué, même à un agent.', 'Le code reçu pour valider un retrait mobile money.', 'otp code sms mobile money'],
        ['Deepfake', 'Deepfake', 'ia', 'Image, vidéo ou voix truquée par IA pour prêter à quelqu’un des paroles ou des actes qu’il n’a jamais eus.', 'Une fausse vidéo d’une personnalité qui circule avant une élection.', 'hypertrucage'],
        ['Données ouvertes', 'Open data', 'data', 'Données publiées librement, que chacun peut télécharger, réutiliser et partager.', 'Les résultats d’un recensement mis en ligne au format CSV.', 'statistiques'],
        ['Données personnelles', 'Personal data', 'cyber', 'Toute information qui permet d’identifier une personne : nom, numéro, photo, localisation.', 'Ton numéro de téléphone est une donnée personnelle.', 'vie privee'],
        ['Échange de carte SIM', 'SIM swap', 'cyber', 'Fraude où l’escroc obtient un duplicata de ta carte SIM pour recevoir tes SMS et tes codes à ta place.', 'Ton téléphone perd soudain le réseau et tes codes arrivent ailleurs.', ''],
        ['Hameçonnage', 'Phishing', 'cyber', 'Arnaque qui imite un service connu (banque, opérateur, administration) pour obtenir un mot de passe, un code ou de l’argent.', 'Un faux SMS de ton opérateur qui te demande un code.', 'arnaque sms faux message'],
        ['IA générative', 'Generative AI', 'ia', 'IA capable de produire du texte, des images, du son ou du code à partir d’une consigne.', 'Demander à un assistant de rédiger une lettre de motivation.', 'chatgpt texte image'],
        ['Ingénierie sociale', 'Social engineering', 'cyber', 'Manipulation qui exploite la confiance, l’urgence ou la peur plutôt qu’une faille technique. La plupart des arnaques en ligne commencent ainsi.', 'Un faux agent qui te presse au téléphone.', ''],
        ['Logiciel malveillant', 'Malware', 'cyber', 'Programme conçu pour nuire : voler des données, espionner ou bloquer un appareil.', 'Une fausse application de lampe torche qui lit tes SMS.', 'virus'],
        ['Modèle de langage', 'Large language model (LLM)', 'ia', 'IA entraînée sur d’immenses quantités de texte pour prédire et générer des phrases.', 'Les assistants conversationnels reposent sur un modèle de langage.', 'chatgpt'],
        ['Pare-feu', 'Firewall', 'cyber', 'Filtre qui contrôle ce qui entre et sort d’un réseau ou d’un appareil.', 'Le pare-feu bloque une connexion suspecte vers ton ordinateur.', ''],
        ['Prompt', 'Prompt', 'ia', 'Consigne écrite donnée à une IA générative pour obtenir un résultat.', '« Résume cet article en trois points simples. »', 'consigne'],
        ['Python', 'Python', 'data', 'Langage de programmation simple à lire, très utilisé en data, en IA et pour automatiser des tâches.', 'Un script Python qui calcule la moyenne des prix du riz par marché.', 'langage programmation code'],
        ['Rançongiciel', 'Ransomware', 'cyber', 'Logiciel malveillant qui bloque des fichiers et exige une rançon pour les rendre.', 'Un hôpital privé de ses dossiers patients jusqu’au paiement.', ''],
        ['Réseau de neurones', 'Neural network', 'ia', 'Modèle mathématique inspiré du cerveau, fait de couches qui apprennent à reconnaître des motifs.', 'Reconnaître un chiffre écrit à la main sur une photo.', ''],
        ['SQL', 'SQL', 'data', 'Langage pour interroger une base de données : chercher, trier, compter.', 'Lister les clients de Conakry inscrits ce mois-ci.', 'base de donnees requete'],
        ['Tableau de bord', 'Dashboard', 'data', 'Écran qui rassemble les indicateurs clés sous forme de chiffres et de graphiques.', 'Le suivi quotidien des ventes d’une boutique.', ''],
        ['Visualisation de données', 'Data visualization', 'data', 'Représentation graphique de données pour les rendre compréhensibles d’un coup d’œil.', 'Une carte des prix du riz par préfecture.', 'graphique'],
        ['VPN', 'Virtual private network', 'cyber', 'Tunnel chiffré qui protège ta connexion, notamment sur un Wi-Fi public.', 'Se connecter au Wi-Fi d’un café sans exposer ses données.', ''],
    ];

    public function run(): void
    {
        foreach (self::TERMS as [$term, $en, $theme, $def, $ex, $kw]) {
            GlossaryTerm::create([
                'slug' => Str::slug($term),
                'term' => $term,
                'english' => $en,
                'theme' => $theme,
                'definition' => $def,
                'example' => $ex,
                'keywords' => $kw,
            ]);
        }
    }
}
