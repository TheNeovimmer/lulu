# LUMA — Là où commence le soin

Plateforme web de soutien à la maternité, offrant un espace d'information, de suivi et d'échange pour les futures et jeunes mamans.

## Architecture

Application MVC PHP sans framework, construite sur un routeur maison avec architecture modulaire.

```
luma/
├── app/
│   ├── autoload.php     # Autoloader PSR-4 natif (aucune dépendance)
│   ├── Controllers/     # Contrôleurs (Admin, Expert, CTT, Maman)
│   ├── Core/            # Framework : Router, Database, View, Request, Session
│   ├── Helpers/         # Utilitaires (Avatar)
│   ├── Middleware/      # Auth, Role, Permission middleware
│   └── Repositories/    # Data access layer
├── database/
│   └── seeds/           # Seed scripts (31 tables)
├── migrations/          # Migrations SQL incrémentales
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
├── database.sql         # Schéma + seed complet (import unique)
├── routes.php           # Définition des routes
└── env.php              # Configuration (DB, URL)
```

## Prérequis

- PHP 8.1+ (aucune dépendance externe — Composer supprimé)
- MySQL 8
- Serveur web (Apache/Nginx)
- Extension PDO MySQL
- Extension GD (pour les avatars)

## Installation sur Laragon (Windows)

**TL;DR — Résumé des étapes :**

1. Installer Laragon → `C:\laragon`
2. Copier le projet → `C:\laragon\www\luma\`
3. Démarrer Apache + MySQL (bouton **Start All**)
4. Créer la DB → `http://localhost/phpmyadmin` → nouvelle base `luma`
5. Importer → sélectionner `luma`, onglet **Importer**, choisir `database.sql`, **Exécuter**
6. Configurer `env.php` à la racine du projet :
   ```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'luma');
   define('DB_USER', 'root');
   define('DB_PASS', '');          // Laragon : pas de mot de passe
   define('BASE_URL', 'http://localhost/luma/public');
   ```
7. Accéder à `http://localhost/luma/public`

**Comptes de test (mot de passe : `password`) :**
| Rôle    | Email          |
|---------|----------------|
| Admin   | admin@luma.tn  |
| Expert  | expert@luma.tn |
| Maman   | maman@luma.tn  |
| CTT     | ctt@luma.tn    |

---

### Étape 1 — Télécharger et installer Laragon

1. Télécharger Laragon sur <https://laragon.org/download/>
2. Lancer l'installateur et choisir un dossier d'installation (ex: `C:\laragon`)
3. À la fin de l'installation, Laragon démarre automatiquement avec Apache et MySQL activés

### Étape 2 — Vérifier que PHP et MySQL fonctionnent

1. Ouvrir Laragon, cliquer sur **MySQL** → **my.ini** pour vérifier que MySQL est actif (bouton **Start All**)
2. Cliquer sur **Menu** → **PHP** pour vérifier la version installée (8.3+ requis)
3. Ouvrir un terminal Windows et taper :
   ```bash
   php -v
   ```
   La version 8.3+ doit s'afficher. Si ce n'est pas le cas, aller dans **Menu > PHP > Version** pour en installer une compatible.

### Étape 3 — Placer le projet dans le dossier web de Laragon

1. Copier le dossier `luma` dans `C:\laragon\www\`
   ```
   C:\laragon\www\luma\
   ```
2. Dans Laragon, cliquer sur **Menu** → **www** pour ouvrir le dossier et vérifier que le projet y est

### Étape 4 — Créer la base de données via phpMyAdmin

1. Dans Laragon, cliquer sur **Database** → **phpMyAdmin** (ou ouvrir `http://localhost/phpmyadmin` dans le navigateur)
2. Cliquer sur l'onglet **Nouvelle base de données** (ou **New** dans le menu de gauche)
3. Nommer la base : `luma`
4. Cliquer sur **Créer**
5. La base de données est créée mais vide — on va la remplir à l'étape suivante

### Étape 5 — Importer la base de données (schéma + données)

Le fichier `database.sql` à la racine du projet contient à la fois le schéma (31 tables) et les données de démonstration.

**Via phpMyAdmin :**

1. Sélectionner la base `luma` dans le menu de gauche
2. Cliquer sur l'onglet **Importer**
3. Cliquer **Choisir un fichier** et sélectionner `C:\laragon\www\luma\database.sql`
4. Cliquer sur **Exécuter** en bas de page
5. Vérifier qu'aucune erreur ne s'affiche (31 tables créées, données insérées)

**Ou via le terminal Laragon :**

1. Cliquer sur **Menu** → **Terminal** dans Laragon
2. Exécuter :
   ```bash
   mysql -u root luma < C:\laragon\www\luma\database.sql
   ```

### Étape 6 — Configurer l'environnement

1. Depuis la racine du projet (`C:\laragon\www\luma\`), copier `env.php` ou créer le fichier avec :
   ```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'luma');
   define('DB_USER', 'root');
   define('DB_PASS', '');          // Laragon : pas de mot de passe par défaut
   define('BASE_URL', 'http://localhost/luma/public');
   ```

### Étape 7 — Configurer Apache (Virtual Host)

**Option A — Via Laragon (recommandé) :**

1. Dans Laragon, aller dans **Menu** → **Apache** → **vhosts.conf**
2. Ajouter :
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/laragon/www/luma/public"
       ServerName luma.test
       <Directory "C:/laragon/www/luma/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
3. Sauvegarder et redémarrer Apache (**Menu** → **Apache** → **Restart**)

**Option B — Accès direct sans Virtual Host :**

Laragon redirige automatiquement vers `http://localhost/luma/public` si le dossier contient un `public/`. Vérifier que le `.htaccess` dans `public/` est bien actif (il contient déjà les règles de réécriture).

### Étape 8 — Accéder au site

1. Ouvrir le navigateur et aller sur :
   ```
   http://localhost/luma/public
   ```
   Ou si vous avez configuré le Virtual Host :
   ```
   http://luma.test
   ```
2. La page d'accueil de LUMA s'affiche

### Étape 9 — Connexion aux comptes de test

| Rôle    | Email             | Mot de passe |
|---------|-------------------|--------------|
| Admin   | admin@luma.tn     | password     |
| Expert  | expert@luma.tn    | password     |
| Maman   | maman@luma.tn     | password     |
| CTT     | ctt@luma.tn       | password     |

## Installation sur XAMPP (Windows)

**TL;DR — Résumé des étapes :**

1. Copier le projet → `C:\xampp\htdocs\luma\`
2. Démarrer Apache + MySQL dans le panneau XAMPP
3. Créer la DB → `http://localhost/phpmyadmin` → nouvelle base `luma`
4. Importer → sélectionner `luma`, onglet **Importer**, choisir `C:\xampp\htdocs\luma\database.sql`, **Exécuter**
5. Vérifier le fichier `C:\xampp\htdocs\luma\env.php` (déjà préconfiguré pour Laragon/XAMPP)
6. Accéder à `http://localhost/luma/public`

### Activer mod_rewrite (indispensable)

1. Ouvrir `C:\xampp\apache\conf\httpd.conf`
2. Enlever le `#` devant cette ligne si elle est commentée :
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Chercher `<Directory "C:/xampp/htdocs">` et remplacer `AllowOverride None` par :
   ```apache
   AllowOverride All
   ```
4. Redémarrer Apache dans le panneau XAMPP

### Comptes de test (mot de passe : `password`)

| Rôle    | Email          |
|---------|----------------|
| Admin   | admin@luma.tn  |
| Expert  | expert@luma.tn |
| Maman   | maman@luma.tn  |
| CTT     | ctt@luma.tn    |

---

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

31 tables couvrant :
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
