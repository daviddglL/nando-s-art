<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = config('admin.password');

        // Sin ADMIN_PASSWORD en .env se genera una aleatoria y se muestra una sola vez
        if (empty($password)) {
            $password = Str::password(16);
            $this->command?->warn('ADMIN_PASSWORD no está definida. Contraseña generada para '.config('admin.email').': '.$password);
        }

        $users = [
            [
                'name' => config('admin.name'),
                'email' => config('admin.email'),
                'password' => bcrypt($password),
                'role' => Role::Admin->value,
                'remember_token' => Str::random(10),
            ],
        ];

        // Usuario de pruebas solo en local
        if (app()->environment('local')) {
            $users[] = [
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('admin'),
                'role' => Role::Admin->value,
                'remember_token' => Str::random(10),
            ];
        }

        DB::table('users')->insert($users);
    }
}
