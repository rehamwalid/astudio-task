<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;
class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::insert([
            ['name' => 'PHP'],
            ['name' => 'JavaScript'],
            ['name' => 'Python'],
            ['name' => 'Java'],
            ['name' => 'C++'],
            ['name' => 'C#'],
            ['name' => 'TypeScript'],
            ['name' => 'Ruby'],
            ['name' => 'Swift'],
            ['name' => 'Go'],
        ]);
    }
}
