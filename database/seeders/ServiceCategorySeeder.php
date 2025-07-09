<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Photography',
            'Videography',
            'Catering',
            'Venue Rental',
            'Makeup Artist',
            'Sound System',
            'Lights and Effects',
            'Host / Emcee',
            'Live Band / DJ',
            'Event Styling / Decoration'
        ];

        foreach ($categories as $name) {
            ServiceCategory::create(['name' => $name]);
        }
    }
}