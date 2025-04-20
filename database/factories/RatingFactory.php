<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function getInstanceOf($class, $returnIdOnly = true)
    {
        $instance = $class::inRandomOrder()->first() ?? $this->factory($class)->create();

        return $returnIdOnly ? $instance->id : $instance;
    }

    public function definition(): array
    {
        return [
            'rating' => fake()->numberBetween(1, 5),
            'review' => fake()->optional(0.8)->paragraph(), // 80% chance of having a review
            'user_id' => User::factory(),
            'ratable_id' => Event::factory(),
            'ratable_type' => Event::class,

        ];
    }
}
