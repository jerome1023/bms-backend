<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role')->insert([
            [
                'id' => Uuid::uuid4(),
                'name' => 'Administrator'
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'User'
            ]
        ]);
    }
}
