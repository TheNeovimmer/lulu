# LUMA — Là où commence le soin

Plateforme web de soutien à la maternité, offrant un espace d'information, de suivi et d'échange pour les futures et jeunes mamans.

## Architecture

Application MVC PHP sans framework, construite sur un routeur maison avec architecture modulaire.

```
luma/
├── app/
│   ├── Controllers/     # Contrôleurs (Admin, Expert, CTT, Maman)
│   ├── Core/            # Framework : Router, Database, View, Request, Session
│   ├── Helpers/         # Utilitaires (Avatar)
│   ├── Middleware/      # Auth, Role, Permission middleware
│   └── Repositories/    # Data access layer
├── database/
│   └── seeds/           # Seed scripts (29 tables)
├── migrations/          # Schéma SQL complet
├── public/
│   ├── assets/
│   │   ├── css/         # Styles (dashboard, animations, responsive)
│   │   ├── js/          # JavaScript (app, dashboard)
│   │   └── images/      # Images et icônes
│   └── index.php        # Point d'entrée
├── views/
│   ├── admin/           # Interface d'administration
│   ├── auth/            # Connexion / inscription
│   ├── ctt/             # Centre de traitement et téléassistance
│   ├── dashboard/       # Tableau de bord maman
│   ├── expert/          # Tableau de bord expert
│   ├── layouts/         # Layouts (admin, expert, maman, ctt, front)
│   ├── pages/           # Pages publiques
│   └── partials/        # Partiels réutilisables
├── routes.php           # Définition des routes
└── env.php              # Configuration (DB, URL)
```

## Prérequis

- PHP 8.3+
- MySQL 8
- Serveur web (Apache/Nginx)
- Extension PDO MySQL
- Extension GD (pour les avatars)

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone <url>
   cd luma
   ```

2. **Configurer l'environnement**
   ```bash
   cp env.example.php env.php
   ```
   Éditer `env.php` avec vos paramètres de base de données.

3. **Importer la base de données**
   ```bash
   mysql -u root -p nom_de_la_base < migrations/v2_create_tables.sql
   ```

4. **Seeder la base de données**
   ```bash
   php database/seeds/seed.php
   ```

5. **Configurer le serveur web**
   - Document root : `/public/`
   - Réécriture URL vers `index.php`

### Avec DDEV

```bash
ddev start
ddev exec php database/seeds/seed.php
```

## Comptes de test

| Rôle    | Email             | Mot de passe |
|---------|-------------------|--------------|
| Admin   | admin@luma.tn     | password     |
| Maman   | maman@test.tn     | password     |
| Expert  | expert@test.tn    | password     |
| CTT     | ctt@luma.tn       | password     |

## Fonctionnalités

### Publiques
- Pages d'accueil, blog, ressources, FAQ
- Annuaire des experts
- Communauté (forums)
- Contact et newsletter

### Maman (dashboard)
- Suivi de grossesse (trimestres, semaines)
- Profil bébé (croissance, vaccins)
- Tickets de support
- Rendez-vous avec experts
- Messagerie
- Agenda

### Expert (expert/)
- Gestion des questions communauté
- Rédaction d'articles
- Création de ressources
- Tickets assignés

### CTT (ctt/)
- Gestion des tickets support
- FAQ
- Rapports et historique
- Notifications

### Admin (admin/)
- Gestion des utilisateurs (création, rôles)
- Articles, catégories, ressources
- Modération communauté, commentaires
- Témoignages, FAQ
- Tickets support
- Contacts, newsletter
- Paramètres du site

## Base de données

29 tables couvrant :
- Utilisateurs et rôles (RBAC avec permissions)
- Profils maman, bébé, grossesse
- Croissance, vaccins, étapes clés
- Articles, ressources, catégories
- Communauté (posts, commentaires, likes)
- Tickets et messages
- Rendez-vous, messagerie expert
- Témoignages, FAQ, contacts
- Newsletters, notifications, logs

## Sécurité

- CSRF tokens sur tous les formulaires
- Validation CSRF côté serveur
- Sessions gérées via `App\Core\Session`
- Mots de passe hachés (bcrypt)
- Protection XSS via `htmlspecialchars()` dans les vues
- Middleware d'authentification et de rôles

## Routes principales

| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/` | Accueil |
| GET/POST | `/auth/login` | Connexion |
| GET/POST | `/auth/register` | Inscription |
| GET | `/blog` | Blog |
| GET | `/ressources` | Ressources |
| GET | `/communaute` | Communauté |
| GET | `/experts` | Annuaire experts |
| GET/POST | `/contact` | Contact |
| GET | `/dashboard/*` | Dashboard maman |
| GET/POST | `/expert/*` | Dashboard expert |
| GET/POST | `/ctt/*` | Dashboard CTT |
| GET/POST | `/admin/*` | Administration |

## Développement

### Backend
- Les contrôleurs se trouvent dans `app/Controllers/`
- Les routes sont définies dans `routes.php`
- Le layout est défini par le contrôleur (`admin`, `expert`, `maman`, `ctt`, `front`)

### Frontend
- CSS : `public/assets/css/` (Bootstrap 5 + personnalisations)
- JS : `public/assets/js/` (app.js, dashboard.js)
- Design : thème blanc et rose (`#c94b72`) avec sidebar rose

## Licence

Propriétaire — LUMA Tunisie
