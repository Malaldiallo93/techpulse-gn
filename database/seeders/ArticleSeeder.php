<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /** Slug lisible : coupé entre deux mots, 60 caractères au plus. */
    public static function slug(string $title): string
    {
        $s = Str::slug($title);

        return strlen($s) <= 60 ? $s : substr($s, 0, strrpos(substr($s, 0, 61), '-'));
    }

    public function run(): void
    {
        $articles = [
            [
                'day' => 0, 'time' => '8:10', 'rank' => 2, 'theme' => 'cyber', 'level' => 1, 'min' => 3,
                'title' => 'Faux SMS de mobile money : comment reconnaître l’arnaque qui circule à Conakry',
                'summary' => 'Le message imite l’opérateur et réclame un code de validation. Trois signes permettent de le repérer.',
                'essential' => 'Un faux SMS de mobile money circule à Conakry. Il annonce un transfert reçu par erreur et demande de renvoyer un code. Ce code sert en réalité à vider ton compte.',
                'points' => [
                    'Aucun agent ne te demandera ton code, ni par SMS ni par téléphone.',
                    'Un transfert « reçu par erreur » est le principal signal d’alerte.',
                    'En cas de doute, appelle le numéro officiel, jamais celui du message.',
                ],
                'why' => 'Le mobile money est souvent ton premier compte. Un seul code transmis peut suffire à le vider, et l’argent perdu est rarement récupéré. Savoir repérer ce message, c’est aussi pouvoir prévenir ta famille.',
                'body' => [
                    'Depuis la mi-septembre, des abonnés reçoivent un SMS annonçant un transfert d’argent reçu « par erreur ». Le message invite à rappeler un numéro ou à transmettre le [[code-a-usage-unique|code à usage unique]] qui arrive juste après. C’est une campagne classique d’[[hameconnage|hameçonnage]] : le code sert en réalité à valider un retrait depuis le compte de la victime.',
                    'L’arnaque repose sur l’[[ingenierie-sociale|ingénierie sociale]] : le ton est pressant, l’expéditeur se présente comme un agent, et la somme évoquée est assez faible pour paraître crédible. Dans plusieurs cas signalés, les escrocs tentent ensuite un [[echange-de-carte-sim|échange de carte SIM]] pour prendre le contrôle du numéro.',
                    'L’opérateur rappelle qu’aucun de ses agents ne demande de code par téléphone ou par SMS, et qu’un transfert reçu par erreur ne peut pas être annulé par le destinataire.',
                ],
                'tags' => ['Mobile money', 'Arnaques'],
                'source' => ['Communiqué de l’opérateur', 'Communiqué de sécurité de l’opérateur', 'en français', 4, 'https://example.org/communique-securite'],
                'reviewer' => 'Mariama Sow',
            ],
            [
                'day' => 0, 'time' => '11:00', 'rank' => 3, 'theme' => 'data', 'level' => 2, 'min' => 5,
                'title' => 'Salaires des data analysts en Afrique de l’Ouest : ce que dit l’enquête régionale',
                'summary' => 'Écarts entre pays, poids du télétravail et compétences les mieux payées : les chiffres clés.',
                'essential' => 'Une enquête menée auprès de 1 800 professionnels de la data dans huit pays d’Afrique de l’Ouest compare les salaires, les contrats et les compétences demandées. Le télétravail pour des employeurs étrangers creuse les écarts.',
                'points' => [
                    'Les profils qui maîtrisent [[sql|SQL]] et un outil de tableau de bord sont les mieux rémunérés en début de carrière.',
                    'Un poste en télétravail pour une entreprise étrangère paie en moyenne deux fois plus qu’un poste local.',
                    'Les femmes représentent moins d’un quart des répondants.',
                ],
                'why' => 'Si tu vises un premier poste en data, l’enquête montre quelles compétences apprendre en priorité et quel salaire demander sans te sous-estimer.',
                'body' => [
                    'L’enquête a été conduite en ligne entre avril et juin auprès d’analystes, de data scientists et d’ingénieurs data. Elle donne des fourchettes par pays et par années d’expérience, plutôt qu’une moyenne unique.',
                    'Les répondants citent [[python|Python]], [[sql|SQL]] et la réalisation de [[tableau-de-bord|tableaux de bord]] comme les trois compétences les plus demandées en entretien. Les certifications comptent moins qu’un projet concret à montrer.',
                ],
                'tags' => ['Emploi', 'Afrique'],
                'source' => ['Réseau régional de la data', 'Enquête 2026 sur les métiers de la data', 'en français', 2, 'https://example.org/enquete-data-2026'],
                'reviewer' => 'Aïssatou Diallo',
            ],
            [
                'day' => 1, 'time' => '9:15', 'rank' => 4, 'theme' => 'ia', 'level' => 2, 'min' => 6,
                'title' => 'Stratégie de l’Union africaine sur l’IA : ce qui change pour les développeurs',
                'summary' => 'Données, compétences, infrastructures : les axes du texte et leur calendrier de mise en œuvre.',
                'essential' => 'L’Union africaine précise le calendrier de sa stratégie continentale sur l’intelligence artificielle. Le texte met l’accent sur les données locales, la formation et le partage d’infrastructures de calcul.',
                'points' => [
                    'Les États sont invités à publier davantage de [[donnees-ouvertes|données ouvertes]] dans les langues nationales.',
                    'Un réseau de centres de calcul partagés doit être ouvert aux chercheurs et aux startups.',
                    'La protection des [[donnees-personnelles|données personnelles]] devient une condition des financements.',
                ],
                'why' => 'Des jeux de données locaux et du calcul partagé, c’est ce qui manque le plus pour créer des outils d’IA adaptés à la Guinée. Les premiers appels à projets arrivent en 2027.',
                'body' => [
                    'Le texte ne crée pas de règle contraignante : chaque pays reste libre de sa législation. Il fixe en revanche des priorités communes et un calendrier de suivi.',
                    'Pour les développeurs, le point le plus concret concerne l’accès au calcul : entraîner un [[modele-de-langage|modèle de langage]] ou un [[reseau-de-neurones|réseau de neurones]] coûte cher, et les centres partagés doivent réduire cette barrière.',
                ],
                'tags' => ['Réglementation', 'Afrique'],
                'source' => ['Union africaine', 'Stratégie continentale sur l’IA, feuille de route', 'en anglais', 3, 'https://example.org/au-ai-strategy'],
                'reviewer' => 'Ibrahima Camara',
            ],
            [
                'day' => 1, 'time' => '10:40', 'rank' => 5, 'theme' => 'cyber', 'level' => 1, 'min' => 4,
                'title' => 'Double authentification : pourquoi une application vaut mieux qu’un SMS',
                'summary' => 'Le SMS peut être détourné par un échange de carte SIM. Une application génère le code sur le téléphone.',
                'essential' => 'Activer l’[[authentification-a-deux-facteurs|authentification à deux facteurs]] protège tes comptes, mais toutes les méthodes ne se valent pas. Un code généré par une application résiste mieux qu’un code reçu par SMS.',
                'points' => [
                    'Le SMS peut être détourné par un [[echange-de-carte-sim|échange de carte SIM]].',
                    'Une application génère le code hors ligne, directement sur ton téléphone.',
                    'Garde les codes de secours sur papier, dans un endroit sûr.',
                ],
                'why' => 'Ton WhatsApp, ton e-mail et ton compte mobile money sont liés à ton numéro. Une seule faille peut tout exposer.',
                'body' => [
                    'La plupart des services proposent aujourd’hui plusieurs méthodes. Le SMS reste mieux que rien, mais il dépend de ta carte SIM et du réseau.',
                    'Une application d’authentification calcule un [[code-a-usage-unique|code à usage unique]] toutes les 30 secondes, sans connexion. C’est aussi plus pratique quand le réseau est faible.',
                ],
                'tags' => ['Outils gratuits'],
                'source' => ['Agence nationale de sécurité numérique', 'Guide des bonnes pratiques, édition 2026', 'en français', 5, 'https://example.org/guide-2fa'],
                'reviewer' => 'Mariama Sow',
            ],
            [
                'day' => 1, 'time' => '14:20', 'theme' => 'data', 'level' => 1, 'min' => 5,
                'title' => 'Open data : où trouver les statistiques officielles guinéennes',
                'summary' => 'Recensement, santé, éducation : les portails publics et internationaux, et les formats à télécharger.',
                'essential' => 'Les statistiques officielles sur la Guinée sont dispersées entre plusieurs portails. Voici où chercher, et quels formats privilégier pour travailler avec une connexion lente.',
                'points' => [
                    'L’institut national de la statistique publie les grands recensements.',
                    'Les portails internationaux proposent souvent les mêmes chiffres en [[donnees-ouvertes|données ouvertes]] au format CSV.',
                    'Télécharge les fichiers CSV plutôt que les PDF : ils sont plus légers et réutilisables.',
                ],
                'why' => 'Pour un exercice, un mémoire ou un projet, des données fiables sur ton pays font toute la différence.',
                'body' => [
                    'Un fichier CSV de quelques centaines de lignes pèse souvent moins de 50 Ko : il se télécharge même en 3G et s’ouvre dans un tableur ou avec [[python|Python]].',
                    'Vérifie toujours la date de collecte et la définition des indicateurs avant de comparer deux sources.',
                ],
                'tags' => ['Afrique', 'Open source'],
                'source' => ['Rédaction TechPulse', 'Recensement des portails de données publiques', 'en français', 1, 'https://example.org/portails-donnees'],
                'reviewer' => 'Aïssatou Diallo',
            ],
            [
                'day' => 1, 'time' => '17:05', 'theme' => 'ia', 'level' => 3, 'min' => 9,
                'title' => 'Faire tourner un petit modèle de langage sur un ordinateur de 8 Go de RAM',
                'summary' => 'Quantification, modèles compacts et réglages : un guide pas à pas, sans carte graphique.',
                'essential' => 'Des modèles de langage compacts fonctionnent désormais sur un ordinateur portable ordinaire. Avec la quantification, 8 Go de RAM suffisent pour un assistant hors ligne.',
                'points' => [
                    'Choisis un modèle de 1 à 3 milliards de paramètres.',
                    'La quantification réduit la taille du modèle au prix d’une petite perte de qualité.',
                    'Tout fonctionne hors ligne une fois le modèle téléchargé.',
                ],
                'why' => 'Un [[modele-de-langage|modèle de langage]] local ne consomme pas de data et garde tes données sur ta machine.',
                'body' => [
                    'Le téléchargement initial reste lourd (1 à 2 Go) : fais-le en Wi-Fi. Ensuite, plus aucune connexion n’est nécessaire.',
                    'Les réponses sont plus lentes qu’avec un service en ligne, et le modèle peut inventer des faits : garde un œil critique.',
                ],
                'tags' => ['Open source', 'Outils gratuits'],
                'source' => ['Communauté open source', 'Guide d’installation des modèles compacts', 'en anglais', 2, 'https://example.org/small-llm-guide'],
                'reviewer' => 'Ibrahima Camara',
            ],
            [
                'day' => 2, 'time' => '8:00', 'theme' => 'cyber', 'level' => 2, 'min' => 6,
                'title' => 'Rançongiciels : les hôpitaux ouest-africains de plus en plus visés',
                'summary' => 'Pourquoi le secteur de la santé attire les attaquants, et les mesures de base qui limitent les dégâts.',
                'essential' => 'Plusieurs établissements de santé de la région ont été paralysés par des [[rancongiciel|rançongiciels]] cette année. Les attaquants savent qu’un hôpital ne peut pas attendre.',
                'points' => [
                    'Les sauvegardes hors ligne restent la meilleure protection.',
                    'La plupart des attaques commencent par un e-mail d’[[hameconnage|hameçonnage]].',
                    'Payer la rançon ne garantit pas la récupération des fichiers.',
                ],
                'why' => 'Si tu travailles ou fais un stage dans une structure de santé, les réflexes de base protègent aussi les patients.',
                'body' => [
                    'Les systèmes informatiques des hôpitaux sont souvent anciens et peu mis à jour, ce qui facilite l’intrusion d’un [[logiciel-malveillant|logiciel malveillant]].',
                    'Un [[pare-feu|pare-feu]] bien configuré et des comptes séparés pour l’administration limitent la propagation.',
                ],
                'tags' => ['Afrique'],
                'source' => ['Observatoire régional de la cybersécurité', 'Rapport semestriel sur les menaces', 'en français', 4, 'https://example.org/rapport-menaces'],
                'reviewer' => 'Mariama Sow',
            ],
            [
                'day' => 2, 'time' => '12:30', 'theme' => 'data', 'level' => 1, 'min' => 4,
                'title' => 'SQL ou Excel : par quoi commencer quand on débute en data ?',
                'summary' => 'Les deux outils ne répondent pas aux mêmes besoins. Notre conseil selon ton objectif.',
                'essential' => 'Le tableur reste l’outil le plus répandu en entreprise, mais [[sql|SQL]] est indispensable dès que les données deviennent volumineuses. Notre conseil : commence par le tableur, enchaîne vite avec SQL.',
                'points' => [
                    'Le tableur est idéal pour explorer un petit fichier et faire un premier graphique.',
                    'SQL permet d’interroger une [[base-de-donnees|base de données]] de millions de lignes.',
                    'Les offres d’emploi en data demandent presque toujours les deux.',
                ],
                'why' => 'Bien choisir ton premier outil t’évite de te décourager, et t’amène plus vite à un premier projet à montrer.',
                'body' => [
                    'Un tableur fonctionne sur téléphone, sans installation : c’est un bon point de départ quand on n’a pas d’ordinateur.',
                    'SQL s’apprend en quelques semaines avec des exercices courts, et ses bases n’ont pas changé depuis des décennies.',
                ],
                'tags' => ['Emploi'],
                'source' => ['Rédaction TechPulse', 'Entretiens avec des recruteurs de Conakry', 'en français', 3, 'https://example.org/sql-ou-excel'],
                'reviewer' => 'Aïssatou Diallo',
            ],
            [
                'day' => 2, 'time' => '15:45', 'theme' => 'ia', 'level' => 2, 'min' => 5,
                'title' => 'Deepfakes audio : comment repérer une fausse voix avant de partager',
                'summary' => 'Les indices à écouter et les outils gratuits de vérification, testés par la rédaction.',
                'essential' => 'Des messages vocaux truqués imitent des personnalités ou des proches. Quelques indices permettent de repérer un [[deepfake|deepfake]] audio avant de le transférer.',
                'points' => [
                    'Une voix trop régulière, sans respiration, doit alerter.',
                    'Méfie-toi d’un message qui presse d’agir ou de payer.',
                    'Rappelle la personne sur son numéro habituel pour vérifier.',
                ],
                'why' => 'Sur WhatsApp, un vocal se partage en un geste. Vérifier avant de transférer, c’est éviter de propager une arnaque.',
                'body' => [
                    'L’[[ia-generative|IA générative]] permet aujourd’hui de cloner une voix à partir de quelques secondes d’enregistrement.',
                    'Ces arnaques combinent souvent la technique et l’[[ingenierie-sociale|ingénierie sociale]] : l’urgence empêche de réfléchir.',
                ],
                'tags' => ['Éthique', 'Arnaques'],
                'source' => ['Collectif de vérification des faits', 'Guide de vérification des contenus audio', 'en français', 4, 'https://example.org/verifier-audio'],
                'reviewer' => 'Ibrahima Camara',
            ],
            [
                'day' => 2, 'time' => '18:10', 'theme' => 'cyber', 'level' => 3, 'min' => 7,
                'title' => 'Chiffrement de bout en bout : ce que WhatsApp protège, et ce qu’il ne protège pas',
                'summary' => 'Contenu des messages, métadonnées, sauvegardes : ce qui reste visible et comment le limiter.',
                'essential' => 'Le [[chiffrement|chiffrement]] de bout en bout protège le contenu de tes messages. Mais les métadonnées et les sauvegardes dans le cloud restent des points faibles.',
                'points' => [
                    'Le contenu des messages n’est lisible que par toi et ton correspondant.',
                    'Qui tu contactes, quand et combien de fois reste visible.',
                    'Les sauvegardes dans le [[cloud|cloud]] doivent être chiffrées séparément.',
                ],
                'why' => 'Tu échanges des documents, des photos et parfois des codes sur WhatsApp : savoir ce qui est protégé t’aide à choisir le bon canal.',
                'body' => [
                    'Les métadonnées suffisent souvent à reconstituer un réseau de relations, même sans lire les messages.',
                    'Active la sauvegarde chiffrée dans les réglages et protège-la par un mot de passe que tu es seul à connaître.',
                ],
                'tags' => ['Vie privée'],
                'source' => ['Documentation technique de la messagerie', 'Livre blanc sur la sécurité', 'en anglais', 6, 'https://example.org/whitepaper'],
                'reviewer' => 'Mariama Sow',
            ],
            [
                'day' => 2, 'time' => '19:30', 'theme' => 'data', 'level' => 1, 'min' => 5,
                'title' => 'Visualiser des données avec des outils gratuits, sans rien installer',
                'summary' => 'Quatre outils en ligne qui fonctionnent sur un téléphone, comparés sur un même jeu de données.',
                'essential' => 'La [[visualisation-de-donnees|visualisation de données]] ne demande plus de logiciel payant. Quatre outils en ligne gratuits permettent de faire un graphique propre depuis un téléphone.',
                'points' => [
                    'Tous acceptent un simple fichier CSV.',
                    'Deux d’entre eux fonctionnent correctement en 3G.',
                    'Exporte en PNG léger pour partager sur WhatsApp.',
                ],
                'why' => 'Un graphique clair rend ton analyse convaincante, pour un exposé, un mémoire ou un entretien.',
                'body' => [
                    'Nous avons testé chaque outil avec les prix du riz sur les marchés de Conakry, un jeu de [[donnees-ouvertes|données ouvertes]] de 300 lignes.',
                    'Le meilleur compromis dépend de ton usage : un [[tableau-de-bord|tableau de bord]] partagé ou un graphique unique.',
                ],
                'tags' => ['Outils gratuits'],
                'source' => ['Rédaction TechPulse', 'Test comparatif de la rédaction', 'en français', 3, 'https://example.org/outils-visualisation'],
                'reviewer' => 'Aïssatou Diallo',
            ],
        ];

        foreach ($articles as $a) {
            [$h, $m] = explode(':', $a['time']);
            $published = now()->subDays($a['day'])->setTime((int) $h, (int) $m);
            [$sname, $stitle, $slang, $sdays, $surl] = $a['source'];

            $article = Article::create([
                'slug' => self::slug($a['title']),
                'status' => 'published',
                'theme' => $a['theme'],
                'level' => $a['level'],
                'title' => $a['title'],
                'summary' => $a['summary'],
                'essential' => $a['essential'],
                'key_points' => $a['points'],
                'why' => $a['why'],
                'body' => $a['body'],
                'tags' => $a['tags'],
                'source_name' => $sname,
                'source_title' => $stitle,
                'source_url' => $surl,
                'source_lang' => $slang,
                'source_date' => $published->copy()->subDays($sdays),
                'reviewer' => $a['reviewer'],
                'reading_minutes' => $a['min'],
                'size_kb' => 10 + $a['min'] * 1,
                'published_at' => $published,
                'daily_rank' => $a['rank'] ?? null,
            ]);
            $article->syncTerms();
        }
    }
}
