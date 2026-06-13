<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get role IDs
        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');
        $mamanRoleId = DB::table('roles')->where('slug', 'maman')->value('id');
        $expertRoleId = DB::table('roles')->where('slug', 'expert')->value('id');
        $cttRoleId = DB::table('roles')->where('slug', 'ctt')->value('id');

        // Admin user
        DB::table('users')->insert([
            'role_id' => $adminRoleId,
            'name' => 'Admin LUMA',
            'email' => 'admin@luma.tn',
            'password' => Hash::make('password'),
            'phone' => '+216 97 203 908',
            'avatar' => null,
            'status' => 'active',
            'specialty' => null,
            'bio' => 'Administrateur système',
            'address' => 'Tunis, Tunisie',
        ]);

        // Expert user
        DB::table('users')->insert([
            'role_id' => $expertRoleId,
            'name' => 'Dr. Amira Ben Ali',
            'email' => 'expert@luma.tn',
            'password' => Hash::make('password'),
            'phone' => '+216 20 123 456',
            'avatar' => null,
            'status' => 'active',
            'specialty' => 'Gynécologue obstétricien',
            'bio' => 'Spécialiste en suivi de grossesse et accouchement',
            'address' => 'Sousse, Tunisie',
        ]);

        // Mother user
        DB::table('users')->insert([
            'role_id' => $mamanRoleId,
            'name' => 'Leila Trabelsi',
            'email' => 'maman@luma.tn',
            'password' => Hash::make('password'),
            'phone' => '+216 55 678 901',
            'avatar' => null,
            'status' => 'active',
            'specialty' => null,
            'bio' => 'Future maman de 28 semaines',
            'address' => 'Sfax, Tunisie',
        ]);

        // CTT user
        DB::table('users')->insert([
            'role_id' => $cttRoleId,
            'name' => 'Nour El Houda',
            'email' => 'ctt@luma.tn',
            'password' => Hash::make('password'),
            'phone' => '+216 22 334 455',
            'avatar' => null,
            'status' => 'active',
            'specialty' => null,
            'bio' => 'Agent CTT spécialisé en assistance maternelle',
            'address' => 'Gabès, Tunisie',
        ]);
    }
}
