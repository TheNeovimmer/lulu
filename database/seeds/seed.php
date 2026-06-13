<?php
/**
 * LUMA Database Seeder
 * Run: php database/seeds/seed.php
 */

require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

echo "Seeding database...\n";

// ── Roles (skip if already seeded) ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM roles");
if ($existing['c'] == 0) {
    $db->insert("INSERT INTO roles (name, slug, description) VALUES ('Administrateur', 'admin', 'Accès complet à la plateforme')");
    $db->insert("INSERT INTO roles (name, slug, description) VALUES ('Maman', 'maman', 'Future maman ou jeune maman')");
    $db->insert("INSERT INTO roles (name, slug, description) VALUES ('Expert', 'expert', 'Professionnel de santé')");
    $db->insert("INSERT INTO roles (name, slug, description) VALUES ('CTT', 'ctt', 'Agent du centre d\'appel')");
    echo "  roles ✓\n";
}

// ── Settings (skip if already seeded) ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM settings");
if ($existing['c'] == 0) {
    $settings = [
        ['site_name', 'LUMA', 'general'],
        ['site_description', 'Là où commence le soin', 'general'],
        ['contact_email', 'contact@luma.tn', 'general'],
        ['contact_phone', '+216 00 000 000', 'general'],
        ['address', 'Tunis, Tunisie', 'general'],
        ['social_facebook', 'https://facebook.com/luma', 'social'],
        ['social_instagram', 'https://instagram.com/luma', 'social'],
        ['site_logo', '/assets/images/home/logo.svg', 'general'],
    ];
    foreach ($settings as $s) {
        $db->insert("INSERT INTO settings (key_name, value, group_name) VALUES (?, ?, ?)", $s);
    }
    echo "  settings ✓\n";
}

// ── Categories (skip if already seeded) ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM categories");
if ($existing['c'] == 0) {
    $categories = [
        ['Grossesse', 'grossesse', 'Tout sur la grossesse et le suivi médical'],
        ['Bébé', 'bebe', 'Soins, développement et santé du bébé'],
        ['Bien-être', 'bien-etre', 'Bien-être et santé maternelle'],
        ['Allaitement', 'allaitement', 'Conseils et astuces pour l\'allaitement'],
        ['Retour d\'expérience', 'retour-experience', 'Témoignages et partages d\'expérience'],
        ['Organisation', 'organisation', 'Conseils d\'organisation familiale'],
        ['Nutrition', 'nutrition', 'Alimentation et nutrition'],
        ['Santé', 'sante', 'Santé et prévention'],
    ];
    foreach ($categories as $c) {
        $db->insert("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)", $c);
    }
    echo "  categories ✓\n";
}

// ── Users (skip if already seeded) ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM users");
if ($existing['c'] == 0) {
    $adminRole = $db->fetch("SELECT id FROM roles WHERE slug = 'admin'")['id'];
    $mamanRole = $db->fetch("SELECT id FROM roles WHERE slug = 'maman'")['id'];
    $expertRole = $db->fetch("SELECT id FROM roles WHERE slug = 'expert'")['id'];
    $cttRole = $db->fetch("SELECT id FROM roles WHERE slug = 'ctt'")['id'];

    $users = [
        ['role_id' => $adminRole, 'name' => 'Admin LUMA', 'email' => 'admin@luma.tn', 'password' => password_hash('password', PASSWORD_DEFAULT), 'phone' => '+216 97 203 908', 'status' => 'active', 'bio' => 'Administrateur système'],
        ['role_id' => $expertRole, 'name' => 'Dr. Amira Ben Ali', 'email' => 'expert@luma.tn', 'password' => password_hash('password', PASSWORD_DEFAULT), 'phone' => '+216 20 123 456', 'status' => 'active', 'specialty' => 'Gynécologue obstétricien', 'bio' => 'Spécialiste en suivi de grossesse et accouchement'],
        ['role_id' => $mamanRole, 'name' => 'Leila Trabelsi', 'email' => 'maman@luma.tn', 'password' => password_hash('password', PASSWORD_DEFAULT), 'phone' => '+216 55 678 901', 'status' => 'active', 'bio' => 'Future maman de 28 semaines'],
        ['role_id' => $cttRole, 'name' => 'Nour El Houda', 'email' => 'ctt@luma.tn', 'password' => password_hash('password', PASSWORD_DEFAULT), 'phone' => '+216 22 334 455', 'status' => 'active', 'bio' => 'Agent CTT'],
    ];
    foreach ($users as $u) {
        $db->insert("INSERT INTO users (role_id, name, email, password, phone, status, specialty, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$u['role_id'], $u['name'], $u['email'], $u['password'], $u['phone'] ?? null, $u['status'], $u['specialty'] ?? null, $u['bio'] ?? null]);
    }
    echo "  users ✓\n";
}

// ── Mothers ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM mothers");
if ($existing['c'] == 0) {
    $mamaUser = $db->fetch("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'maman' LIMIT 1");
    if ($mamaUser) {
        $db->insert("INSERT INTO mothers (user_id, date_of_birth, due_date, pregnancy_week, city, child_count) VALUES (?, ?, ?, ?, ?, ?)",
            [$mamaUser['id'], '1995-03-15', '2026-12-20', 28, 'Sfax', 1]);
        echo "  mothers ✓\n";
    }
}

// ── Pregnancies ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM pregnancies");
if ($existing['c'] == 0) {
    $mother = $db->fetch("SELECT id FROM mothers LIMIT 1");
    if ($mother) {
        $db->insert("INSERT INTO pregnancies (mother_id, start_date, due_date, week, trimester, status) VALUES (?, ?, ?, ?, ?, ?)",
            [$mother['id'], '2026-04-20', '2026-12-20', 28, 3, 'active']);
        echo "  pregnancies ✓\n";
    }
}

// ── Babies ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM babies");
if ($existing['c'] == 0) {
    $mother = $db->fetch("SELECT id FROM mothers LIMIT 1");
    if ($mother) {
        $db->insert("INSERT INTO babies (mother_id, name, date_of_birth, gender, blood_type, notes) VALUES (?, ?, ?, ?, ?, ?)",
            [$mother['id'], 'Youssef', '2026-06-10', 'boy', 'O+', 'Bébé en bonne santé']);
        echo "  babies ✓\n";
    }
}

// ── Growth Records ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM growth_records");
if ($existing['c'] == 0) {
    $baby = $db->fetch("SELECT id FROM babies LIMIT 1");
    if ($baby) {
        $db->insert("INSERT INTO growth_records (baby_id, record_date, weight, height, head_circumference) VALUES (?, ?, ?, ?, ?)",
            [$baby['id'], '2026-06-10', 3.5, 50, 35]);
        $db->insert("INSERT INTO growth_records (baby_id, record_date, weight, height, head_circumference) VALUES (?, ?, ?, ?, ?)",
            [$baby['id'], '2026-06-17', 3.6, 50.5, 35.2]);
        echo "  growth_records ✓\n";
    }
}

// ── Vaccinations ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM vaccinations");
if ($existing['c'] == 0) {
    $baby = $db->fetch("SELECT id FROM babies LIMIT 1");
    if ($baby) {
        $db->insert("INSERT INTO vaccinations (baby_id, vaccine_name, due_date, administered_date, status) VALUES (?, ?, ?, ?, ?)",
            [$baby['id'], 'BCG', '2026-06-10', '2026-06-10', 'done']);
        $db->insert("INSERT INTO vaccinations (baby_id, vaccine_name, due_date, administered_date, status) VALUES (?, ?, ?, ?, ?)",
            [$baby['id'], 'Hépatite B', '2026-06-10', '2026-06-10', 'done']);
        $db->insert("INSERT INTO vaccinations (baby_id, vaccine_name, due_date, status) VALUES (?, ?, ?, ?)",
            [$baby['id'], 'DTaP-Hib-IPV', '2026-08-10', 'pending']);
        echo "  vaccinations ✓\n";
    }
}

// ── Articles ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM articles");
if ($existing['c'] == 0) {
    $cat = $db->fetch("SELECT id FROM categories ORDER BY id LIMIT 1");
    $author = $db->fetch("SELECT id FROM users ORDER BY id LIMIT 1");
    if ($cat && $author) {
        $articles = [
            ['Les premiers signes de grossesse', 'les-premiers-signes-de-grossesse', 'Découvrez les premiers signes qui annoncent une grossesse. Nausées matinales, fatigue, retard de règles... Apprenez à les reconnaître et à les distinguer d\'autres conditions.', true],
            ['Préparer l\'arrivée de bébé', 'preparer-arrivee-bebe', 'Guide complet pour préparer l\'arrivée de votre bébé : liste de naissance, préparation de la chambre, choix du pédiatre et organisation du congé maternité.', true],
            ['L\'allaitement maternel : guide complet', 'allaitement-maternel-guide', 'Tout ce que vous devez savoir sur l\'allaitement maternel : positions, fréquence, conservation du lait, et résolution des problèmes courants.', true],
            ['Le suivi médical pendant la grossesse', 'suivi-medical-grossesse', 'Les étapes clés du suivi médical pendant la grossesse : échographies, analyses, consultations obligatoires et examens recommandés.', true],
            ['Les bienfaits du yoga prénatal', 'bienfaits-yoga-prenatal', 'Le yoga prénatal aide à préparer le corps et l\'esprit à l\'accouchement. Découvrez ses nombreux bienfaits et les postures adaptées.', false],
        ];
        foreach ($articles as $a) {
            $db->insert("INSERT INTO articles (category_id, user_id, title, slug, content, status, featured, created_at) VALUES (?, ?, ?, ?, ?, 'published', ?, NOW())",
                [$cat['id'], $author['id'], $a[0], $a[1], $a[2], $a[3] ? 1 : 0]);
        }
        echo "  articles ✓\n";
    }
}

// ── Comments ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM comments");
if ($existing['c'] == 0) {
    $article = $db->fetch("SELECT id FROM articles LIMIT 1");
    $user = $db->fetch("SELECT id FROM users WHERE id > 1 LIMIT 1");
    if ($article && $user) {
        $db->insert("INSERT INTO comments (article_id, user_id, content, status, created_at) VALUES (?, ?, ?, 'approved', NOW())",
            [$article['id'], $user['id'], 'Merci pour cet article très instructif !']);
        $db->insert("INSERT INTO comments (article_id, user_id, content, status, created_at) VALUES (?, ?, ?, 'pending', NOW())",
            [$article['id'], $user['id'], 'J\'ai une question complémentaire...']);
        echo "  comments ✓\n";
    }
}

// ── Resources ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM resources");
if ($existing['c'] == 0) {
    $cat = $db->fetch("SELECT id FROM categories ORDER BY id LIMIT 1");
    $expert = $db->fetch("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'expert' LIMIT 1");
    if ($cat && $expert) {
        $resources = [
            ['Guide de la grossesse', 'guide-grossesse', 'Guide complet pour les futures mamans', 'pdf'],
            ['Carnet de vaccination', 'carnet-vaccination', 'Calendrier vaccinal du nourrisson', 'pdf'],
            ['Exercices de relaxation', 'exercices-relaxation', 'Séance guidée de relaxation prénatale', 'video'],
        ];
        foreach ($resources as $r) {
            $db->insert("INSERT INTO resources (user_id, category_id, title, slug, type, description, file_url, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'https://example.com/ressource', 'published', NOW())",
                [$expert['id'], $cat['id'], $r[0], $r[1], $r[3], $r[2]]);
        }
        echo "  resources ✓\n";
    }
}

// ── Community Posts ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM community_posts");
if ($existing['c'] == 0) {
    $user = $db->fetch("SELECT id FROM users WHERE id > 1 LIMIT 1");
    if ($user) {
        $posts = [
            ['Bonjour à toutes ! Je suis nouvelle sur la plateforme et ravie de rejoindre cette communauté.', 'published'],
            ['Qui a déjà testé le cours de préparation à l\'accouchement ? Des recommandations ?', 'published'],
            ['Mon bébé ne dort pas la nuit, des conseils ?', 'published'],
        ];
        foreach ($posts as $p) {
            $db->insert("INSERT INTO community_posts (user_id, content, status, created_at) VALUES (?, ?, ?, NOW())",
                [$user['id'], $p[0], $p[1]]);
        }
        echo "  community_posts ✓\n";
    }
}

// ── Community Comments ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM community_comments");
if ($existing['c'] == 0) {
    $post = $db->fetch("SELECT id FROM community_posts LIMIT 1");
    $expert = $db->fetch("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'expert' LIMIT 1");
    if ($post && $expert) {
        $db->insert("INSERT INTO community_comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())",
            [$post['id'], $expert['id'], 'Bienvenue ! N\'hésitez pas à poser toutes vos questions.']);
        echo "  community_comments ✓\n";
    }
}

// ── Community Likes ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM community_likes");
if ($existing['c'] == 0) {
    $post = $db->fetch("SELECT id FROM community_posts LIMIT 1");
    $users = $db->fetchAll("SELECT id FROM users");
    if ($post && $users) {
        foreach ($users as $u) {
            $db->insert("INSERT INTO community_likes (post_id, user_id) VALUES (?, ?)", [$post['id'], $u['id']]);
        }
        echo "  community_likes ✓\n";
    }
}

// ── Testimonials ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM testimonials");
if ($existing['c'] == 0) {
    $user = $db->fetch("SELECT id FROM users WHERE id > 1 LIMIT 1");
    if ($user) {
        $db->insert("INSERT INTO testimonials (user_id, content, rating, status, created_at) VALUES (?, ?, ?, 'approved', NOW())",
            [$user['id'], 'LUMA m\'a accompagnée tout au long de ma grossesse. Une plateforme exceptionnelle avec des experts à l\'écoute.', 5]);
        $db->insert("INSERT INTO testimonials (user_id, content, rating, status, created_at) VALUES (?, ?, ?, 'pending', NOW())",
            [$user['id'], 'Les ressources sont très utiles pour les jeunes mamans comme moi.', 4]);
        echo "  testimonials ✓\n";
    }
}

// ── FAQs ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM faqs");
if ($existing['c'] == 0) {
    $faqs = [
        ['Inscription', 'Comment créer un compte ?', 'Rendez-vous sur la page d\'accueil et cliquez sur "Mon compte" puis "S\'inscrire". Remplissez le formulaire avec vos informations.', 1],
        ['Inscription', 'Puis-je changer mon rôle après inscription ?', 'Contactez notre équipe via la page Contact pour modifier votre profil.', 2],
        ['Grossesse', 'À partir de quand puis-je suivre ma grossesse ?', 'Dès que vous avez confirmé votre grossesse, vous pouvez créer votre profil et commencer le suivi.', 3],
        ['Grossesse', 'Comment sont calculées les semaines de grossesse ?', 'Les semaines sont calculées à partir de votre date de dernières règles (DDR).', 4],
        ['Compte', 'Comment modifier mes informations personnelles ?', 'Connectez-vous à votre tableau de bord et accédez à la section "Mon profil".', 5],
        ['Compte', 'Comment supprimer mon compte ?', 'Contactez l\'administrateur via la page Contact pour demander la suppression de votre compte.', 6],
    ];
    foreach ($faqs as $f) {
        $db->insert("INSERT INTO faqs (category, question, answer, display_order) VALUES (?, ?, ?, ?)", $f);
    }
    echo "  faqs ✓\n";
}

// ── Contacts ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM contacts");
if ($existing['c'] == 0) {
    $db->insert("INSERT INTO contacts (name, email, subject, message, is_read, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
        ['Salma Ben Ali', 'salma@example.com', 'Question sur le suivi', 'Bonjour, je souhaiterais avoir plus d\'informations sur le suivi de grossesse. Merci !', 0]);
    $db->insert("INSERT INTO contacts (name, email, subject, message, is_read, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
        ['Ahmed Mansour', 'ahmed@example.com', 'Demande de partenariat', 'Nous souhaitons proposer nos services à la plateforme LUMA.', 0]);
    echo "  contacts ✓\n";
}

// ── Newsletters ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM newsletters");
if ($existing['c'] == 0) {
    $db->insert("INSERT INTO newsletters (email, is_active, created_at) VALUES (?, ?, NOW())", ['maman@luma.tn', 1]);
    $db->insert("INSERT INTO newsletters (email, is_active, created_at) VALUES (?, ?, NOW())", ['expert@luma.tn', 1]);
    $db->insert("INSERT INTO newsletters (email, is_active, created_at) VALUES (?, ?, NOW())", ['subscriber@example.com', 1]);
    echo "  newsletters ✓\n";
}

// ── Tickets ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM tickets");
if ($existing['c'] == 0) {
    $user = $db->fetch("SELECT id FROM users WHERE id > 1 LIMIT 1");
    $expert = $db->fetch("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'expert' LIMIT 1");
    if ($user) {
        $db->insert("INSERT INTO tickets (user_id, assigned_to, subject, message, priority, status, created_at) VALUES (?, ?, ?, ?, ?, 'open', NOW())",
            [$user['id'], null, 'Problème de connexion', 'Je n\'arrive pas à me connecter à mon compte depuis hier.', 'medium']);
        if ($expert) {
            $db->insert("INSERT INTO tickets (user_id, assigned_to, subject, message, priority, status, created_at) VALUES (?, ?, ?, ?, ?, 'in_progress', NOW())",
                [$user['id'], $expert['id'], 'Question sur le suivi', 'Pouvez-vous m\'aider avec le suivi de grossesse ?', 'high']);
        }
        echo "  tickets ✓\n";
    }
}

// ── Ticket Messages ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM ticket_messages");
if ($existing['c'] == 0) {
    $ticket = $db->fetch("SELECT id, user_id FROM tickets WHERE status = 'open' LIMIT 1");
    if ($ticket) {
        $db->insert("INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())",
            [$ticket['id'], $ticket['user_id'], 'J\'ai essayé de réinitialiser mon mot de passe mais je ne reçois pas l\'email.']);
        echo "  ticket_messages ✓\n";
    }
}

// ── Notifications ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM notifications");
if ($existing['c'] == 0) {
    $users = $db->fetchAll("SELECT id FROM users");
    if ($users) {
        foreach ($users as $u) {
            $db->insert("INSERT INTO notifications (user_id, type, title, message, created_at) VALUES (?, ?, ?, ?, NOW())",
                [$u['id'], 'welcome', 'Bienvenue sur LUMA', 'Merci de vous être inscrit sur LUMA. Explorez nos ressources et notre communauté.']);
        }
        echo "  notifications ✓\n";
    }
}

// ── Appointments ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM appointments");
if ($existing['c'] == 0) {
    $mother = $db->fetch("SELECT m.id, u.id as user_id FROM mothers m JOIN users u ON m.user_id = u.id LIMIT 1");
    $expert = $db->fetch("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'expert' LIMIT 1");
    if ($mother && $expert) {
        $db->insert("INSERT INTO appointments (mother_id, expert_id, appointment_date, status, type, notes, created_at) VALUES (?, ?, ?, 'confirmed', 'online', ?, NOW())",
            [$mother['id'], $expert['id'], date('Y-m-d H:i:s', strtotime('+2 days 10:00')), 'Consultation prénatale de routine']);
        $db->insert("INSERT INTO appointments (mother_id, expert_id, appointment_date, status, type, notes, created_at) VALUES (?, ?, ?, 'pending', 'in_person', ?, NOW())",
            [$mother['id'], $expert['id'], date('Y-m-d H:i:s', strtotime('+1 week 14:30')), 'Échographie de contrôle']);
        echo "  appointments ✓\n";
    }
}

// ── Expert Messages ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM expert_messages");
if ($existing['c'] == 0) {
    $users = $db->fetchAll("SELECT id FROM users ORDER BY id LIMIT 2");
    if (count($users) >= 2) {
        $db->insert("INSERT INTO expert_messages (sender_id, receiver_id, message, is_read, created_at) VALUES (?, ?, ?, 1, NOW())",
            [$users[1]['id'], $users[0]['id'], 'Bonjour, je suis votre expert dédié. N\'hésitez pas à me poser vos questions !']);
        echo "  expert_messages ✓\n";
    }
}

// ── Baby Memories ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM baby_memories");
if ($existing['c'] == 0) {
    $baby = $db->fetch("SELECT id FROM babies LIMIT 1");
    if ($baby) {
        $db->insert("INSERT INTO baby_memories (baby_id, title, content, event_date, created_at) VALUES (?, ?, ?, ?, NOW())",
            [$baby['id'], 'Premier sourire', 'Youssef a fait son premier sourire aujourd\'hui ! Un moment magique.', '2026-06-25']);
        $db->insert("INSERT INTO baby_memories (baby_id, title, content, event_date, created_at) VALUES (?, ?, ?, ?, NOW())",
            [$baby['id'], 'Premier bain', 'Son premier bain à la maison. Il a adoré l\'eau !', '2026-06-12']);
        echo "  baby_memories ✓\n";
    }
}

// ── Baby Milestones ──
$existing = $db->fetch("SELECT COUNT(*) as c FROM baby_milestones");
if ($existing['c'] == 0) {
    $baby = $db->fetch("SELECT id FROM babies LIMIT 1");
    if ($baby) {
        $db->insert("INSERT INTO baby_milestones (baby_id, milestone_key, achieved_date) VALUES (?, ?, ?)", [$baby['id'], 'first_smile', '2026-06-25']);
        $db->insert("INSERT INTO baby_milestones (baby_id, milestone_key, achieved_date) VALUES (?, ?, ?)", [$baby['id'], 'first_bath', '2026-06-12']);
        $db->insert("INSERT INTO baby_milestones (baby_id, milestone_key, achieved_date) VALUES (?, ?, ?)", [$baby['id'], 'first_word', null]);
        echo "  baby_milestones ✓\n";
    }
}

echo "\nSeeding complete!\n";
