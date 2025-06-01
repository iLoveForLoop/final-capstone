<?php

namespace Database\Seeders;

use App\Models\User;
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

        $this->command->info('Roles and Users seeded successfully.');
    }
}
