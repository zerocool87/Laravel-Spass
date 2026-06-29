# Laravel-Spass — Plan Produit (mis à jour 25/06/2026)

## Objectif

Fournir une plateforme mobile et web pour les élus et l'administration , centrée sur l'information institutionnelle, la gestion des événements, la consultation/vote, et la communication personnalisée.

## Contexte technique réel

- Framework: Laravel 12 / PHP 8.4
- Frontend: Tailwind CSS v3 + Alpine.js + FullCalendar
- Tests: PHPUnit 11
- Qualité: Laravel Pint
- Base de données: SQLite (dev), configurable via .env
- Cible: application web responsive (PC Windows) + mobile (Android/iOS)
- Contrôle d'accès: Gates + middleware élu + titres-based scoping

---

## Changements récents (25/06/2026)

- Contrôle d'accès par titres multi-mandats : `titres` (JSON array) ajouté aux documents, réunions ; `fonction` renommé en `titres` sur users ; scoping dans les contrôleurs et traits (FiltersDocuments, Elus\ReunionController).
- Instances simplifiées : passage d'une table complexe (type, description, territory, members) à une simple liste fixe.
- Profil élu étendu (EluProfile) : nouveau modèle avec 25+ champs (code_insee, collectivité, civilité, profession, date_naissance, etc.) lié en hasOne à User.
- Accusés de lecture dans la messagerie : table pivot `message_user` avec `read_at`, compteur de non-lus dans l'en-tête.
- 12 classes Form Request dédiées pour la validation (ActualiteRequest, DocumentRequest, ProjectRequest, etc.).
- `declare(strict_types=1)` ajouté sur l'ensemble des fichiers PHP.
- Refactoring des contrôleurs (séparation Admin/Elus complète, traits Concerns).
- Améliorations responsive mobile (tableaux, formulaires, navigation).
- **Actualités page** — refonte UI complète : titres en orange, pagination intégrée dans l'en-tête "Le Journal du SEHV" avec liens "Articles précédents | Articles suivants", 7 articles par page, modal avec contenu HTML et sauts de ligne préservés (`nl2br`), hauteur adaptative sans colonnes.

## Changements récents (11/03/2026)

- Module Actualités créé (migration, modèle, CRUD admin complet, vue liste + détail pour les élus).
- Messagerie collaborative (Collab) entre élus disponible : conversations, messages, interface dédiée.

## Changements récents (10/03/2026)

- Dashboard: correction d'alignement des widgets (réunions, actualités, instances, documents). Les 4 widgets sont maintenant enfants directs de la grille CSS 2×2 pour garantir une hauteur égale par rangée.
- UI: réduction des paddings et des espacements (stats cards, en-têtes de widgets, lignes de tables) pour assurer l'affichage entier sur un écran HD.
- Frontend: recompilation des assets Tailwind/Vite (`npm run build`) afin d'inclure les nouvelles classes utilitaires.


## Périmètre fonctionnel complet

### 01 — Authentification
- Login / mot de passe (identifiant reçu par SMS ou à l'accueil du comité syndical)
- Accès web (ordinateur Windows) et mobile (Android Samsung, iOS Apple)
- Rôles : admin / élu
- Profil élu étendu (EluProfile : code_insee, collectivité, civilité, profession, date_naissance, etc.)

### 02 — Actualités
- Publications au fil de l'eau (actualités brèves, divers sujets)
- Flux d'actualités paginé et triable, consultation pour les élus connectés
- CRUD admin avec statut brouillon/publié

### 03 — Événements
- Visualiser les événements et instances (comité syndical, bureaux, commissions)
- Inscription à un événement (si non faite par mail)
- Accès aux documents de la réunion (ordre du jour, rapport, etc.)
- Accès aux badges (ex. Forum des Énergies)
- Donner un pouvoir → notification mail + in-app au porteur de pouvoir

### 04 — Agenda
- Vue calendrier d'ensemble (FullCalendar)
- 3 types d'événements (légende distincte) :
  - Événements organisés par le SEHV
  - Événements auxquels le SEHV participe
  - Événements organisés par les partenaires du SEHV
- JSON feed public pour intégration calendrier externe

### 05 — Consultations
- 3 types de consultations :
  - **Rapport** : vote électronique
  - **PCRS** : répondre à un questionnaire
  - **Avis** : recueillir les avis des élus/agents sur une action du SEHV

### 06 — Mes Interlocuteurs
- Liste des agents SEHV dédiés à la commune de l'élu connecté
- Coordonnées (téléphone, email, poste)

### 07 — Commissions / Instances
- Accès au compte-rendu de chaque commission
- Accès à la liste des élus de chaque commission
- Instances simplifiées (liste fixe : Comités, Bureaux, Commissions)
- Réunions avec détection de conflits d'horaires

### 08 — Messagerie Élu
- Historique des messages personnels transmis par le SEHV à l'élu

### 09 — Gestion documentaire
- Upload, consultation, bibliothèque groupée par catégories
- Preview et embed avec support Range headers (streaming partiel)
- Contrôle d'accès par titres et visible_to_all
- Catégorisation automatique (commande artisan)
- Notifications mail à l'upload/partage

### 10 — Projets
- CRUD complet avec GeoJSON
- Filtres par type, statut, territoire, commune
- Indicateurs de suivi, budget

### 11 — Messagerie collaborative (Collab)
- Conversations 1-1 entre élus
- Messages avec accusés de lecture (read_at)
- Compteur de messages non-lus dans l'en-tête

### 12 — Administration avancée
- Dashboard admin avec statistiques globales
- Import CSV massif d'utilisateurs + profils
- Gestion des rôles, mandats (titres), profils étendus

---

## État d'avancement par module

| Module | Statut |
|---|---|
| 01 Authentification | ✅ En place (rôles admin/élu, profil étendu EluProfile) |
| 02 Actualités | ✅ En place — CRUD admin + vue liste/détail élus |
| 03 Événements | 🟡 Partiel — inscription, badge, délégation de pouvoir manquants |
| 04 Agenda | 🟡 Partiel — 3 types d'événements non différenciés |
| 05 Consultations | 🔴 À créer |
| 06 Mes Interlocuteurs | 🔴 À créer |
| 07 Commissions | 🟡 Partiel — Instances simplifiées (liste fixe) ; comptes-rendus et listes membres manquants |
| 08 Messagerie Élu | 🔴 À créer |
| —  Collab inter-élus | ✅ En place — conversations + messages, accusés de lecture |
| —  Contrôle d'accès par titres | ✅ En place — documents, réunions, projets filtrés par mandat/fonction |
| —  Administration avancée | ✅ En place — dashboard admin, import CSV massif, gestion utilisateurs + profils |

---

## Priorités produit révisées

### P0 — Fondations ✅ En place
1. Authentification robuste (login SMS, sessions sécurisées, profils étendus)
2. Événements + Agenda (parcours calendrier stable, 3 types d'événements)
3. Documents liés aux réunions (ordre du jour, rapport, preview, embed)
4. Actualités (flux de publications — CRUD admin + vues élus)
5. Messagerie collaborative inter-élus (Collab — conversations, messages, accusés lecture)
6. Gestion documentaire avancée (catégories, titres, notifications)
7. Projets (CRUD, GeoJSON, filtres)
8. Contrôle d'accès par titres (multi-mandats)
9. Import CSV massif + gestion utilisateurs avancée

### P1 — Modules manquants à fort impact
1. **Consultations** — vote électronique + questionnaires + avis
2. **Commissions** — comptes-rendus + listes membres (Instances en place, simplifiées)
3. **Pouvoir** — délégation de vote + notification porteur

### P2 — Modules à forte valeur ajoutée UX
1. **Mes Interlocuteurs** — fiches agents dédiés à la commune
2. **Messagerie** — historique des messages SEHV → élu
3. **Badges** — accès et affichage badge événement

### P3 — Améliorations futures
1. 2FA (recommandation R4 audit)
2. Policies authorization (recommandation R1 audit)
3. Rate limiting uploads (recommandation R2 audit)
4. Audit logging (recommandation R3 audit)
5. Versioning documentaire avancé
6. Reporting financier
7. Push notifications mobile

---

## Architecture fonctionnelle actuelle

```
Application
├── Authentification
│   ├── Login (identifiant/mdp reçu par SMS)
│   ├── Rôles (admin / élu)
│   └── Profil étendu (EluProfile : 25+ champs)
├── Actualités
│   ├── Flux publications au fil de l'eau
│   ├── CRUD admin (brouillon/publié)
│   └── Vue liste + détail élus
├── Événements
│   ├── Liste & détail
│   ├── Calendrier FullCalendar
│   ├── JSON feed public
│   ├── 3 types (SEHV / Participation / Partenaires)
│   ├── ─ Inscription (à venir)
│   ├── ─ Badges (à venir)
│   └── ─ Délégation de pouvoir (à venir)
├── Gestion documentaire
│   ├── Upload, consultation, bibliothèque
│   ├── Preview & embed (Range headers)
│   ├── Catégories & catégorisation automatique
│   ├── Contrôle d'accès par titres
│   └── Notifications mail
├── Réunions
│   ├── CRUD complet
│   ├── Détection de conflits d'horaires
│   ├── Calendrier FullCalendar
│   └── Contrôle d'accès par titres
├── Instances / Commissions
│   ├── Liste fixe simplifiée
│   └── Réunions associées
│   ├── ─ Comptes-rendus (à venir)
│   └── ─ Liste élus (à venir)
├── Projets
│   ├── CRUD complet
│   ├── GeoJSON
│   └── Filtres (type, statut, territoire)
├── Messagerie
│   ├── Collab inter-élus (conversations, messages, accusés lecture)
│   └── ─ Historique messages SEHV (à venir)
├── Administration
│   ├── Dashboard stats globales
│   ├── Import CSV massif
│   └── Gestion utilisateurs + profils
├── ─ Consultations (à créer)
└── ─ Mes Interlocuteurs (à créer)
```

---

## Backlog technique priorisé

### ✅ Réalisé
- UI Dashboard: alignement des widgets, réduction des espacements — ✅ Complété (10/03/2026)
- Module Actualités: migration, modèle, CRUD admin, vue élus — ✅ Complété (11/03/2026)
- Messagerie collaborative inter-élus (Collab): conversations + messages — ✅ Complété
- Contrôle d'accès par titres (mult-titres documents, réunions, utilisateurs) — ✅ Complété (24-25/06/2026)
- Instances simplifiées (liste fixe, suppression colonnes superflues) — ✅ Complété (24/06/2026)
- Profil élu étendu (EluProfile : 25+ champs) — ✅ Complété (24/06/2026)
- Accusés de lecture messagerie (message_user pivot avec read_at) — ✅ Complété (24/06/2026)
- Form Requests validation (12 classes dédiées) — ✅ Complété
- Import CSV massif utilisateurs — ✅ Complété
- GeoJSON pour projets — ✅ Complété
- Détection de conflits réunions — ✅ Complété
- Documentation audit sécurité (13 findings, 0 critique) — ✅ Complété (14/04/2026)

### 📋 À faire
1. **Sécurité**
   - Journal des actions sensibles (vote, pouvoir, inscription)
   - Validation renforcée des uploads (documents réunion, badges)
2. **Nouvelles migrations / modèles**
   - `consultations`, `votes`, `questionnaires`, `interlocuteurs`
3. **Notifications**
   - Mail + in-app : réception d'un pouvoir, rappel événement
4. **Performance**
   - Eager loading sur modules Événements et Commissions
   - Caching sur listes Actualités et Interlocuteurs
5. **Qualité**
   - Tests feature sur les 4 nouveaux modules
   - Form Requests pour Consultations et Inscription événement

---

## Plan d'exécution révisé (12 semaines)

### Sprint 1 (S1-S2) — Stabilisation + Actualités ✅ Terminé
- ✅ Fiabiliser Authentification et Événements existants
- ✅ Créer module Actualités (modèle, CRUD admin, vue élu)
- 🟡 Différencier les 3 types d'événements dans l'Agenda (à finaliser)

### Sprint 2 (S3-S4) — Événements enrichis + Commissions + Infrastructure ✅ Terminé
- ✅ Messagerie collaborative inter-élus (Collab)
- ✅ Contrôle d'accès par titres multi-mandats
- ✅ Instances simplifiées (liste fixe)
- ✅ Profil élu étendu (EluProfile)
- ✅ Form Requests validation (12 classes)
- ✅ Import CSV utilisateurs
- ✅ Déclare strict_types=1 sur toute la codebase
- ✅ Accusés de lecture messagerie
- 🟡 Inscription événement, délégation de pouvoir (reporté)

### Sprint 3 (S5-S6) — Événements enrichis + Commissions
- Inscription à un événement
- Délégation de pouvoir + notification mail/in-app
- Module Commissions (comptes-rendus + membres)

### Sprint 4 (S7-S8) — Consultations
- Vote électronique (Rapport)
- Questionnaire PCRS
- Recueil d'avis élus/agents

### Sprint 5 (S9-S10) — Messagerie Élu + Interlocuteurs + Badges
- Module Messagerie (historique messages SEHV → élu)
- Module Mes Interlocuteurs (agents dédiés à la commune)
- Badges événements

### Sprint 6 (S11-S12) — Polish & Performance
- Tests feature sur l'ensemble des modules
- Performance (eager loading, caching)
- Responsive mobile finalisation
- Audit sécurité (recommandations R1-R4)
- Pint, cleanup final

---

## Indicateurs de succès

1. Les 12 modules accessibles et fonctionnels
2. 95 % des actions principales en moins de 3 clics
3. Temps de réponse < 2 s sur les écrans clés
4. 0 régression critique sur Événements/Documents/Authentification
5. Couverture de tests > 80 % sur l'ensemble des modules
