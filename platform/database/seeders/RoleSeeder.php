<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdmin = Role::create(['name' => 'super_admin']);
        $admin = Role::create(['name' => 'admin']);
        $finance = Role::create(['name' => 'finance']);
        $contentManager = Role::create(['name' => 'content_manager']);

        // Create 1 super admin user
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@deltaindo.co.id',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole($superAdmin);
    }
}
