<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\Language;
use App\Models\Location;
use App\Models\Category;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample jobs
        $jobs = Job::factory(20)->create();

        //Attach the job to random langs,locations and catgories
        foreach ($jobs as $job) {

            $languages = Language::inRandomOrder()->limit(rand(1, 3))->get();
            $job->languages()->attach($languages);

            $locations = Location::inRandomOrder()->limit(rand(1, 2))->get();
            $job->locations()->attach($locations);

            $category = Category::inRandomOrder()->first();
            $job->categories()->attach($category);
        }
    }
}