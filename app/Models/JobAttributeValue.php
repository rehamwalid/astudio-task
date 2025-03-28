<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attribute;
use App\Models\Job;
class JobAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = ['job_id', 'attribute_id', 'value'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
