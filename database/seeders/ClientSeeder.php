<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;


class ClientSeeder extends Seeder
{
    public function run()
    {
        // Создание тестового клиента
        Client::create([
            'full_name' => 'Иван Иванов',
            'email' => 'ivanov@example.ru',
            'password' => Hash::make('password123'),
            'login' => 'ivan_ivanov',
            'birth' => '1985-05-15',
            'telephone' => '+7 912 345 67 89',
        ]);

        Client::create([
            'full_name' => 'Мария Смирнова',
            'email' => 'smirnova@example.ru',
            'password' => Hash::make('password123'),
            'login' => 'maria_smirnova',
            'birth' => '1990-08-25',
            'telephone' => '+7 905 876 54 32',
        ]);
    }
}
