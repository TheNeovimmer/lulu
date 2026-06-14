-- =========================================================================
-- LUMA — Full Database Schema + Seed Data
-- =========================================================================
-- Import via phpMyAdmin: select "luma" DB → Importer → choose this file
-- Or via CLI: mysql -u root luma < database.sql
-- =========================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ── DROP ALL TABLES ──────────────────────────────────────────────────────

DROP TABLE IF EXISTS expert_unavailable_dates;
DROP TABLE IF EXISTS expert_availability;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS ticket_messages;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS expert_messages;
DROP TABLE IF EXISTS baby_milestones;
DROP TABLE IF EXISTS baby_memories;
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
DROP TABLE IF EXISTS forum_replies;
DROP TABLE IF EXISTS forum_topics;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================================
-- SCHEMA
-- =========================================================================

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

-- 4. USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'suspended', 'banned') DEFAULT 'active',
    specialty VARCHAR(100) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
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

-- 10. CATEGORIES
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. ARTICLES
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

-- 12. COMMENTS
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

-- 13. RESOURCES
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
    title VARCHAR(255) NOT NULL,
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

-- 16. COMMUNITY_LIKES
CREATE TABLE community_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_post_user (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. TESTIMONIALS
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. FAQS
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) DEFAULT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. CONTACTS
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. NEWSLETTERS
CREATE TABLE newsletters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. TICKETS
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

-- 22. TICKET_MESSAGES
CREATE TABLE ticket_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23. NOTIFICATIONS
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

-- 24. SETTINGS
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) NOT NULL UNIQUE,
    value TEXT DEFAULT NULL,
    group_name VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 25. ACTIVITY_LOGS
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 26. APPOINTMENTS
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mother_id INT NOT NULL,
    expert_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    notes TEXT DEFAULT NULL,
    type ENUM('online', 'in_person') DEFAULT 'online',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mother_id) REFERENCES mothers(id) ON DELETE CASCADE,
    FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 27. EXPERT_MESSAGES
CREATE TABLE expert_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 28. BABY_MEMORIES
CREATE TABLE baby_memories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    baby_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    event_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (baby_id) REFERENCES babies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 29. BABY_MILESTONES
CREATE TABLE baby_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    baby_id INT NOT NULL,
    milestone_key VARCHAR(100) NOT NULL,
    achieved_date DATE DEFAULT NULL,
    FOREIGN KEY (baby_id) REFERENCES babies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_baby_milestone (baby_id, milestone_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 30. EXPERT_AVAILABILITY
CREATE TABLE expert_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expert_id INT NOT NULL,
    day_of_week TINYINT NOT NULL COMMENT '0=Lundi, 6=Dimanche',
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_expert_day (expert_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 31. EXPERT_UNAVAILABLE_DATES
CREATE TABLE expert_unavailable_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expert_id INT NOT NULL,
    unavailable_date DATE NOT NULL,
    reason VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_expert_date (expert_id, unavailable_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- SEED DATA
-- =========================================================================

-- ── Roles ────────────────────────────────────────────────────────────────

INSERT INTO roles (name, slug, description) VALUES
('Administrateur', 'admin', 'Gestion complète de la plateforme'),
('Maman', 'maman', 'Utilisatrice principale de la plateforme'),
('Expert', 'expert', 'Professionnel de santé'),
('CTT', 'ctt', 'Agent du centre de traitement et téléassistance');

-- ── Permissions ─────────────────────────────────────────────────────────

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

-- ── Role-Permissions (Admin = all) ──────────────────────────────────────

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p WHERE r.slug = 'admin';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.slug = 'maman' AND p.slug IN (
    'articles.view', 'resources.view', 'community.view', 'community.create',
    'community.edit', 'tickets.create', 'tickets.view', 'testimonials.view',
    'faqs.view'
);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.slug = 'expert' AND p.slug IN (
    'articles.view', 'articles.create', 'articles.edit', 'articles.publish',
    'resources.view', 'resources.create', 'resources.edit',
    'community.view', 'community.create',
    'tickets.view', 'testimonials.view', 'faqs.view'
);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.slug = 'ctt' AND p.slug IN (
    'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.assign', 'tickets.close',
    'faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete',
    'users.view', 'mothers.view', 'experts.view'
);

-- ── Users ───────────────────────────────────────────────────────────────

-- Password hash for "password" (bcrypt)
INSERT INTO users (role_id, name, email, password, phone, status, specialty, bio) VALUES
(1, 'Admin LUMA', 'admin@luma.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 97 203 908', 'active', NULL, 'Administrateur système'),
(3, 'Dr. Amira Ben Ali', 'expert@luma.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 20 123 456', 'active', 'Gynécologue obstétricien', 'Spécialiste en suivi de grossesse et accouchement'),
(2, 'Leila Trabelsi', 'maman@luma.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 55 678 901', 'active', NULL, 'Future maman de 28 semaines'),
(4, 'Nour El Houda', 'ctt@luma.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 22 334 455', 'active', NULL, 'Agent CTT');

-- ── Mothers ─────────────────────────────────────────────────────────────

INSERT INTO mothers (user_id, date_of_birth, due_date, pregnancy_week, city, child_count) VALUES
(3, '1995-03-15', '2026-12-20', 28, 'Sfax', 1);

-- ── Pregnancies ─────────────────────────────────────────────────────────

INSERT INTO pregnancies (mother_id, start_date, due_date, week, trimester, status) VALUES
(1, '2026-04-20', '2026-12-20', 28, 3, 'active');

-- ── Babies ──────────────────────────────────────────────────────────────

INSERT INTO babies (mother_id, name, date_of_birth, gender, blood_type, notes) VALUES
(1, 'Youssef', '2026-06-10', 'boy', 'O+', 'Bébé en bonne santé');

-- ── Growth Records ──────────────────────────────────────────────────────

INSERT INTO growth_records (baby_id, record_date, weight, height, head_circumference) VALUES
(1, '2026-06-10', 3.50, 50.0, 35.0),
(1, '2026-06-17', 3.60, 50.5, 35.2);

-- ── Vaccinations ────────────────────────────────────────────────────────

INSERT INTO vaccinations (baby_id, vaccine_name, due_date, administered_date, status) VALUES
(1, 'BCG', '2026-06-10', '2026-06-10', 'done'),
(1, 'Hépatite B', '2026-06-10', '2026-06-10', 'done'),
(1, 'DTaP-Hib-IPV', '2026-08-10', NULL, 'pending');

-- ── Categories ──────────────────────────────────────────────────────────

INSERT INTO categories (name, slug, description) VALUES
('Grossesse', 'grossesse', 'Tout sur la grossesse et le suivi médical'),
('Bébé', 'bebe', 'Soins, développement et santé du bébé'),
('Bien-être', 'bien-etre', 'Bien-être et santé maternelle'),
('Allaitement', 'allaitement', 'Conseils et astuces pour l\'allaitement'),
('Retour d\'expérience', 'retour-experience', 'Témoignages et partages d\'expérience'),
('Organisation', 'organisation', 'Conseils d\'organisation familiale'),
('Nutrition', 'nutrition', 'Alimentation et nutrition'),
('Santé', 'sante', 'Santé et prévention');

-- ── Articles ────────────────────────────────────────────────────────────

INSERT INTO articles (category_id, user_id, title, slug, content, status, featured, created_at) VALUES
(1, 1, 'Les premiers signes de grossesse', 'les-premiers-signes-de-grossesse', 'Découvrez les premiers signes qui annoncent une grossesse. Nausées matinales, fatigue, retard de règles... Apprenez à les reconnaître et à les distinguer d\'autres conditions.', 'published', 1, NOW()),
(1, 1, 'Préparer l\'arrivée de bébé', 'preparer-arrivee-bebe', 'Guide complet pour préparer l\'arrivée de votre bébé : liste de naissance, préparation de la chambre, choix du pédiatre et organisation du congé maternité.', 'published', 1, NOW()),
(2, 1, 'L\'allaitement maternel : guide complet', 'allaitement-maternel-guide', 'Tout ce que vous devez savoir sur l\'allaitement maternel : positions, fréquence, conservation du lait, et résolution des problèmes courants.', 'published', 1, NOW()),
(1, 1, 'Le suivi médical pendant la grossesse', 'suivi-medical-grossesse', 'Les étapes clés du suivi médical pendant la grossesse : échographies, analyses, consultations obligatoires et examens recommandés.', 'published', 1, NOW()),
(1, 1, 'Les bienfaits du yoga prénatal', 'bienfaits-yoga-prenatal', 'Le yoga prénatal aide à préparer le corps et l\'esprit à l\'accouchement. Découvrez ses nombreux bienfaits et les postures adaptées.', 'published', 0, NOW());

-- ── Comments ────────────────────────────────────────────────────────────

INSERT INTO comments (article_id, user_id, content, status, created_at) VALUES
(1, 3, 'Merci pour cet article très instructif !', 'approved', NOW()),
(1, 3, 'J\'ai une question complémentaire...', 'pending', NOW());

-- ── Resources ───────────────────────────────────────────────────────────

INSERT INTO resources (user_id, category_id, title, slug, type, description, file_url, status, created_at) VALUES
(2, 1, 'Guide de la grossesse', 'guide-grossesse', 'pdf', 'Guide complet pour les futures mamans', '/uploads/resources/guide-grossesse.pdf', 'published', NOW()),
(2, 1, 'Carnet de vaccination', 'carnet-vaccination', 'pdf', 'Calendrier vaccinal du nourrisson', '/uploads/resources/carnet-vaccination.pdf', 'published', NOW()),
(2, 1, 'Exercices de relaxation', 'exercices-relaxation', 'video', 'Séance guidée de relaxation prénatale', '/uploads/resources/exercices-relaxation.mp4', 'published', NOW());

-- ── Community Posts ─────────────────────────────────────────────────────

INSERT INTO community_posts (user_id, title, content, status, created_at) VALUES
(3, 'Bienvenue sur LUMA', 'Bonjour à toutes ! Je suis nouvelle sur la plateforme et ravie de rejoindre cette communauté.', 'published', NOW()),
(3, 'Cours de préparation à l\'accouchement', 'Qui a déjà testé le cours de préparation à l\'accouchement ? Des recommandations ?', 'published', NOW()),
(3, 'Bébé ne dort pas la nuit', 'Mon bébé ne dort pas la nuit, des conseils ?', 'published', NOW());

-- ── Community Comments ──────────────────────────────────────────────────

INSERT INTO community_comments (post_id, user_id, content, created_at) VALUES
(1, 2, 'Bienvenue ! N\'hésitez pas à poser toutes vos questions.', NOW());

-- ── Community Likes ─────────────────────────────────────────────────────

INSERT INTO community_likes (post_id, user_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4);

-- ── Testimonials ────────────────────────────────────────────────────────

INSERT INTO testimonials (user_id, content, rating, status, created_at) VALUES
(3, 'LUMA m\'a accompagnée tout au long de ma grossesse. Une plateforme exceptionnelle avec des experts à l\'écoute.', 5, 'approved', NOW()),
(3, 'Les ressources sont très utiles pour les jeunes mamans comme moi.', 4, 'pending', NOW());

-- ── FAQs ────────────────────────────────────────────────────────────────

INSERT INTO faqs (category, question, answer, display_order) VALUES
('Inscription', 'Comment créer un compte ?', 'Rendez-vous sur la page d\'accueil et cliquez sur "Mon compte" puis "S\'inscrire". Remplissez le formulaire avec vos informations.', 1),
('Inscription', 'Puis-je changer mon rôle après inscription ?', 'Contactez notre équipe via la page Contact pour modifier votre profil.', 2),
('Grossesse', 'À partir de quand puis-je suivre ma grossesse ?', 'Dès que vous avez confirmé votre grossesse, vous pouvez créer votre profil et commencer le suivi.', 3),
('Grossesse', 'Comment sont calculées les semaines de grossesse ?', 'Les semaines sont calculées à partir de votre date de dernières règles (DDR).', 4),
('Compte', 'Comment modifier mes informations personnelles ?', 'Connectez-vous à votre tableau de bord et accédez à la section "Mon profil".', 5),
('Compte', 'Comment supprimer mon compte ?', 'Contactez l\'administrateur via la page Contact pour demander la suppression de votre compte.', 6);

-- ── Contacts ────────────────────────────────────────────────────────────

INSERT INTO contacts (name, email, subject, message, is_read, created_at) VALUES
('Salma Ben Ali', 'salma@example.com', 'Question sur le suivi', 'Bonjour, je souhaiterais avoir plus d\'informations sur le suivi de grossesse. Merci !', 0, NOW()),
('Ahmed Mansour', 'ahmed@example.com', 'Demande de partenariat', 'Nous souhaitons proposer nos services à la plateforme LUMA.', 0, NOW());

-- ── Newsletters ─────────────────────────────────────────────────────────

INSERT INTO newsletters (email, is_active, created_at) VALUES
('maman@luma.tn', 1, NOW()),
('expert@luma.tn', 1, NOW()),
('newsletter@example.com', 1, NOW());

-- ── Tickets ─────────────────────────────────────────────────────────────

INSERT INTO tickets (user_id, assigned_to, subject, message, priority, status, created_at) VALUES
(3, NULL, 'Problème de connexion', 'Je n\'arrive pas à me connecter à mon compte depuis hier.', 'medium', 'open', NOW()),
(3, 2, 'Question sur le suivi', 'Pouvez-vous m\'aider avec le suivi de grossesse ?', 'high', 'in_progress', NOW());

-- ── Ticket Messages ─────────────────────────────────────────────────────

INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) VALUES
(1, 3, 'J\'ai essayé de réinitialiser mon mot de passe mais je ne reçois pas l\'email.', NOW());

-- ── Notifications ───────────────────────────────────────────────────────

INSERT INTO notifications (user_id, type, title, message, created_at) VALUES
(1, 'welcome', 'Bienvenue sur LUMA', 'Merci de vous être inscrit sur LUMA. Explorez nos ressources et notre communauté.', NOW()),
(2, 'welcome', 'Bienvenue sur LUMA', 'Merci de vous être inscrit sur LUMA. Explorez nos ressources et notre communauté.', NOW()),
(3, 'welcome', 'Bienvenue sur LUMA', 'Merci de vous être inscrit sur LUMA. Explorez nos ressources et notre communauté.', NOW()),
(4, 'welcome', 'Bienvenue sur LUMA', 'Merci de vous être inscrit sur LUMA. Explorez nos ressources et notre communauté.', NOW());

-- ── Settings ────────────────────────────────────────────────────────────

INSERT INTO settings (key_name, value, group_name) VALUES
('site_name', 'LUMA', 'general'),
('site_description', 'Là où commence le soin', 'general'),
('contact_email', 'contact@luma.tn', 'general'),
('contact_phone', '+216 00 000 000', 'general'),
('address', 'Tunis, Tunisie', 'general'),
('social_facebook', 'https://facebook.com/luma', 'social'),
('social_instagram', 'https://instagram.com/luma', 'social'),
('site_logo', '/assets/images/home/logo.svg', 'general');

-- ── Appointments ────────────────────────────────────────────────────────

INSERT INTO appointments (mother_id, expert_id, appointment_date, status, type, notes, created_at) VALUES
(1, 2, '2026-06-16 10:00:00', 'confirmed', 'online', 'Consultation prénatale de routine', NOW()),
(1, 2, '2026-06-21 14:30:00', 'pending', 'in_person', 'Échographie de contrôle', NOW());

-- ── Expert Messages ─────────────────────────────────────────────────────

INSERT INTO expert_messages (sender_id, receiver_id, message, is_read, created_at) VALUES
(2, 1, 'Bonjour, je suis votre expert dédié. N\'hésitez pas à me poser vos questions !', 1, NOW());

-- ── Baby Memories ───────────────────────────────────────────────────────

INSERT INTO baby_memories (baby_id, title, content, event_date, created_at) VALUES
(1, 'Premier sourire', 'Youssef a fait son premier sourire aujourd\'hui ! Un moment magique.', '2026-06-25', NOW()),
(1, 'Premier bain', 'Son premier bain à la maison. Il a adoré l\'eau !', '2026-06-12', NOW());

-- ── Baby Milestones ─────────────────────────────────────────────────────

INSERT INTO baby_milestones (baby_id, milestone_key, achieved_date) VALUES
(1, 'first_smile', '2026-06-25'),
(1, 'first_bath', '2026-06-12'),
(1, 'first_word', NULL);
