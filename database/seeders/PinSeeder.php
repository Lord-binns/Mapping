<?php

namespace Database\Seeders;

use App\Models\Pin;
use App\Models\User;
use Illuminate\Database\Seeder;

class PinSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = User::factory()->create(['role' => 'admin']);
        }

        Pin::factory(10)->create([
            'user_id' => $adminUser->id,
        ]);
    }
}

