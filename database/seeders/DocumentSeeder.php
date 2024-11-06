<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('documents')->insert([
            [
                'id' => Uuid::uuid4(),
                'name' => 'Barangay Clearance',
                'price' => 100
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Barangay Residency',
                'price' => 50
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Police Clearance',
                'price' => 150
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Barangay Certificate',
                'price' => 50
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Permit sa Baril',
                'price' => 300
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Cut Tree',
                'price' => 100
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Passport/Visa/Court',
                'price' => 200
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Business Clearance (A)',
                'price' => 200
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Business Clearance (B)',
                'price' => 300
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Business Clearance (C)',
                'price' => 500
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Business Clearance (D)',
                'price' => 1500
            ],
        ]);
    }
}
