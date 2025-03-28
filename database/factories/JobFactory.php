<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\JobType;
use App\Enums\JobStatus; 
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                'title' => fake()->jobTitle(),
                'description' => fake()->paragraphs(3, true),
                'company_name' => fake()->company(),
                'salary_min' => fake()->numberBetween(50000, 100000),
                'salary_max' => fake()->numberBetween(100000, 200000),
                'is_remote' => fake()->boolean(),
                'job_type' => fake()->randomElement(JobType::cases()),
                'status' => fake()->randomElement(JobStatus::cases()), 
                'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            ];
    }
}
