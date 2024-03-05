<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class BarangayDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('barangay_details')->insert([
            [
                'id' => Uuid::uuid4(),
                'name' => 'Barangay Name',
                'image' => 'Image',
                'logo' => 'Logo'
            ]
        ]);
    }
}
