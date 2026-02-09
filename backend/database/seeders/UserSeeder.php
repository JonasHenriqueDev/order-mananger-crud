<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        $admin = User::create([
            'first_name' => 'Jonas',
            'last_name' => 'Henrique',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'active' => true,
        ]);

        $admin->phones()->create([
            'country_code' => '+55',
            'number' => '11987654321',
            'type' => 'mobile',
            'is_primary' => true,
        ]);

        $admin->address()->create([
            'street' => 'Rua das Flores',
            'number' => '123',
            'complement' => 'Apto 456',
            'district' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01310-100',
            'country' => 'BR',
            'is_primary' => true,
        ]);

        // Manager User
        $manager = User::create([
            'first_name' => 'Maria',
            'last_name' => 'Silva',
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'active' => true,
        ]);

        $manager->phones()->create([
            'country_code' => '+55',
            'number' => '11998765432',
            'type' => 'mobile',
            'is_primary' => true,
        ]);

        $manager->address()->create([
            'street' => 'Avenida Paulista',
            'number' => '1000',
            'complement' => 'Sala 200',
            'district' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01310-100',
            'country' => 'BR',
            'is_primary' => true,
        ]);

        $manager->address()->create([
            'street' => 'Rua Augusta',
            'number' => '2000',
            'complement' => null,
            'district' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01305-100',
            'country' => 'BR',
            'is_primary' => false,
        ]);

        // Regular User
        $user = User::create([
            'first_name' => 'João',
            'last_name' => 'Santos',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'active' => true,
        ]);

        $user->phones()->create([
            'country_code' => '+55',
            'number' => '21987654321',
            'type' => 'mobile',
            'is_primary' => true,
        ]);

        $user->address()->create([
            'street' => 'Rua do Ouvidor',
            'number' => '50',
            'complement' => null,
            'district' => 'Centro',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'postal_code' => '20040-030',
            'country' => 'BR',
            'is_primary' => true,
        ]);

        // Inactive User
        $inactive = User::create([
            'first_name' => 'Pedro',
            'last_name' => 'Oliveira',
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'active' => false,
        ]);

        $inactive->phones()->create([
            'country_code' => '+55',
            'number' => '85987654321',
            'type' => 'mobile',
            'is_primary' => true,
        ]);

        $inactive->address()->create([
            'street' => 'Rua Dragão do Mar',
            'number' => '300',
            'complement' => null,
            'district' => 'Praia de Iracema',
            'city' => 'Fortaleza',
            'state' => 'CE',
            'postal_code' => '60060-140',
            'country' => 'BR',
            'is_primary' => true,
        ]);
    }
}


