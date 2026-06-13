<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BabiesTableSeeder extends Seeder
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

        DB::table('babies')->insert([
            'mother_id' => $motherId,
            'name' => 'Youssef',
            'date_of_birth' => '2026-06-10',
            'gender' => 'boy',
            'blood_type' => 'O+',
            'notes' => 'Né à terme, poids 3.2kg',
        ]);
    }
}
