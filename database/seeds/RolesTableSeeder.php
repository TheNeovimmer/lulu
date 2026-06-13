<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['name' => 'Administrateur', 'slug' => 'admin', 'description' => 'Gestion complète de la plateforme'],
            ['name' => 'Maman', 'slug' => 'maman', 'description' => 'Utilisatrice principale de la plateforme'],
            ['name' => 'Expert', 'slug' => 'expert', 'description' => 'Professionnel de santé'],
            ['name' => 'CTT', 'slug' => 'ctt', 'description' => 'Agent du centre de traitement et téléassistance'],
        ]);
    }
}
