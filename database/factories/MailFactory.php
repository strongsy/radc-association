<?php

namespace Database\Factories;

use App\Models\Mail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MailFactory extends Factory
{
    protected $model = Mail::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'subject' => $this->faker->words(5, true),
            'message' => $this->faker->paragraphs(3, true),
        ];
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-90 days', '-1 days'),
        ]);
    }

    /**
     * Indicate that the event is in the future.
     */
    public function future(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('+1 days', '+90 days'),
        ]);
    }
}
