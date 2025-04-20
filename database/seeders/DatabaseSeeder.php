<?php

namespace Database\Seeders;

use App\Models\Attendee;
use App\Models\Comment;
use App\Models\Event;
use App\Models\EventGuest;
use App\Models\Mail;
use App\Models\Rating;
use App\Models\Registrant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Create an instance of the RoleAndPermissionSeeder class
        $roleAndPermissionSeeder = new RoleAndPermissionSeeder;
        $roleAndPermissionSeeder->run(); // Call the run method on the instance

        /**
         * create a super admin user
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
            'unsubscribe_token' => Str::random(32),
        ]);

        $user->assignRole('super-admin');

        // Create users
        $users = User::factory(20)->create();

        foreach ($users as $user) {
            $user->assignRole('user');
        }

        Registrant::factory(20)->create();

        Mail::factory(10)->create();

        // Create events
        $pastEvents = Event::factory(5)
            ->past()
            ->create(['user_id' => $users->random()->id]);

        $futureEvents = Event::factory(10)
            ->future()
            ->create(['user_id' => $users->random()->id]);

        $events = $pastEvents->merge($futureEvents);

        // Add attendees to events
        $events->each(function ($event) use ($users) {
            // Add a random number of attendees (between 3 and 8)
            $attendeeCount = random_int(3, 8);
            $randomUsers = $users->random($attendeeCount);

            foreach ($randomUsers as $user) {
                Attendee::factory()->create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'is_attending' => true,
                ]);
            }

            // Add some guests for events that allow them
            if ($event->allow_guests) {
                $guestInviters = $randomUsers->random(min(3, $randomUsers->count()));

                foreach ($guestInviters as $inviter) {
                    $guestCount = 0;
                    if ($event->max_guests_per_user >= 1) {
                        $guestCount = min(
                            random_int(1, $event->max_guests_per_user),
                            $event->max_guests_per_user
                        );
                    }

                    for ($i = 0; $i < $guestCount; $i++) {
                        EventGuest::factory()->create([
                            'event_id' => $event->id,
                            'invited_by' => $inviter->id,
                        ]);
                    }
                }
            }

            // Add comments to events
            $commentCount = random_int(2, 10); // Random number of comments per event
            $commenters = $users->random(min($commentCount, $users->count()));

            foreach ($commenters as $commenter) {
                Comment::factory()->create([
                    'user_id' => $commenter->id,
                    'commentable_id' => $event->id,
                    'commentable_type' => Event::class,
                ]);
            }

            // Add ratings for past events
            if ($event->date < now()) {
                $raters = $randomUsers->random(min(5, $randomUsers->count()));

                foreach ($raters as $user) {
                    Rating::factory()->create([
                        'ratable_id' => $event->id,
                        'ratable_type' => Event::class,
                        'user_id' => $user->id,
                    ]);
                }
            }
        });
    }
}
