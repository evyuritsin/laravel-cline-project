<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\Inspection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'inspection_id' => Inspection::factory(),
            'pdf_path' => 'reports/' . $this->faker->uuid() . '.pdf',
            'status' => $this->faker->randomElement(['pending', 'sent', 'failed']),
            'generated_at' => now(),
        ];
    }
}