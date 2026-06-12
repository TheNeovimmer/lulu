# LUMA — Conversion MVC PHP/MySQL Full Stack

**Date :** 2026-06-12
**Contexte :** Conversion de 4 pages statiques PHP (with inline styles) en application full-stack MVC PHP/MySQL avec dashboards, auth, CRUD.

---

## 1. Architecture du projet

```
luma/
├── app/
│   ├── Controllers/       # Contrôleurs (Admin, Auth, Article, Community, Contact, FAQ, Testimonial, User)
│   ├── Models/            # Article, User, Category, Comment, Testimonial, FAQ, Contact, Newsletter, ForumTopic, ForumReply
│   ├── Services/          # AuthService, NewsletterService, UploadService, MailService
│   ├── Repositories/      # ArticleRepository, UserRepository, CategoryRepository, etc.
│   ├── Core/              # Router, Database (PDO singleton), Request, Session, Validator
│   └── Middleware/        # AuthMiddleware, AdminMiddleware, GuestMiddleware
├── config/                # database.php, app.php
├── public/                # index.php (front controller), .htaccess, assets/
│   └── assets/
│       ├── css/           # style.css (charte conservée), responsive.css
│       ├── js/            # app.js
│       └── images/        # logos, icons, uploads
├── views/
│   ├── layouts/           # header.php, footer.php, admin-header.php, admin-footer.php
│   ├── pages/             # home, blog, blog-single, community, community-topic, contact, faq
│   ├── admin/             # dashboard, articles/index, articles/form, categories/index, users/index, comments/index, testimonials/index, faqs/index, contacts/index, newsletters/index, forum/index
│   └── auth/              # login, register, forgot-password, reset-password
├── migrations/            # 001_create_tables.sql
├── .htaccess              # Rewrite to public/
├── env.example.php        # DB config template
└── env.php                # DB config (gitignored)
```

### Routage
- `public/index.php` → inclut `app/Core/Router.php`
- Router parse l'URL, dispatche vers `Controller@action`
- Middleware chain exécuté avant les controllers

### Design System (charte conservée et renforcée)
- **Couleurs principales :** #2E0F1C (bg), #C94B72 (accent rose), #F0A0BB (rose clair), #632538 (surfaces), #70A2B4 (teal accent), #F5F5F5 (text)
- **Polices :** Royalist (headings), Inter (body), Poppins (accents)
- **Styles :** Thème sombre, bordures roses, coins arrondis (10px/20px/50px), glassmorphism (rgba white 0.10)
- **Responsive :** Media queries à 480px, 768px, 1024px, 1440px

---

## 2. Base de Données MySQL

### Tables

```sql
-- users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'member') DEFAULT 'member',
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL
);

-- articles
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- comments
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- testimonials
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- faqs
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) DEFAULT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    display_order INT DEFAULT 0
);

-- contacts
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- newsletters
CREATE TABLE newsletters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- forum_topics
CREATE TABLE forum_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(100) DEFAULT NULL,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- forum_replies
CREATE TABLE forum_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 3. Modules fonctionnels

### 3.1 Authentification
- Inscription avec validation (name, email, password)
- Connexion avec session
- Mot de passe hashé (password_hash/bcrypt)
- Middleware guest/auth/admin
- Routes protégées

### 3.2 Blog / Articles
- CRUD complet dans l'admin
- Upload d'image pour l'article
- Catégorisation
- Statut (brouillon/publié)
- Mise en avant (featured)
- Pagination
- Filtre par catégorie

### 3.3 Commentaires
- Ajout sur les articles (utilisateur connecté)
- Modération (pending/approved/rejected)
- Liste dans l'admin

### 3.4 Communauté / Forum
- Création de sujets par catégorie
- Réponses aux sujets
- Statut ouvert/fermé
- Modération admin

### 3.5 Témoignages
- Soumission par membres connectés
- Note (1-5 étoiles)
- Modération admin
- Affichage sur la page d'accueil

### 3.6 FAQ
- CRUD admin
- Catégories de questions
- Ordre d'affichage personnalisable

### 3.7 Contact
- Formulaire public
- Stockage en base
- Marquage lu/non lu dans l'admin

### 3.8 Newsletter
- Inscription/désinscription
- Export CSV des emails

---

## 4. Pages / Routes

### Pages publiques
| Route | Page | Controller@Method |
|---|---|---|
| `/` | Accueil | `PageController@home` |
| `/blog` | Blog | `ArticleController@index` |
| `/blog/{slug}` | Article détail | `ArticleController@show` |
| `/community` | Forum | `CommunityController@index` |
| `/community/{id}` | Sujet forum | `CommunityController@topic` |
| `/contact` | Contact | `ContactController@index` / `@store` |
| `/faq` | FAQ | `FaqController@index` |
| `/auth/login` | Connexion | `AuthController@login` / `@authenticate` |
| `/auth/register` | Inscription | `AuthController@register` / `@store` |
| `/auth/logout` | Déconnexion | `AuthController@logout` |

### Pages membre (/dashboard)
| Route | Page |
|---|---|
| `/dashboard` | Profil |
| `/dashboard/comments` | Mes commentaires |
| `/dashboard/topics` | Mes sujets forum |
| `/dashboard/testimonials` | Mes témoignages |
| `/dashboard/testimonials/create` | Ajouter témoignage |

### Pages admin (/admin)
| Route | Page |
|---|---|
| `/admin` | Dashboard stats |
| `/admin/articles` | CRUD articles |
| `/admin/categories` | CRUD catégories |
| `/admin/users` | Gestion utilisateurs |
| `/admin/comments` | Modération commentaires |
| `/admin/testimonials` | Modération témoignages |
| `/admin/faqs` | CRUD FAQ |
| `/admin/contacts` | Gestion messages |
| `/admin/newsletters` | Gestion newsletter |
| `/admin/forum` | Modération forum |

---

## 5. Auth & Security

- Password hash avec `password_hash(PASSWORD_BCRYPT)`
- Sessions PHP avec régénération d'ID
- CSRF tokens sur tous les formulaires
- XSS prevention : `htmlspecialchars()` sur toutes les sorties
- SQL injection prevention : PDO prepared statements
- Middleware : vérification du rôle pour les routes admin
- Upload : validation type/taille, stockage dans `public/assets/uploads/`

---

## 6. Responsive Design

- CSS basé sur la charte existante, refactoré en fichiers séparés
- Media queries pour tous les breakpoints
- Navigation mobile (hamburger menu)
- Grid/Flexbox pour les layouts
- Images responsives
- Tableaux scrollables dans l'admin

---

## 7. Implémentation — Ordre de priorité

1. Structure du projet (Core, Router, Database)
2. Auth system (register, login, logout, middleware)
3. Layouts et templates (header/footer, pages front)
4. Accueil (statique → dynamique)
5. Contact (formulaire + admin)
6. Blog (articles, catégories, CRUD admin)
7. Commentaires
8. FAQ (admin + page publique)
9. Témoignages (admin + page accueil)
10. Newsletter
11. Forum / Communauté
12. Dashboard membre
13. Dashboard admin complet
14. Responsive final polish
15. Migration SQL et déploiement
