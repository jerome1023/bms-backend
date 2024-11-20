<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            ['name' => 'Barangay Clearance', 'price' => 100],
            ['name' => 'Barangay Residency', 'price' => 50],
            ['name' => 'Police Clearance', 'price' => 150],
            ['name' => 'Barangay Certificate', 'price' => 50],
            ['name' => 'Permit sa Baril', 'price' => 300],
            ['name' => 'Cut Tree', 'price' => 100],
            ['name' => 'Passport/Visa/Court', 'price' => 200],
            ['name' => 'Business Clearance (A)', 'price' => 200],
            ['name' => 'Business Clearance (B)', 'price' => 300],
            ['name' => 'Business Clearance (C)', 'price' => 500],
            ['name' => 'Business Clearance (D)', 'price' => 1500],
        ];

        foreach ($documents as $document) {
            Document::create([
                'id' => Uuid::uuid4(),
                'name' => $document['name'],
                'price' => $document['price'],
            ]);
        }
    }
}
