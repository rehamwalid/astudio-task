<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Language;
use App\Models\Location;
use App\Models\Category;
use App\Models\JobAttributeValue;
use App\Enums\JobType;
use App\Enums\JobStatus;

class Job extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => JobStatus::class,
        'type' => JobType::class,
    ];

    public function languages()
    {
        return $this->belongsToMany(Language::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'job_category');	
    }

    public function attributes()
    {
        return $this->hasMany(JobAttributeValue::class);
    }
}
