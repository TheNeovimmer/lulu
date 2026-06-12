# LUMA v2 — Plateforme Maternité & Bébé (Phase 1)

**Date :** 2026-06-12
**Contexte :** Extension de LUMA (PHP MVC existant) vers une plateforme complète avec 4 rôles, RBAC, Bootstrap 5, 22 tables, et dashboards complets.

---

## 1. Architecture

### Stack
- **Backend :** PHP 8.3+ vanilla MVC (Router, Database/PDO, Session, Request, View, Middleware) — architecture existante conservée et étendue
- **Frontend :** Bootstrap 5 avec thème dark personnalisé (charte LUMA conservée)
- **Base de données :** MySQL 8 / MariaDB 11
- **Sécurité :** RBAC (roles + permissions), CSRF tokens, XSS (htmlspecialchars), SQL injection (PDO prepared statements)

### Évolution de l'architecture existante
- L'architecture MVC vanilla existante est **conservée** et **étendue**
- Le Routing, Database, Session, Request, View, Middleware restent inchangés dans leur principe
- Nouveau : RBAC layer (PermissionMiddleware), Service layer pour la logique métier

---

## 2. Base de Données — 22 Tables

### 2.1 Identity & Access

```sql
-- roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL
);

-- permissions
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    group_name VARCHAR(50) DEFAULT NULL
);

-- role_permissions
CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- users (modifiée)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'suspended', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);
```

### 2.2 Maman & Bébé (nouvelles)

```sql
-- mothers
CREATE TABLE mothers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    date_of_birth DATE DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    pregnancy_week INT DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    child_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- babies
CREATE TABLE babies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mother_id INT NOT NULL,
    name VARCHAR(100) DEFAULT NULL,
    date_of_birth DATE DEFAULT NULL,
    gender ENUM('girl', 'boy', 'other') DEFAULT NULL,
    blood_type VARCHAR(5) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mother_id) REFERENCES mothers(id) ON DELETE CASCADE
);

-- pregnancies
CREATE TABLE pregnancies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mother_id INT NOT NULL,
    start_date DATE DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    week INT DEFAULT NULL,
    trimester INT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('active', 'completed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mother_id) REFERENCES mothers(id) ON DELETE CASCADE
);

-- growth_records
CREATE TABLE growth_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    baby_id INT NOT NULL,
    record_date DATE NOT NULL,
    weight DECIMAL(5,2) DEFAULT NULL,
    height DECIMAL(5,2) DEFAULT NULL,
    head_circumference DECIMAL(5,2) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    FOREIGN KEY (baby_id) REFERENCES babies(id) ON DELETE CASCADE
);

-- vaccinations
CREATE TABLE vaccinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    baby_id INT NOT NULL,
    vaccine_name VARCHAR(255) NOT NULL,
    due_date DATE DEFAULT NULL,
    administered_date DATE DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('pending', 'done', 'missed') DEFAULT 'pending',
    FOREIGN KEY (baby_id) REFERENCES babies(id) ON DELETE CASCADE
);
```

### 2.3 Blog & Ressources

```sql
-- articles (modifiée : ajout tags, views_count, excerpt)
ALTER TABLE articles ADD COLUMN tags VARCHAR(255) DEFAULT NULL AFTER content;
ALTER TABLE articles ADD COLUMN views_count INT DEFAULT 0 AFTER featured;
ALTER TABLE articles ADD COLUMN excerpt TEXT DEFAULT NULL AFTER content;

-- resources (nouvelle)
CREATE TABLE resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    category_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    type ENUM('pdf', 'ebook', 'video', 'guide') NOT NULL,
    description TEXT DEFAULT NULL,
    file_url VARCHAR(255) NOT NULL,
    thumbnail VARCHAR(255) DEFAULT NULL,
    downloads_count INT DEFAULT 0,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);
```

### 2.4 Communauté

```sql
-- community_posts (remplace forum_topics)
CREATE TABLE community_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    likes_count INT DEFAULT 0,
    status ENUM('published', 'hidden', 'reported') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- community_comments (remplace forum_replies + comments combiné)
CREATE TABLE community_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 2.5 Support & Système

```sql
-- tickets
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    assigned_to INT DEFAULT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'closed') DEFAULT 'open',
    category VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) NOT NULL UNIQUE,
    value TEXT DEFAULT NULL,
    group_name VARCHAR(50) DEFAULT NULL
);

-- activity_logs
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2.6 Tables existantes conservées

- `categories` (inchangée)
- `comments` (conservée pour articles, distincte de community_comments)
- `testimonials` (inchangée)
- `faqs` (inchangée)
- `contacts` (inchangée)
- `newsletters` (inchangée)

---

## 3. RBAC — Rôles & Permissions

### Rôles seedés

| Rôle | Slug | Description |
|------|------|-------------|
| Administrateur | admin | Gestion complète de la plateforme |
| Maman | maman | Utilisatrice principale |
| Expert | expert | Professionnel de santé |
| CTT | ctt | Agent du centre de traitement |

### Permissions par groupe

**users :** users.view, users.create, users.edit, users.delete, users.suspend
**articles :** articles.view, articles.create, articles.edit, articles.delete, articles.publish, articles.moderate_comments
**resources :** resources.view, resources.create, resources.edit, resources.delete
**community :** community.view, community.create, community.edit, community.delete, community.moderate
**tickets :** tickets.view, tickets.create, tickets.edit, tickets.assign, tickets.close
**testimonials :** testimonials.view, testimonials.approve, testimonials.reject
**faqs :** faqs.view, faqs.create, faqs.edit, faqs.delete
**experts :** experts.view, experts.validate, experts.manage_certifications
**mothers :** mothers.view, mothers.edit
**notifications :** notifications.send, notifications.manage
**settings :** settings.view, settings.edit

### Seed des permissions par rôle

**admin :** toutes les permissions
**maman :** articles.view, resources.view, community.view, community.create, community.edit (own), tickets.create, tickets.view (own), testimonials.view
**expert :** articles.view, articles.create, articles.edit (own), articles.publish, resources.view, resources.create, resources.edit (own), community.view, community.create, tickets.view
**ctt :** tickets.view, tickets.create, tickets.edit, tickets.assign, tickets.close, faqs.view, faqs.create, faqs.edit, faqs.delete

### Middleware

```php
// PermissionMiddleware vérifie une permission spécifique
PermissionMiddleware::check('articles.create');

// Raccourcis existants améliorés
AuthMiddleware::check();       // Vérifie user connecté
AdminMiddleware::check();      // Vérifie rôle admin
RoleMiddleware::check('expert'); // Vérifie un rôle spécifique
```

---

## 4. Dashboards

### 4.1 Admin Dashboard

**Route :** `/admin`

**Menu sidebar :**
- Dashboard (stats)
- Utilisateurs (CRUD + suspension)
- Mamans (consultation profils)
- Experts (validation, certifications)
- Articles (CRUD + catégories)
- Ressources (CRUD)
- Communauté (modération posts/comments)
- Tickets (liste, affectation)
- FAQ (CRUD)
- Témoignages (approbation)
- Notifications (envoi)
- Paramètres (SMTP, SEO, réseaux sociaux)

### 4.2 Maman Dashboard

**Route :** `/dashboard`

**Menu sidebar :**
- Accueil (stats personnelles, conseils)
- Mon Profil (infos personnelles, avatar)
- Ma Grossesse (suivi semaine par semaine)
- Mon Bébé (infos, historique médical)
- Croissance (courbes taille/poids)
- Vaccination (calendrier, rappels)
- Blog (articles, commentaires, favoris)
- Ressources (téléchargement)
- Communauté (posts, likes, commentaires)
- Questions Experts (poser, consulter)
- Support (tickets)
- Notifications
- Paramètres (sécurité, mot de passe)

### 4.3 Expert Dashboard

**Route :** `/expert/dashboard`

**Menu sidebar :**
- Dashboard (stats consultations, questions, articles)
- Profil Professionnel (diplômes, certifications, expériences)
- Questions Mamans (répondre, historique)
- Articles (CRUD)
- Ressources (ajout/gestion)
- Communauté (participation)
- Notifications
- Paramètres

### 4.4 CTT Dashboard

**Route :** `/ctt/dashboard`

**Menu sidebar :**
- Dashboard (tickets stats)
- Gestion Tickets (liste, répondre, clôturer, réassigner)
- Support Mamans
- Support Experts
- FAQ (CRUD)
- Historique (tickets, interventions)
- Rapports (statistiques, temps résolution)
- Notifications

---

## 5. Site Public

### Pages

| Route | Page | Contrôleur |
|-------|------|------------|
| `/` | Accueil | PageController@home |
| `/a-propos` | À Propos | PageController@about |
| `/blog` | Blog | ArticleController@index |
| `/blog/{slug}` | Article détail | ArticleController@show |
| `/ressources` | Ressources | ResourceController@index |
| `/ressources/{slug}` | Ressource détail | ResourceController@show |
| `/communaute` | Communauté | CommunityController@index |
| `/communaute/{id}` | Post détail | CommunityController@show |
| `/faq` | FAQ | FaqController@index |
| `/contact` | Contact | ContactController@index/store |
| `/auth/login` | Connexion | AuthController@login/authenticate |
| `/auth/register` | Inscription | AuthController@register/store |
| `/auth/logout` | Déconnexion | AuthController@logout |

### Frontend (Bootstrap 5)

- Thème dark via `data-bs-theme="dark"` + CSS personnalisé pour la charte LUMA
- Composants : Navbar Bootstrap, Cards, Tables, Modals, Forms, Badges, Alerts
- Grille responsive : container → row → col-*
- Icônes : Bootstrap Icons
- JavaScript : Bootstrap JS bundle + app.js spécifique

---

## 6. Sécurité

- RBAC : permissions par rôle, middleware de vérification
- CSRF : tokens sur tous les formulaires
- XSS : htmlspecialchars() sur toutes les sorties
- SQL injection : PDO prepared statements
- Password : password_hash(PASSWORD_BCRYPT)
- Sessions : PHP sessions with regeneration
- Upload : validation type MIME + taille

---

## 7. Structure des fichiers (évolutions)

```
app/
├── Core/           (inchangé)
├── Middleware/
│   ├── AuthMiddleware.php     (inchangé)
│   ├── AdminMiddleware.php    (inchangé)
│   ├── GuestMiddleware.php    (inchangé)
│   ├── RoleMiddleware.php     (NOUVEAU)
│   └── PermissionMiddleware.php (NOUVEAU)
├── Controllers/
│   ├── PageController.php     (inchangé)
│   ├── AuthController.php     (modifié : RBAC)
│   ├── ArticleController.php  (inchangé)
│   ├── CommunityController.php (modifié : posts/comments)
│   ├── ContactController.php  (inchangé)
│   ├── FaqController.php      (inchangé)
│   ├── ResourceController.php (NOUVEAU)
│   ├── DashboardController.php (modifié : maman dashboard)
│   ├── ExpertController.php   (NOUVEAU)
│   ├── CttController.php      (NOUVEAU)
│   ├── TicketController.php   (NOUVEAU)
│   ├── NotificationController.php (NOUVEAU)
│   ├── admin/                 (admin controllers)
│   │   ├── AdminController.php
│   │   ├── AdminArticleController.php
│   │   ├── AdminCategoryController.php
│   │   ├── AdminUserController.php
│   │   ├── AdminCommentController.php
│   │   ├── AdminTestimonialController.php
│   │   ├── AdminFaqController.php
│   │   ├── AdminContactController.php
│   │   ├── AdminNewsletterController.php
│   │   ├── AdminResourceController.php (NOUVEAU)
│   │   ├── AdminExpertController.php (NOUVEAU)
│   │   ├── AdminTicketController.php (NOUVEAU)
│   │   └── AdminCommunityController.php (NOUVEAU)
├── Repositories/   (inchangé, étendu)
├── Models/         (inchangé)
└── Services/       (NOUVEAU : TicketService, NotificationService, etc.)

views/
├── layouts/
│   ├── front.php   (modifié : Bootstrap 5)
│   ├── admin.php   (modifié : Bootstrap 5)
│   ├── maman.php   (NOUVEAU)
│   ├── expert.php  (NOUVEAU)
│   └── ctt.php     (NOUVEAU)
├── pages/          (modifiés : Bootstrap 5)
│   ├── home.php, blog.php, blog-single.php, community.php, community-post.php
│   ├── contact.php, faq.php, about.php, resources.php, resource-detail.php
│   └── dashboard/  (maman dashboard)
├── admin/          (modifiés : Bootstrap 5)
├── expert/         (NOUVEAU)
├── ctt/            (NOUVEAU)
├── auth/           (modifiés : Bootstrap 5)
└── errors/         (inchangé)

public/
├── index.php       (inchangé)
├── assets/
│   ├── css/
│   │   ├── style.css       (modifié : Bootstrap + thème dark LUMA)
│   │   ├── admin.css        (modifié)
│   │   └── responsive.css   (supprimé : géré par Bootstrap)
│   ├── js/
│   │   ├── bootstrap.bundle.min.js
│   │   ├── app.js           (modifié)
│   │   └── ajax.js          (NOUVEAU)
│   └── images/     (inchangé)
```

---

## 8. Contenu de ce qui reste pour Phase 1

- ✅ Architecture RBAC complète (roles, permissions, middleware)
- ✅ Migration SQL complète (22 tables + seed)
- ✅ Bootstrap 5 + thème dark LUMA
- ✅ Layouts Bootstrap pour chaque rôle
- ✅ Dashboards : Admin, Maman, Expert, CTT
- ✅ Pages publiques : Accueil, Blog, Ressources, Communauté, FAQ, Contact, À Propos
- ✅ Auth : connexion, inscription (avec choix du rôle)
- ✅ CRUD : articles, catégories, commentaires, ressources, FAQ, témoignages
- ✅ Système de tickets (création + gestion)
- ✅ Notifications
- ✅ Community posts/comments (avec likes)

## 9. Hors Phase 1 (phases futures)

- REST API
- Export PDF des rapports
- Sitemap XML / SEO avancé
- Notifications push temps réel
- Courbes de croissance graphiques avancées
- Calendrier vaccinal interactif
- Paiement / abonnements
- Application mobile
