<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['name' => 'Grossesse', 'slug' => 'grossesse', 'description' => 'Articles sur la grossesse'],
            ['name' => 'Bébé', 'slug' => 'bebe', 'description' => 'Soins et développement du bébé'],
            ['name' => 'Bien-être', 'slug' => 'bien-etre', 'description' => 'Bien-être et santé maternelle'],
            ['name' => 'Allaitement', 'slug' => 'allaitement', 'description' => 'Conseils sur l allaitement'],
            ['name' => 'Retour d\'expérience', 'slug' => 'retour-experience', 'description' => 'Témoignages et retours d\'expérience'],
            ['name' => 'Organisation', 'slug' => 'organisation', 'description' => 'Conseils d\'organisation familiale'],
        ]);
    }
}
