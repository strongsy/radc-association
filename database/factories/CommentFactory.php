<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function getInstanceOf($class, $returnIdOnly = true)
    {
        $instance = $class::inRandomOrder()->first() ?? $this->factory($class)->create();

        return $returnIdOnly ? $instance->id : $instance;
    }

    public function definition(): array
    {
        return [
            'content' => fake()->paragraph(),
            'user_id' => User::factory(),
            'commentable_id' => Event::factory(),
            'commentable_type' => Event::class,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function ($comment) {
            // Additional configuration if needed
        })->afterCreating(function ($comment) {
            // Additional actions after creation if needed
        });
    }

    /**
     * Indicate the comment is for a specific commentable model.
     */
    public function forCommentable(Model $commentable): static
    {
        return $this->state(function (array $attributes) use ($commentable) {
            return [
                'commentable_id' => $commentable->id,
                'commentable_type' => get_class($commentable),
            ];
        });
    }

}
