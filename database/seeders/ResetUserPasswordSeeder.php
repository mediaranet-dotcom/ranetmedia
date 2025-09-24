<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswordSeeder extends Seeder
{
    public function run()
    {
        // Reset password untuk user yang ada
        $users = [
            'user@email.com' => 'password',
            'admin@admin.com' => 'password',
            'admin@test.com' => 'password',
        ];

        foreach ($users as $email => $password) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->password = Hash::make($password);
                $user->save();
                echo "Password updated for: {$email}\n";
            } else {
                // Buat user baru jika tidak ada
                User::create([
                    'name' => 'Admin',
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);
                echo "User created: {$email}\n";
            }
        }

        // Tampilkan semua user
        echo "\nAll users in database:\n";
        User::all(['id', 'name', 'email'])->each(function ($user) {
            echo "ID: {$user->id} - Email: {$user->email} - Name: {$user->name}\n";
        });
    }
}
