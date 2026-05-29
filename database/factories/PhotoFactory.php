<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'filename' => $this->faker->uuid() . '.jpg',
            'storage_path' => 'photos/' . $this->faker->uuid() . '.jpg',
            'latitude' => $this->faker->latitude(55.0, 56.0),
            'longitude' => $this->faker->longitude(37.0, 38.0),
            'description' => $this->faker->optional()->sentence(),
            'taken_at' => now(),
            'file_size' => $this->faker->numberBetween(50000, 500000),
            'mime_type' => 'image/jpeg',
        ];
    }
}