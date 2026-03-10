# Laravel-Spass — Plan Produit (mis à jour 10/03/2026)

## Objectif

Fournir une plateforme mobile et web pour les élus et l'administration du SDEEG, centrée sur l'information institutionnelle, la gestion des événements, la consultation/vote, et la communication personnalisée.

## Contexte technique réel

- Framework: Laravel 12 / PHP 8.4
- Frontend: Tailwind CSS v3 + Alpine.js + FullCalendar
- Tests: PHPUnit 11
- Qualité: Laravel Pint
- Cible: application web responsive (PC Windows) + mobile (Android/iOS)

---

## Changements récents (10/03/2026)

- Dashboard: correction d'alignement des widgets (réunions, actualités, instances, documents). Les 4 widgets sont maintenant enfants directs de la grille CSS 2×2 pour garantir une hauteur égale par rangée.
- UI: réduction des paddings et des espacements (stats cards, en-têtes de widgets, lignes de tables) pour assurer l'affichage entier sur un écran HD.
- Frontend: recompilation des assets Tailwind/Vite (`npm run build`) afin d'inclure les nouvelles classes utilitaires.


## Périmètre fonctionnel complet (8 modules)

### 01 — Authentification
- Login / mot de passe (identifiant reçu par SMS ou à l'accueil du comité syndical)
- Accès web (ordinateur Windows) et mobile (Android Samsung, iOS Apple)
- Rôles : admin / élu

### 02 — Actualités
- Publications au fil de l'eau (actualités brèves, divers sujets)
- Flux d'actualités paginé et triable, consultation pour les élus connectés

### 03 — Événements
- Visualiser les événements et instances (comité syndical, bureaux, commissions)
- Inscription à un événement (si non faite par mail)
- Accès aux documents de la réunion (ordre du jour, rapport, etc.)
- Accès aux badges (ex. Forum des Énergies)
- Donner un pouvoir → notification mail + in-app au porteur de pouvoir

### 04 — Agenda
- Vue calendrier d'ensemble
- 3 types d'événements (légende distincte) :
  - Événements organisés par le SDEEG
  - Événements auxquels le SDEEG participe
  - Événements organisés par les partenaires du SDEEG

### 05 — Consultations
- 3 types de consultations :
  - **Rapport** : vote électronique
  - **PCRS** : répondre à un questionnaire
  - **Avis** : recueillir les avis des élus/agents sur une action du SDEEG

### 06 — Mes Interlocuteurs
- Liste des agents SDEEG dédiés à la commune de l'élu connecté
- Coordonnées (téléphone, email, poste)

### 07 — Commissions
- Accès au compte-rendu de chaque commission
- Accès à la liste des élus de chaque commission

### 08 — Messagerie
- Historique des messages personnels transmis par le SDEEG à l'élu

---

## État d'avancement par module

| Module | Statut |
|---|---|
| 01 Authentification | ✅ En place (rôles admin/élu) |
| 02 Actualités | 🔴 À créer |
| 03 Événements | 🟡 Partiel — inscription, badge, délégation de pouvoir manquants |
| 04 Agenda | 🟡 Partiel — 3 types d'événements non différenciés |
| 05 Consultations | 🔴 À créer |
| 06 Mes Interlocuteurs | 🔴 À créer |
| 07 Commissions | 🟡 Partiel — comptes-rendus et listes membres manquants |
| 08 Messagerie | 🔴 À créer |

---

## Priorités produit révisées

### P0 — Fondations (déjà en place, à fiabiliser)
1. Authentification robuste (login SMS, sessions sécurisées)
2. Événements + Agenda (parcours calendrier stable, 3 types d'événements)
3. Documents liés aux réunions (ordre du jour, rapport)

### P1 — Modules manquants à fort impact
1. **Actualités** — flux de publications
2. **Consultations** — vote électronique + questionnaires + avis
3. **Commissions** — comptes-rendus + listes membres
4. **Pouvoir** — délégation de vote + notification porteur

### P2 — Modules à forte valeur ajoutée UX
1. **Mes Interlocuteurs** — fiches agents dédiés à la commune
2. **Messagerie** — historique des messages SDEEG
3. **Badges** — accès et affichage badge événement

### P3 — Améliorations futures
1. 2FA
2. Versioning documentaire avancé
3. Reporting financier
4. Push notifications mobile

---

## Architecture fonctionnelle cible

```
Application SDEEG
├── Authentification
│   ├── Login (identifiant/mdp reçu par SMS)
│   └── Rôles (admin / élu)
├── Actualités
│   └── Flux publications au fil de l'eau
├── Événements
│   ├── Liste & détail
│   ├── Inscription
│   ├── Documents (ordre du jour, rapport)
│   ├── Badges
│   └── Délégation de pouvoir
├── Agenda
│   ├── Calendrier
│   └── 3 types (SDEEG / Participation / Partenaires)
├── Consultations
│   ├── Vote électronique (Rapport)
│   ├── Questionnaire (PCRS)
│   └── Avis élus/agents
├── Mes Interlocuteurs
│   └── Agents dédiés + coordonnées
├── Commissions
│   ├── Comptes-rendus
│   └── Liste élus par commission
└── Messagerie
    └── Historique messages SDEEG
```

---

## Backlog technique priorisé

- UI Dashboard: alignement des widgets, réduction des espacements, recompilation des assets — ✅ Complété (10/03/2026)

1. **Sécurité**
   - Journal des actions sensibles (vote, pouvoir, inscription)
   - Validation renforcée des uploads (documents réunion, badges)
2. **Nouvelles migrations / modèles**
   - `actualites`, `consultations`, `votes`, `questionnaires`, `interlocuteurs`, `messages`
3. **Notifications**
   - Mail + in-app : réception d'un pouvoir, rappel événement
4. **Performance**
   - Eager loading sur modules Événements et Commissions
   - Caching sur listes Actualités et Interlocuteurs
5. **Qualité**
   - Tests feature sur les 4 nouveaux modules
   - Form Requests pour Consultations et Inscription événement

---

## Plan d'exécution révisé (8 semaines)

### Sprint 1 (S1-S2) — Stabilisation + Actualités
- Fiabiliser Authentification et Événements existants
- Créer module Actualités (modèle, CRUD admin, vue élu)
- Différencier les 3 types d'événements dans l'Agenda

### Sprint 2 (S3-S4) — Événements enrichis + Commissions
- Inscription à un événement
- Délégation de pouvoir + notification mail/in-app
- Module Commissions (comptes-rendus + membres)

### Sprint 3 (S5-S6) — Consultations
- Vote électronique (Rapport)
- Questionnaire PCRS
- Recueil d'avis élus/agents

### Sprint 4 (S7-S8) — Messagerie + Interlocuteurs + Badges
- Module Messagerie (historique messages)
- Module Mes Interlocuteurs (agents dédiés)
- Badges événements
- Tests, performance, pint, polish

---

## Indicateurs de succès

1. Les 8 modules accessibles et fonctionnels
2. 95 % des actions principales en moins de 3 clics
3. Temps de réponse < 2 s sur les écrans clés
4. 0 régression critique sur Événements/Documents/Authentification
5. Couverture de tests > 80 % sur les nouveaux modules
