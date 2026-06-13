<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MothersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get user ID for maman
        $mamanUserId = DB::table('users')->where('email', 'maman@luma.tn')->value('id');

        DB::table('mothers')->insert([
            'user_id' => $mamanUserId,
            'date_of_birth' => '1995-05-15',
            'due_date' => '2026-12-20',
            'pregnancy_week' => 28,
            'city' => 'Sfax',
            'bio' => 'Première grossesse, suivie régulièrement',
            'avatar' => null,
            'child_count' => 0,
        ]);
    }
}
