<?php

namespace Database\Factories;

use App\Models\Mail;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RepliesFactory extends Factory
{
    protected $model = Reply::class;

    public function definition(): array
    {
        return [
            'mail_id' => Mail::factory(),
            'user_id' => User::factory(),
            'message' => $this->faker->paragraphs(3, true),
        ];
    }
}
