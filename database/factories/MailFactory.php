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
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => Carbon::now(),
        ];
    }
}
