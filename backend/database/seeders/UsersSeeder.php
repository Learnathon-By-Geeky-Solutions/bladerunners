<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Import your models
use App\Models\Admin;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $usersData = [
            // Admin
            [
                'type' => 'Admin',
                'email' => 'admin@example.com',
                'name' => 'Admin User',
                'payment_phone' => '1234567890',
                'balance' => 100.00,
                'nationality' => 'USA',
                'google_id' => null,
            ],
            // Clients
            [
                'type' => 'Client',
                'email' => 'client1@example.com',
                'name' => 'Client One',
                'payment_phone' => '5556661111',
                'balance' => 200.00,
                'nationality' => 'USA',
                'google_id' => null,
            ],
            [
                'type' => 'Client',
                'email' => 'client2@example.com',
                'name' => 'Client Two',
                'payment_phone' => '5556662222',
                'balance' => 150.00,
                'nationality' => 'Canada',
                'google_id' => null,
            ],
            [
                'type' => 'Client',
                'email' => 'client3@example.com',
                'name' => 'Client Three',
                'payment_phone' => '5556663333',
                'balance' => 250.00,
                'nationality' => 'UK',
                'google_id' => null,
            ],
            // Freelancers
            [
                'type' => 'Freelancer',
                'email' => 'freelancer1@example.com',
                'name' => 'Freelancer One',
                'payment_phone' => '9876541001',
                'balance' => 50.00,
                'nationality' => 'USA',
                'google_id' => Str::random(10),
            ],
            [
                'type' => 'Freelancer',
                'email' => 'freelancer2@example.com',
                'name' => 'Freelancer Two',
                'payment_phone' => '9876541002',
                'balance' => 60.00,
                'nationality' => 'Canada',
                'google_id' => Str::random(10),
            ],
            [
                'type' => 'Freelancer',
                'email' => 'freelancer3@example.com',
                'name' => 'Freelancer Three',
                'payment_phone' => '9876541003',
                'balance' => 70.00,
                'nationality' => 'UK',
                'google_id' => Str::random(10),
            ],
            [
                'type' => 'Freelancer',
                'email' => 'freelancer4@example.com',
                'name' => 'Freelancer Four',
                'payment_phone' => '9876541004',
                'balance' => 80.00,
                'nationality' => 'Germany',
                'google_id' => Str::random(10),
            ],
            [
                'type' => 'Freelancer',
                'email' => 'freelancer5@example.com',
                'name' => 'Freelancer Five',
                'payment_phone' => '9876541005',
                'balance' => 90.00,
                'nationality' => 'Australia',
                'google_id' => Str::random(10),
            ],
            [
                'type' => 'Freelancer',
                'email' => 'freelancer6@example.com',
                'name' => 'Freelancer Six',
                'payment_phone' => '9876541006',
                'balance' => 100.00,
                'nationality' => 'USA',
                'google_id' => Str::random(10),
            ],
            [
                'type' => 'Freelancer',
                'email' => 'freelancer7@example.com',
                'name' => 'Freelancer Seven',
                'payment_phone' => '9876541007',
                'balance' => 110.00,
                'nationality' => 'Canada',
                'google_id' => Str::random(10),
            ],
            [
                'type' => 'Freelancer',
                'email' => 'freelancer8@example.com',
                'name' => 'Freelancer Eight',
                'payment_phone' => '9876541008',
                'balance' => 120.00,
                'nationality' => 'UK',
                'google_id' => Str::random(10),
            ],
            [
                'type' => 'Freelancer',
                'email' => 'freelancer9@example.com',
                'name' => 'Freelancer Nine',
                'payment_phone' => '9876541009',
                'balance' => 130.00,
                'nationality' => 'Germany',
                'google_id' => Str::random(10),
            ],
            [
                'type' => 'Freelancer',
                'email' => 'freelancer10@example.com',
                'name' => 'Freelancer Ten',
                'payment_phone' => '9876541010',
                'balance' => 140.00,
                'nationality' => 'Australia',
                'google_id' => Str::random(10),
            ],
        ];

        foreach ($usersData as $data) {
            $user = User::create([
                'id' => (string) Str::uuid(),
                'type' => $data['type'],
                'email' => $data['email'],
                'password' => Hash::make('12345678'), // All passwords set to '12345678'
                'name' => $data['name'],
                'profile_picture' => null,
                'payment_phone' => $data['payment_phone'],
                'balance' => $data['balance'],
                'google_id' => $data['google_id'],
                'avatar' => null,
                'payment_history_id' => null,
                'nationality' => $data['nationality'],
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create the associated role record
            switch ($user->type) {
                case 'Freelancer':
                    Freelancer::create([
                        'freelancer_id' => $user->id,
                    ]);
                    break;
                case 'Client':
                    Client::create([
                        'client_id' => $user->id,
                    ]);
                    break;
                case 'Admin':
                    Admin::create([
                        'admin_id' => $user->id,
                    ]);
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid user type: {$user->type}");
            }
        }
    }
}
