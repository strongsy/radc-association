<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'caption' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
