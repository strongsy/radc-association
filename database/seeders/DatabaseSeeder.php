<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\RegistrantFactory;
use Illuminate\Database\Seeder;

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
            'affiliation' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quae.',
            'is_subscribed' => true,
            'is_blocked' => false,
        ]);

        $user->assignRole('super-admin');

        RegistrantFactory::new()->count(20)->create();

        //create users
        /*$newUser = User::factory(10)->create();

        foreach ($newUser as $user) {
            $user->assignRole('user');
        }*/
    }
}
