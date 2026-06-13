<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Call other seeders here
        $this->call([
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolePermissionsTableSeeder::class,
            UsersTableSeeder::class,
            MothersTableSeeder::class,
            BabiesTableSeeder::class,
            PregnanciesTableSeeder::class,
            GrowthRecordsTableSeeder::class,
            VaccinationsTableSeeder::class,
            CategoriesTableSeeder::class,
            ArticlesTableSeeder::class,
            CommentsTableSeeder::class,
            ResourcesTableSeeder::class,
            CommunityPostsTableSeeder::class,
            CommunityLikesTableSeeder::class,
            CommunityCommentsTableSeeder::class,
            TestimonialsTableSeeder::class,
            FaqsTableSeeder::class,
            ContactsTableSeeder::class,
            NewslettersTableSeeder::class,
            TicketsTableSeeder::class,
            TicketMessagesTableSeeder::class,
            NotificationsTableSeeder::class,
            SettingsTableSeeder::class,
            AppointmentsTableSeeder::class,
            ExpertMessagesTableSeeder::class,
            BabyMemoriesTableSeeder::class,
            BabyMilestonesTableSeeder::class,
        ]);
    }
}
