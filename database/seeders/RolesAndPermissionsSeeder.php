<?php

namespace Database\Seeders;

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
            "password" => bcrypt("admin")

        ]);

        $admin->assignRole('admin');

        $client = User::create([
            "name" => "client",
            "email" => "client@email.com",
            "password" => bcrypt("client")
        ]);




        $client->assignRole('client');

        $vendor = User::create([
            "name" => "vendor",
            "email" => "vendor@email.com",
            "password" => bcrypt("vendor")
        ]);

        $vendor->assignRole('vendor');

        Vendor::create([
                'user_id' => $vendor->id,
                'business_name' => 'First Vendor',
                'location' => 'Amoa',
                'contact_number' => 'Secret'
            ]);



        $this->command->info('Roles and Users seeded successfully.');
    }
}
