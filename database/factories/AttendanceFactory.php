<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function getInstanceOf($class, $returnIdOnly = true)
    {
        $instance = $class::inRandomOrder()->first() ?? $this->factory($class)->create();
        return $returnIdOnly ? $instance->id : $instance;
    }

    public function definition(): array
    {
        return [
            'event_id' => $this->getInstanceOf(Event::class),
            'user_id' => $this->getInstanceOf(User::class),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
