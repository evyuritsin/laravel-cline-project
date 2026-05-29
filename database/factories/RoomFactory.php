<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Inspection;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'inspection_id' => Inspection::factory(),
            'name' => $this->faker->randomElement(['Кухня', 'Спальня', 'Гостиная', 'Ванная комната', 'Прихожая', 'Балкон']),
            'sort_order' => $this->faker->numberBetween(1, 10),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}