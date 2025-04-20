<?php

namespace Database\Factories;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AttendeeFactory extends Factory
{
    protected $model = Attendee::class;

    public function getInstanceOf($class, $returnIdOnly = true)
    {
        $instance = $class::inRandomOrder()->first() ?? $this->factory($class)->create();

        return $returnIdOnly ? $instance->id : $instance;
    }

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'is_attending' => $this->faker->randomElement([true, false]),
        ];
    }

    public function notAttending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_attending' => false,
        ]);
    }

}
