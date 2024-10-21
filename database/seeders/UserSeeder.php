<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // Default password
        ]);

        // Generate a token for this user
        $token = $user->createToken('api-token')->plainTextToken;

        // Output the token to the console so you can use it
        echo "API Token for Test User: $token\n";
    }
}
