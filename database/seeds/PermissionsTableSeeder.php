<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Users
            ['name' => 'Voir les utilisateurs', 'slug' => 'users.view', 'group_name' => 'users'],
            ['name' => 'Créer des utilisateurs', 'slug' => 'users.create', 'group_name' => 'users'],
            ['name' => 'Modifier les utilisateurs', 'slug' => 'users.edit', 'group_name' => 'users'],
            ['name' => 'Supprimer les utilisateurs', 'slug' => 'users.delete', 'group_name' => 'users'],
            ['name' => 'Suspendre les utilisateurs', 'slug' => 'users.suspend', 'group_name' => 'users'],
            // Articles
            ['name' => 'Voir les articles', 'slug' => 'articles.view', 'group_name' => 'articles'],
            ['name' => 'Créer des articles', 'slug' => 'articles.create', 'group_name' => 'articles'],
            ['name' => 'Modifier les articles', 'slug' => 'articles.edit', 'group_name' => 'articles'],
            ['name' => 'Supprimer les articles', 'slug' => 'articles.delete', 'group_name' => 'articles'],
            ['name' => 'Publier des articles', 'slug' => 'articles.publish', 'group_name' => 'articles'],
            ['name' => 'Modérer les commentaires', 'slug' => 'articles.moderate_comments', 'group_name' => 'articles'],
            // Resources
            ['name' => 'Voir les ressources', 'slug' => 'resources.view', 'group_name' => 'resources'],
            ['name' => 'Créer des ressources', 'slug' => 'resources.create', 'group_name' => 'resources'],
            ['name' => 'Modifier les ressources', 'slug' => 'resources.edit', 'group_name' => 'resources'],
            ['name' => 'Supprimer les ressources', 'slug' => 'resources.delete', 'group_name' => 'resources'],
            // Community
            ['name' => 'Voir la communauté', 'slug' => 'community.view', 'group_name' => 'community'],
            ['name' => 'Créer des publications', 'slug' => 'community.create', 'group_name' => 'community'],
            ['name' => 'Modifier ses publications', 'slug' => 'community.edit', 'group_name' => 'community'],
            ['name' => 'Supprimer les publications', 'slug' => 'community.delete', 'group_name' => 'community'],
            ['name' => 'Modérer la communauté', 'slug' => 'community.moderate', 'group_name' => 'community'],
            // Tickets
            ['name' => 'Voir les tickets', 'slug' => 'tickets.view', 'group_name' => 'tickets'],
            ['name' => 'Créer des tickets', 'slug' => 'tickets.create', 'group_name' => 'tickets'],
            ['name' => 'Modifier les tickets', 'slug' => 'tickets.edit', 'group_name' => 'tickets'],
            ['name' => 'Assigner les tickets', 'slug' => 'tickets.assign', 'group_name' => 'tickets'],
            ['name' => 'Fermer les tickets', 'slug' => 'tickets.close', 'group_name' => 'tickets'],
            // Testimonials
            ['name' => 'Voir les témoignages', 'slug' => 'testimonials.view', 'group_name' => 'testimonials'],
            ['name' => 'Approuver les témoignages', 'slug' => 'testimonials.approve', 'group_name' => 'testimonials'],
            ['name' => 'Rejeter les témoignages', 'slug' => 'testimonials.reject', 'group_name' => 'testimonials'],
            // FAQs
            ['name' => 'Voir la FAQ', 'slug' => 'faqs.view', 'group_name' => 'faqs'],
            ['name' => 'Créer des FAQ', 'slug' => 'faqs.create', 'group_name' => 'faqs'],
            ['name' => 'Modifier des FAQ', 'slug' => 'faqs.edit', 'group_name' => 'faqs'],
            ['name' => 'Supprimer des FAQ', 'slug' => 'faqs.delete', 'group_name' => 'faqs'],
            // Experts
            ['name' => 'Voir les experts', 'slug' => 'experts.view', 'group_name' => 'experts'],
            ['name' => 'Valider les experts', 'slug' => 'experts.validate', 'group_name' => 'experts'],
            ['name' => 'Gérer les certifications', 'slug' => 'experts.manage_certifications', 'group_name' => 'experts'],
            // Mothers
            ['name' => 'Voir les mamans', 'slug' => 'mothers.view', 'group_name' => 'mothers'],
            ['name' => 'Modifier les profils mamans', 'slug' => 'mothers.edit', 'group_name' => 'mothers'],
            // Admin
            ['name' => 'Accès administration', 'slug' => 'admin.access', 'group_name' => 'admin'],
            // Settings
            ['name' => 'Voir les paramètres', 'slug' => 'settings.view', 'group_name' => 'settings'],
            ['name' => 'Modifier les paramètres', 'slug' => 'settings.edit', 'group_name' => 'settings'],
            // Notifications
            ['name' => 'Envoyer des notifications', 'slug' => 'notifications.send', 'group_name' => 'notifications'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert($permission);
        }
    }
}
