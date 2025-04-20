<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventGuest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventGuestFactory extends Factory
{
    protected $model = EventGuest::class;

    public function getInstanceOf($class, $returnIdOnly = true)
    {
        $instance = $class::inRandomOrder()->first() ?? $this->factory($class)->create();

        return $returnIdOnly ? $instance->id : $instance;
    }

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'invited_by' => User::factory(),
            'guest_name' => fake()->name(),
            'guest_email' => fake()->optional(0.7)->safeEmail(),

        ];
    }
}
