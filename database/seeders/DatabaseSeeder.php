<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $token = '1|this_is_a_fixed_token_for_testing_purposes';

        $user->tokens()->create([
            'name' => 'ScribeToken',
            'token' => hash('sha256', explode('|', $token)[1]),
            'abilities' => ['*'],
        ]);

        $this->call([
            TaskSeeder::class,
        ]);
    }
}
