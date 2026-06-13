<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin : toutes les permissions
        DB::table('role_permissions')->insert(
            DB::table('roles')
                ->join('permissions', function($join) {
                    $join->on(1, '=', 1); // Cross join
                })
                ->where('roles.slug', 'admin')
                ->select('roles.id as role_id', 'permissions.id as permission_id')
                ->get()
                ->toArray()
        );

        // Maman
        DB::table('role_permissions')->insert(
            DB::table('roles')
                ->join('permissions', function($join) {
                    $join->on(1, '=', 1); // Cross join
                })
                ->where('roles.slug', 'maman')
                ->whereIn('permissions.slug', [
                    'articles.view', 'resources.view', 'community.view', 'community.create',
                    'community.edit', 'tickets.create', 'tickets.view', 'testimonials.view',
                    'faqs.view'
                ])
                ->select('roles.id as role_id', 'permissions.id as permission_id')
                ->get()
                ->toArray()
        );

        // Expert
        DB::table('role_permissions')->insert(
            DB::table('roles')
                ->join('permissions', function($join) {
                    $join->on(1, '=', 1); // Cross join
                })
                ->where('roles.slug', 'expert')
                ->whereIn('permissions.slug', [
                    'articles.view', 'articles.create', 'articles.edit', 'articles.publish',
                    'resources.view', 'resources.create', 'resources.edit',
                    'community.view', 'community.create',
                    'tickets.view', 'testimonials.view', 'faqs.view'
                ])
                ->select('roles.id as role_id', 'permissions.id as permission_id')
                ->get()
                ->toArray()
        );

        // CTT
        DB::table('role_permissions')->insert(
            DB::table('roles')
                ->join('permissions', function($join) {
                    $join->on(1, '=', 1); // Cross join
                })
                ->where('roles.slug', 'ctt')
                ->whereIn('permissions.slug', [
                    'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.assign', 'tickets.close',
                    'faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete',
                    'users.view', 'mothers.view', 'experts.view'
                ])
                ->select('roles.id as role_id', 'permissions.id as permission_id')
                ->get()
                ->toArray()
        );
    }
}
