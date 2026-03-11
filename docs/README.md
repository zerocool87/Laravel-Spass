# Documentation — Laravel-Spass

Ce dossier regroupe les points d’entrée documentation du projet.

## Vue d’ensemble

- Documentation principale projet: [features/roadmap.md](features/roadmap.md)
- Guide de démarrage: [../README.md](../README.md)
- Conventions et règles agent/projet: [../CLAUDE.md](../CLAUDE.md)
- Contexte technique complémentaire: [../.mistral-context.md](../.mistral-context.md)

## Organisation recommandée

- `README.md` (racine): onboarding et commandes utiles.
- `docs/features/roadmap.md`: vision produit, état d'avancement, roadmap d'exécution.
- `CLAUDE.md` (racine): conventions obligatoires de développement (Laravel Boost).
- `docs/README.md`: index rapide et navigation.

## État d'avancement (11/03/2026)

Modules opérationnels: Authentification, Actualités, Événements/Agenda (partiel), Instances, Projets, Réunions, Documents, Collab inter-élus.
Modules à créer: Consultations, Mes Interlocuteurs, Messagerie SDEEG→Élu, Badges, Délégation de pouvoir.

## Notes

- Certains `.md` restent volontairement à la racine ou dans des dossiers cachés car ils sont utilisés par les outils de développement.
- Toute nouvelle documentation fonctionnelle peut être ajoutée dans `docs/` (ex: `docs/adr/`, `docs/features/`).
