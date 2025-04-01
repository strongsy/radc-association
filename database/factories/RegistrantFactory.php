<?php

namespace Database\Factories;

use App\Models\Registrant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RegistrantFactory extends Factory
{
    protected $model = Registrant::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'community' => fake()->randomElement(['Serving', 'Reserve', 'Veteran', 'Civilian', 'Other']),
            'membership' => fake()->randomElement(['Life', 'Annual', 'Unknown']),
            'affiliation' => $this->faker->paragraphs(2, true),
            'is_subscribed' => fake()->randomelement([true, false]),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
