<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'telegram_id' => fake()->unique()->randomNumber(9),
            'tier' => fake()->randomElement(['free', 'starter', 'pro', 'premium']),
            'avatar_url' => fake()->optional()->imageUrl(),
            'metadata' => ['phone' => fake()->optional()->phoneNumber()],
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'tier' => 'free',
        ]);
    }

    public function pro(): static
    {
        return $this->state(fn (array $attributes) => [
            'tier' => 'pro',
        ]);
    }
}