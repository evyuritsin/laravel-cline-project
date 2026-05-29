<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Inspection;
use App\Models\Room;
use App\Models\Photo;
use App\Models\Report;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём тестовых пользователей
        $users = User::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Иван Петров', 'tier' => 'free'],
                ['name' => 'Анна Сидорова', 'tier' => 'starter'],
                ['name' => 'Михаил Иванов', 'tier' => 'pro'],
                ['name' => 'Елена Козлова', 'tier' => 'premium'],
                ['name' => 'Тестовый Пользователь', 'tier' => 'free']
            )
            ->create();

        // Для каждого пользователя создаём по 2-3 осмотра
        foreach ($users as $user) {
            $inspections = Inspection::factory()
                ->count(fake()->numberBetween(2, 3))
                ->sequence(
                    ['type' => 'move_in', 'status' => 'completed'],
                    ['type' => 'move_out', 'status' => 'draft']
                )
                ->create(['user_id' => $user->id]);

            foreach ($inspections as $inspection) {
                // Создаём комнаты для каждого осмотра
                $rooms = Room::factory()
                    ->count(fake()->numberBetween(3, 6))
                    ->create(['inspection_id' => $inspection->id]);

                // Добавляем фотографии к каждой комнате
                foreach ($rooms as $room) {
                    Photo::factory()
                        ->count(fake()->numberBetween(2, 5))
                        ->create(['room_id' => $room->id]);
                }

                // Генерируем отчёт для завершённых осмотров
                if ($inspection->status === 'completed') {
                    Report::factory()->create(['inspection_id' => $inspection->id]);
                }
            }
        }

        // Создаём один осмотр с полными данными для демонстрации
        $demoUser = User::factory()->create([
            'name' => 'Демо Пользователь',
            'email' => 'demo@example.com',
            'tier' => 'pro',
            'telegram_id' => 123456789,
        ]);

        $demoInspection = Inspection::factory()->create([
            'user_id' => $demoUser->id,
            'address' => 'Москва, ул. Примерная, д. 1, кв. 100',
            'latitude' => 55.755794,
            'longitude' => 37.617139,
            'type' => 'move_in',
            'status' => 'completed',
            'notes' => 'Демонстрационный осмотр квартиры',
        ]);

        $roomNames = ['Кухня', 'Гостиная', 'Спальня', 'Ванная комната', 'Прихожая'];
        foreach ($roomNames as $index => $roomName) {
            $room = Room::factory()->create([
                'inspection_id' => $demoInspection->id,
                'name' => $roomName,
                'sort_order' => $index + 1,
                'notes' => "Осмотр {$roomName}",
            ]);

            Photo::factory()->count(3)->create([
                'room_id' => $room->id,
            ]);
        }

        Report::factory()->create([
            'inspection_id' => $demoInspection->id,
        ]);

        echo "Тестовые данные созданы успешно!\n";
        echo "Демо пользователь: demo@example.com\n";
    }
}