-- LUMA v2 — Full Platform Schema (22 tables)

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS community_comments;
DROP TABLE IF EXISTS community_likes;
DROP TABLE IF EXISTS community_posts;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS vaccinations;
DROP TABLE IF EXISTS growth_records;
DROP TABLE IF EXISTS babies;
DROP TABLE IF EXISTS pregnancies;
DROP TABLE IF EXISTS mothers;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS testimonials;
DROP TABLE IF EXISTS newsletters;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS faqs;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. ROLES
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. PERMISSIONS
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    group_name VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. ROLE_PERMISSIONS
CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. USERS (modifiée)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. MOTHERS
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BABIES
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. PREGNANCIES
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. GROWTH_RECORDS
CREATE TABLE growth_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    baby_id INT NOT NULL,
    record_date DATE NOT NULL,
    weight DECIMAL(5,2) DEFAULT NULL,
    height DECIMAL(5,2) DEFAULT NULL,
    head_circumference DECIMAL(5,2) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    FOREIGN KEY (baby_id) REFERENCES babies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. VACCINATIONS
CREATE TABLE vaccinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    baby_id INT NOT NULL,
    vaccine_name VARCHAR(255) NOT NULL,
    due_date DATE DEFAULT NULL,
    administered_date DATE DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('pending', 'done', 'missed') DEFAULT 'pending',
    FOREIGN KEY (baby_id) REFERENCES babies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. CATEGORIES (conservée)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. ARTICLES (modifiée)
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt TEXT DEFAULT NULL,
    tags VARCHAR(255) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    featured TINYINT(1) DEFAULT 0,
    views_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. COMMENTS (conservée pour articles)
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. RESOURCES (nouvelle)
CREATE TABLE resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    category_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    type ENUM('pdf', 'ebook', 'video', 'guide') NOT NULL,
    description TEXT DEFAULT NULL,
    file_url VARCHAR(255) NOT NULL,
    thumbnail VARCHAR(255) DEFAULT NULL,
    downloads_count INT DEFAULT 0,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. COMMUNITY_POSTS
CREATE TABLE community_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    likes_count INT DEFAULT 0,
    status ENUM('published', 'hidden', 'reported') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. COMMUNITY_COMMENTS
CREATE TABLE community_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15b. COMMUNITY_LIKES
CREATE TABLE community_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_post_user (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. TESTIMONIALS (conservée)
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. FAQS (conservée)
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) DEFAULT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. CONTACTS (conservée)
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. NEWSLETTERS (conservée)
CREATE TABLE newsletters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. TICKETS (nouvelle)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. NOTIFICATIONS (nouvelle)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. SETTINGS (nouvelle)
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) NOT NULL UNIQUE,
    value TEXT DEFAULT NULL,
    group_name VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23. ACTIVITY_LOGS (nouvelle)
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== SEED DATA =====

-- Roles
INSERT INTO roles (name, slug, description) VALUES
('Administrateur', 'admin', 'Gestion complète de la plateforme'),
('Maman', 'maman', 'Utilisatrice principale de la plateforme'),
('Expert', 'expert', 'Professionnel de santé'),
('CTT', 'ctt', 'Agent du centre de traitement et téléassistance');

-- Permissions
INSERT INTO permissions (name, slug, group_name) VALUES
('Voir les utilisateurs', 'users.view', 'users'),
('Créer des utilisateurs', 'users.create', 'users'),
('Modifier les utilisateurs', 'users.edit', 'users'),
('Supprimer les utilisateurs', 'users.delete', 'users'),
('Suspendre les utilisateurs', 'users.suspend', 'users'),
('Voir les articles', 'articles.view', 'articles'),
('Créer des articles', 'articles.create', 'articles'),
('Modifier les articles', 'articles.edit', 'articles'),
('Supprimer les articles', 'articles.delete', 'articles'),
('Publier des articles', 'articles.publish', 'articles'),
('Modérer les commentaires', 'articles.moderate_comments', 'articles'),
('Voir les ressources', 'resources.view', 'resources'),
('Créer des ressources', 'resources.create', 'resources'),
('Modifier les ressources', 'resources.edit', 'resources'),
('Supprimer les ressources', 'resources.delete', 'resources'),
('Voir la communauté', 'community.view', 'community'),
('Créer des publications', 'community.create', 'community'),
('Modifier ses publications', 'community.edit', 'community'),
('Supprimer les publications', 'community.delete', 'community'),
('Modérer la communauté', 'community.moderate', 'community'),
('Voir les tickets', 'tickets.view', 'tickets'),
('Créer des tickets', 'tickets.create', 'tickets'),
('Modifier les tickets', 'tickets.edit', 'tickets'),
('Assigner les tickets', 'tickets.assign', 'tickets'),
('Fermer les tickets', 'tickets.close', 'tickets'),
('Voir les témoignages', 'testimonials.view', 'testimonials'),
('Approuver les témoignages', 'testimonials.approve', 'testimonials'),
('Rejeter les témoignages', 'testimonials.reject', 'testimonials'),
('Voir la FAQ', 'faqs.view', 'faqs'),
('Créer des FAQ', 'faqs.create', 'faqs'),
('Modifier des FAQ', 'faqs.edit', 'faqs'),
('Supprimer des FAQ', 'faqs.delete', 'faqs'),
('Voir les experts', 'experts.view', 'experts'),
('Valider les experts', 'experts.validate', 'experts'),
('Gérer les certifications', 'experts.manage_certifications', 'experts'),
('Voir les mamans', 'mothers.view', 'mothers'),
('Modifier les profils mamans', 'mothers.edit', 'mothers'),
('Accès administration', 'admin.access', 'admin'),
('Voir les paramètres', 'settings.view', 'settings'),
('Modifier les paramètres', 'settings.edit', 'settings'),
('Envoyer des notifications', 'notifications.send', 'notifications');

-- Admin : toutes les permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p WHERE r.slug = 'admin';

-- Maman
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.slug = 'maman' AND p.slug IN (
    'articles.view', 'resources.view', 'community.view', 'community.create',
    'community.edit', 'tickets.create', 'tickets.view', 'testimonials.view',
    'faqs.view'
);

-- Expert
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.slug = 'expert' AND p.slug IN (
    'articles.view', 'articles.create', 'articles.edit', 'articles.publish',
    'resources.view', 'resources.create', 'resources.edit',
    'community.view', 'community.create',
    'tickets.view', 'testimonials.view', 'faqs.view'
);

-- CTT
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.slug = 'ctt' AND p.slug IN (
    'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.assign', 'tickets.close',
    'faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete',
    'users.view', 'mothers.view', 'experts.view'
);

-- Admin user (password: password)
INSERT INTO users (role_id, name, email, password) 
SELECT r.id, 'Admin LUMA', 'admin@luma.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
FROM roles r WHERE r.slug = 'admin';

-- Categories
INSERT INTO categories (name, slug, description) VALUES
('Grossesse', 'grossesse', 'Articles sur la grossesse'),
('Bébé', 'bebe', 'Soins et développement du bébé'),
('Bien-être', 'bien-etre', 'Bien-être et santé maternelle'),
('Allaitement', 'allaitement', 'Conseils sur l allaitement'),
('Retour d\'expérience', 'retour-experience', 'Témoignages et retours d\'expérience'),
('Organisation', 'organisation', 'Conseils d\'organisation familiale');

-- Default settings
INSERT INTO settings (key_name, value, group_name) VALUES
('site_name', 'LUMA', 'general'),
('site_description', 'Là où commence le soin', 'general'),
('contact_email', 'hello@luma.tn', 'contact'),
('contact_phone', '+216 97 203 908', 'contact');
