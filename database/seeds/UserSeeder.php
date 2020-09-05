<?php

use RoleModule\Role;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $row = [
            'first_name' => 'Vanguard',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => 'admin123',
            'avatar' => null,
            'country_id' => null,
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => now(),
        ];

        $responses = event("seeding.users", [$row]);

        foreach ($responses as $response){
            $row = $row + $response;
        }
        User::create($row);
    }
}
