<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Job;

class Language extends Model
{
    use HasFactory;

    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }
}
