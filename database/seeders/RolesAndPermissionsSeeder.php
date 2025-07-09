<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ["admin", "client", "vendor"];

        foreach($roles as $role){
            Role::create(['name' => $role]);
        }

        $admin = User::create([
            "name" => "admin",
            "email" => "admin@email.com",
            // "is_approved" => true,
            "password" => bcrypt("admin")

        ]);

        $admin->assignRole('admin');

        $client = User::create([
            "name" => "client",
            "email" => "client@email.com",
            // "is_approved" => true,
            "password" => bcrypt("client")
        ]);

        Client::create([
                'user_id' => $client->id,
                'full_name' => $client->name,
                'contact_number' => 'not available',
                'location' => 'ilaha'
            ]);




        $client->assignRole('client');

        $vendor = User::create([
            "name" => "vendor",
            "email" => "vendor@email.com",
            // "is_approved" => true,
            "password" => bcrypt("vendor")
        ]);

        $vendor->assignRole('vendor');

        Vendor::create([
                'user_id' => $vendor->id,
                'full_name' => $vendor->name,
                'business_name' => 'First Vendor',
                'location' => 'Amoa',
                'contact_number' => 'Secret',
                "is_approved" => true,
            ]);

        $vendor->vendor->serviceCategories()->attach([1, 3]);



        $this->command->info('Roles and Users seeded successfully.');
    }
}
