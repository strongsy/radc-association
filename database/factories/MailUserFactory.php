<?php

namespace Database\Factories;

use App\Models\MailUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MailUserFactory extends Factory
{
    protected $model = MailUser::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'mail_id' => $this->faker->randomNumber(),
            'subject' => $this->faker->word(),
            'message' => $this->faker->word(),
            'replied_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
