<?php /** @noinspection PhpVoidFunctionResultUsedInspection */

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
            'date' => $this->faker->dateTimeBetween('-10 days', '+10 days'),
            'time' => $this->faker->time(),
            'min_attendees' => $this->faker->randomNumber(2, true),
            'max_attendees' => $this->faker->randomNumber(2, true),
            'user_id' => $this->getInstanceOf(User::class),
            'photo_path' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function factory($class): void
    {
    }
}
