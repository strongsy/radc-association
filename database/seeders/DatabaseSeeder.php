<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\AttendanceFactory;
use Database\Factories\EventFactory;
use Database\Factories\MailFactory;
use Database\Factories\RegistrantFactory;
use Illuminate\Database\Seeder;
use Random\RandomException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an instance of the RoleAndPermissionSeeder class
        $roleAndPermissionSeeder = new RoleAndPermissionSeeder();
        $roleAndPermissionSeeder->run(); // Call the run method on the instance

        /**
         * create super admin user
         */
        $user = User::factory()->create([
            'name' => 'Paul Armstrong',
            'email' => 'strongs@icloud.com',
            'password' => bcrypt('ginpalsup'),
            'community' => 'Veteran',
            'membership' => 'Life',
            'affiliation' => 'All files within the bucket are public and are publicly accessible via the Internet via a Laravel Cloud provided URL. These buckets are typically used for publicly viewable assets like user avatars.',
            'is_subscribed' => true,
            'is_blocked' => false,
        ]);

        //assign super-admin to my account
        $user->assignRole('super-admin');

        //create registrants
        RegistrantFactory::new()->count(20)->create();

        //create mail
        MailFactory::new()->count(5)->create();

        //create users
        $newUser = User::factory(20)->create();

        //loop through added users and assign user role
        foreach ($newUser as $user) {
            $user->assignRole('user');
        }

        //create events
        EventFactory::new()->count(10)->create();

        //create attendees
        AttendanceFactory::new()->count(10)->create();
    }
}
