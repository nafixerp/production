<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@fooderp.com'], [
            'name'     => 'Admin',
            'email'    => 'admin@fooderp.com',
            'password' => Hash::make('12345'),
        ]);
    }
}
