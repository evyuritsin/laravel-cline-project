<?php

namespace Database\Factories;

use App\Models\Inspection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InspectionFactory extends Factory
{
    protected $model = Inspection::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(55.0, 56.0),
            'longitude' => $this->faker->longitude(37.0, 38.0),
            'type' => $this->faker->randomElement(['move_in', 'move_out']),
            'status' => $this->faker->randomElement(['draft', 'completed', 'sent']),
            'inspection_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}