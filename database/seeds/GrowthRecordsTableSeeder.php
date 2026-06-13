<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrowthRecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get baby ID
        $babyId = DB::table('babies')->where('mother_id', DB::table('mothers')->where('user_id', DB::table('users')->where('email', 'maman@luma.tn')->value('id'))->value('id'))->value('id');

        DB::table('growth_records')->insert([
            'baby_id' => $babyId,
            'record_date' => '2026-06-10',
            'weight' => 3.20,
            'height' => 50.0,
            'head_circumference' => 34.0,
            'notes' => 'Mesures à la naissance',
        ]);

        DB::table('growth_records')->insert([
            'baby_id' => $babyId,
            'record_date' => '2026-06-17',
            'weight' => 3.50,
            'height' => 51.0,
            'head_circumference' => 34.5,
            'notes' => 'Première semaine, bonne prise de poids',
        ]);
    }
}
