<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use DateMalformedStringException;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Random\RandomException;

class EventSeeder extends Seeder
{
    /**
     * @throws DateMalformedStringException
     * @throws RandomException
     */
    public function run(): void
    {
        $faker = Factory::create();

        $users = User::all(); // Ensure users exist first

        foreach (range(1, 20) as $i) {
            $start = $faker->dateTimeBetween('-10 days', '+10 days');
            $end = (clone $start)->modify('+'.random_int(1, 5).' hours');

            Event::create([
                'user_id' => $users->random()->id,
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'location' => $faker->address,
                'start_time' => $start,
                'end_time' => $end,
                'min_attendees' => random_int(2, 5),
                'max_attendees' => random_int(6, 20),
                'photo_path' => null, // or seed a placeholder path
            ]);
        }
    }
}
