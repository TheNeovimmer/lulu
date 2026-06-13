<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VaccinationsTableSeeder extends Seeder
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

        DB::table('vaccinations')->insert([
            'baby_id' => $babyId,
            'vaccine_name' => 'BCG',
            'due_date' => '2026-06-10',
            'administered_date' => '2026-06-10',
            'notes' => 'Vaccin administré à la maternité',
            'status' => 'done',
        ]);

        DB::table('vaccinations')->insert([
            'baby_id' => $babyId,
            'vaccine_name' => 'Hépatite B',
            'due_date' => '2026-06-10',
            'administered_date' => '2026-06-10',
            'notes' => 'Première dose',
            'status' => 'done',
        ]);

        DB::table('vaccinations')->insert([
            'baby_id' => $babyId,
            'vaccine_name' => 'DTaP-Hib-IPV',
            'due_date' => '2026-07-10',
            'administered_date' => null,
            'notes' => 'À administrer à 2 mois',
            'status' => 'pending',
        ]);
    }
}
