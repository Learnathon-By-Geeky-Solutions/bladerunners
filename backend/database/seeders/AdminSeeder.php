<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1) Find the most recently created User with type = 'Admin'
        $user = User::where('type', 'Admin')
                    ->orderBy('created_at', 'desc')
                    ->first();

        if (! $user) {
            $this->command->warn('No users with type=Admin found, skipping AdminSeeder.');
            return;
        }

        // 2) Insert or update their row in the admins table
        Admin::updateOrCreate(
            ['admin_id' => $user->id],
            [
                'department'     => 'IT Operations',
                'role_level'     => 'Super Admin',
                'is_super_admin'=> true,
            ]
        );
    }
}
