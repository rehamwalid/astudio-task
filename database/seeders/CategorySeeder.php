<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            ['name' => 'Web Development'],
            ['name' => 'Mobile Development'],
            ['name' => 'Data Science'],
            ['name' => 'Software Engineering'],
            ['name' => 'Design'],
            ['name' => 'Marketing'],
            ['name' => 'Sales'],
            ['name' => 'Human Resources'],
            ['name' => 'Finance'],
            ['name' => 'Project Management'],
        ]);
    }
}
