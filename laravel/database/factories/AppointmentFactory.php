<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * @throws \Exception
     */
    #[ArrayShape(['begin_time' => '\DateTime', 'end_time' => '\DateTime', 'service_id' => 'mixed', 'user_id' => 'mixed', 'note' => 'string'])]
    public function definition() : array
    {
        $service = Service::random();
        $begin_time = $this->faker->dateTimeBetween('now', '+1 week');
        $end_time = $begin_time->add(new DateInterval('PT' . $service->duration . 'M'));
        return [
            'begin_time' => $begin_time,
            'end_time' => $end_time,
            'service_id' => $service->id,
            'user_id' => User::random()->id,
            'note' => $this->faker->boolean(25) ? $this->faker->paragraph() : ''
        ];
    }
}
