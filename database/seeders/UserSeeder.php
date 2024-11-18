<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name','Administrator')->first();

        if($role)
        {
            User::create([
                'id' => Uuid::uuid4(),
                'firstname' => 'Admin',
                'lastname' => 'Account',
                'email' => 'admin@email.com',
                'password' => Hash::make('admin123'),
                'role_id' => $role->id,
                'archive_status' => false,
            ]);
        }
     else {
        error_log('Role "Administrator" not found.');
    }
    }
}
