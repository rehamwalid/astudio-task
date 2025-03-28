<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Language;
use App\Models\Location;
use App\Models\Category;
use App\Models\JobAttributeValue;

class Job extends Model
{
    use HasFactory;

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
        return $this->belongsToMany(Category::class);
    }

    public function attributes()
    {
        return $this->hasMany(JobAttributeValue::class);
    }
}
