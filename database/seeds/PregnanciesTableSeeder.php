<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PregnanciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get mother ID
        $motherId = DB::table('mothers')->where('user_id', DB::table('users')->where('email', 'maman@luma.tn')->value('id'))->value('id');

        DB::table('pregnancies')->insert([
            'mother_id' => $motherId,
            'start_date' => '2026-04-20',
            'due_date' => '2026-12-20',
            'week' => 28,
            'trimester' => 3,
            'notes' => 'Grossesse normale, suivi mensuel',
            'status' => 'active',
        ]);
    }
}
