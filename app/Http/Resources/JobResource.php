<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'company_name' => $this->company_name,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'is_remote' => (bool) $this->is_remote,
            'job_type' => $this->type, 
            'status' => $this->status, 
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'languages' => $this->languages->map(function ($language) {
                return [
                    'name' => $language->name,
                ];
            }),
            'locations' => $this->locations->map(function ($location) {
                return [
                    'city' => $location->city,
                    'state' => $location->state,
                    'country' => $location->country,
                ];
            }),
            'categories' => $this->categories->map(function ($category) {
                return [
                    'name' => $category->name,
                ];
            }),
            'attributes' => $this->attributes->map(function ($attribute) {
                return [
                    'name' => $attribute->attribute->name,
                    'value' => $attribute->value,       
                ];
            }),
        ];
    }
}
