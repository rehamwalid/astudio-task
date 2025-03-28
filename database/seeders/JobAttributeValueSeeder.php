<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\Attribute;
use App\Models\JobAttributeValue;

class JobAttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = Job::all();
        $attributes = Attribute::all();

        foreach ($jobs as $job) {
            foreach ($attributes as $attribute) {
                $value = $this->generateAttributeValue($attribute->type);

                JobAttributeValue::create([
                    'job_id' => $job->id,
                    'attribute_id' => $attribute->id,
                    'value' => $value,
                ]);
            }
        }
    }

    private function generateAttributeValue($type): mixed
    {
        return match ($type->value) {
            'number' => fake()->numberBetween(1, 10),
            'select' => fake()->randomElement(Attribute::where('type', 'select')->first()->options),
            'boolean' => fake()->boolean(),
            'text' => fake()->sentence(),
            default => null,
        };
    }
}