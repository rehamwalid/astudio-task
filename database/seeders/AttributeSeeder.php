<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Enums\AttributeType;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attribute::insert([
            ['name' => 'years_experience', 'type' => AttributeType::NUMBER->value, 'options' => null],
            ['name' => 'degree_required', 'type' => AttributeType::SELECT->value, 'options' => json_encode(['Bachelor', 'Master', 'PhD'])],
            ['name' => 'remote_policy', 'type' => AttributeType::SELECT->value, 'options' => json_encode(['Fully Remote', 'Hybrid', 'On-Site'])],
            ['name' => 'is_urgent', 'type' => AttributeType::BOOLEAN->value, 'options' => null],
            ['name' => 'required_skills', 'type' => AttributeType::TEXT->value, 'options' => null],
        ]);
    }
}
    

