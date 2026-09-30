# TechPulse · la tech, en clair, pour toi

Plateforme francophone de veille IA, cybersécurité et data pour les jeunes de Guinée et d’Afrique francophone.
Implémentation du handoff Claude Design (Design System v2 « Journal », 11 écrans mobile et desktop).

- **Stack** : Laravel 13 (PHP 8.3), SQLite par défaut, vues Blade rendues côté serveur.
- **Front** : aucun framework. Une feuille CSS, un petit JS commun et un script par page.
- **Build** : aucun. Les fichiers de `public/` sont servis tels quels, ce qui permet un hébergement PHP mutualisé.
- **PWA** : service worker et manifeste, pour le hors ligne et l'installation sur l'écran d'accueil.

## Démarrer

```bash
git clone https://github.com/Malaldiallo93/techpulse-gn.git && cd techpulse-gn
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed      # contenus de démonstration (fictifs)
php artisan serve               # http://localhost:8000
```

- **Back-office** : `/redaction`. Compte de démonstration : `redaction@techpulse.gn` / `techpulse-redaction`. Il se modifie avec `TECHPULSE_EDITOR_EMAIL` et `TECHPULSE_EDITOR_PASSWORD` avant le seed.
- **Données de démonstration** : les dates (comptes à rebours, « L’essentiel du jour », événements) sont relatives au moment du seed. `php artisan migrate:fresh --seed` les remet à jour.
- **Tests** : `php artisan test`.

## Écrans

| Écran | Route | Notes |
|---|---|---|
| 01 Accueil | `/` | Essentiel du jour (choix de la rédaction via `daily_rank`), opportunités ouvertes aux candidats de Guinée, parcours, TechPulse Brief |
| 02 Actualités | `/actualites` | Filtres thème, niveau et tags côté client ; filtres partageables dans l’URL ; pagination (desktop) ou « Articles plus anciens » (mobile) |
| 03 Article | `/actualites/{slug}` | Glossaire au toucher (`[[slug\|texte]]` dans le corps), source, partage WhatsApp/Telegram, enregistrement hors ligne |
| 04 Opportunités | `/opportunites`, `/opportunites/{slug}` | Liste et détail côte à côte sur desktop ; rappel « 48 h avant » enregistré par appareil |
| 05 Apprendre | `/apprendre`, `/apprendre/{slug}`, `…/lecon-{n}` | Progression par appareil, gardée localement hors ligne puis synchronisée ; téléchargement du parcours complet |
| 06 Glossaire | `/glossaire` (+ `/glossaire.json`) | Recherche instantanée sans accents et en anglais, index A–Z, fiche |
| 07 Écosystème | `/ecosysteme` | Onglets (mobile), filtre ville, « Je participe », propositions de la communauté |
| 08 Recherche | `/recherche` (+ `/recherche/index.json`) | Index compact en cache, correspondance en début de mot, classement par pertinence, recherches récentes |
| 09 À propos | `/a-propos` | Formulaire contributeur validé côté client et côté serveur |
| 10 Hors ligne | `/hors-ligne` | Contenus enregistrés sur le téléphone, espace utilisé, nettoyage des contenus lus |
| 11 Back-office | `/redaction` | File d’attente, source et résumé côte à côte, passages signalés, Publier / Modifier / Rejeter |

## Choix d’implémentation

- **Tutoiement partout**, à la demande. L’encart « Pourquoi c’est important pour toi » tutoyait déjà.
- **Pas de compte lecteur** : un cookie anonyme (`techpulse_device`) rattache à l’appareil les rappels, la progression et les participations.
- **Hors ligne** :
  - Les métadonnées des contenus enregistrés vivent dans `localStorage`, les pages dans le cache `techpulse-saved`.
  - L’essentiel du jour se télécharge une fois par jour, en Wi-Fi uniquement, et le réglage est désactivable.
- **Économie de données** : attribut `data-saver` sur `<html>`. Il est activé par défaut si `navigator.connection.saveData` est vrai, et il masque tout élément `.img`.
- **Mode sombre** : il suit `prefers-color-scheme`, avec un réglage manuel dans le menu.
- **Police** : Archivo variable 400–700, sous-ensemble latin de 35 Ko, hébergée dans `public/fonts`. Aucune requête vers Google.
- **Back-office** :
  - Un résumé est fait de blocs, et chaque bloc de segments. Un segment `{f: 'f1'}` renvoie à un `review_flags`.
  - À la publication, le brouillon est recopié dans `essential`, `key_points` et `why`.
  - Un bloc réécrit à la main compte comme traité (`edited`).
- **Typographie française** : espaces insécables avant « : ; ? ! » et dans les guillemets, appliquées aux titres et aux textes affichés.

## Écarts assumés par rapport aux maquettes

- **Premier article du jour** : l’article n° 1 de la maquette (« jeu de données vocal ») est le brouillon en relecture du back-office. Il apparaît en tête de l’accueil une fois publié depuis `/redaction`.
- **Lien « Opportunités signalées » du back-office** : il est devenu « Propositions de la communauté ». La page liste les candidatures de contributeurs et les suggestions (termes, sujets, événements, startups, communautés).
- **Page de leçon** : les maquettes n’en prévoyaient pas. Une page sobre a été ajoutée ; son contenu détaillé reste à rédiger.
- **Images** : aucune n’est livrée. Toutes les cartes restent complètes sans image ; le champ `image_path` est prêt.

## À faire avant la production

- Mesurer les contrastes (AA) et les poids réels (les cibles des maquettes n’ont pas été mesurées).
- Remplacer les pictogrammes WhatsApp et Telegram par les logos officiels, et renseigner les vrais canaux (`TECHPULSE_WHATSAPP_URL`, `TECHPULSE_TELEGRAM_URL`).
- Brancher l’envoi réel de la newsletter et des rappels d’opportunité (les données sont déjà stockées).
- Écrire le pipeline qui produit les brouillons IA et leurs signalements (le back-office consomme `draft_blocks`, `source_paragraphs` et `review_flags`).
