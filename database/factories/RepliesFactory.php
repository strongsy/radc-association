<?php

namespace Database\Factories;

use App\Models\Reply;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RepliesFactory extends Factory
{
    protected $model = Reply::class;

    public function definition(): array
    {
        return [
            'mail_id' => $this->faker->randomNumber(),
            'user_id' => $this->faker->randomNumber(),
            'message' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
