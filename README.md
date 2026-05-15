# AideChain — Coordination Humanitaire pour le Tchad

  > MIABE Hackathon 2026 · ODD 1 · ODD 2 · ODD 16

  ## Contexte

  Au Tchad, 70+ organisations humanitaires distribuent ~400 millions USD d'aide par an
  dans des systèmes totalement isolés. Résultat : 20 à 35 % des bénéficiaires reçoivent
  des doublons d'aide (OCHA, 2022), pendant que des zones entières restent sans assistance.

  AideChain est une plateforme web + mobile qui connecte ces organisations autour d'un
  registre partagé, avec détection automatique des doublons et cartographie des zones
  non couvertes.

  ## Fonctionnalités

  ### Interface ONG (Représentant)
  - Enregistrement de l'organisation et gestion des agents terrain
  - Création de projets d'aide (type, zone cible, date d'expiration)
  - Tableau de bord : bénéficiaires, distributions, projets actifs

  ### Interface Agent terrain (Web + Mobile)
  - Enregistrement rapide des bénéficiaires (nom, genre, localisation)
  - Détection de doublon en temps réel avant toute distribution
  - Distribution d'aide liée à un projet — immutable une fois enregistrée

  ### Tableau de bord OCHA (Coordination)
  - Vue consolidée multi-ONG
  - Couverture géographique par zone
  - Identification des zones non couvertes (rouge)
  - Historique complet des distributions

  ### Application mobile (Flutter)
  - Mode hors-ligne : distributions sauvegardées localement
  - Synchronisation automatique à la reconnexion
  - Détection de doublon cross-ONG avant distribution

  ## Stack technique

  | Couche | Technologie |
  |--------|-------------|
  | Backend | Laravel 13 · PHP 8.3 |
  | Frontend web | Livewire 4 · Flux UI · Tailwind CSS v4 |
  | Auth web | Laravel Fortify |
  | Auth API | Laravel Sanctum |
  | Base de données | MySQL 8 |
  | Application mobile | Flutter 3.41 · Dart 3.11 |
  | HTTP mobile | Dio · SharedPreferences · SQLite |

  ## Sécurité & Intégrité

  - **Privacy by design** — les données des bénéficiaires sont hashées (identity_hash)
  - **Détection de doublon atomique** — vérifiée avant tout enregistrement
  - **Immutabilité des aides** — une distribution ne peut être ni modifiée ni supprimée
  - **Isolation multi-ONG** — chaque ONG n'accède qu'à ses propres données
  - **Audit trail** — chaque action est tracée (ONG, timestamp, type)

  ## Rôles utilisateurs

  | Rôle | Accès |
  |------|-------|
  | `super_admin` | Gestion globale, toutes les ONG |
  | `ong_representant` | Gestion de son ONG, projets, agents |
  | `ong_agent` | Enregistrement bénéficiaires + distributions |
  | Coordinateur OCHA | Dashboard lecture seule cross-ONG |

  ## Lancement rapide

  ```bash
  cp .env.example .env
  composer install
  php artisan key:generate
  php artisan migrate --seed
  php artisan serve

  Compte de démonstration : admin@aidechain.td / password

  ---
  Projet réalisé dans le cadre du MIABE Hackathon 2026 — Édition MBH 2026.
