<?php

/** @noinspection PhpVoidFunctionResultUsedInspection */

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function getInstanceOf($class, $returnIdOnly = true)
    {
        $instance = $class::inRandomOrder()->first() ?? $this->factory($class)->create();

        return $returnIdOnly ? $instance->id : $instance;
    }

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->text(),
            'location' => $this->faker->address(),
            'date' => $this->faker->dateTimeBetween('-10 days', '+10 days'),
            'time' => $this->faker->time(),
            'allow_guests' => $this->faker->randomElement([true, false]),
            'max_guests_per_user' => $this->faker->numberBetween(0, 4),
            'min_attendees' => $this->faker->numberBetween(5, 20),
            'max_attendees' => $this->faker->numberBetween(20, 50),
            'user_id' => User::factory(),
            'photo_path' => $this->faker->word(),
        ];
    }

    /**
     * Indicate that the event is in the past.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => fake()->dateTimeBetween('-90 days', '-1 days'),
        ]);
    }

    /**
     * Indicate that the event is in the future.
     */
    public function future(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => fake()->dateTimeBetween('+1 days', '+90 days'),
        ]);
    }


    private function factory($class): void {}
}
