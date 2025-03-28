<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\jobAttributeValues;
use App\Enums\AttributeType;

class Attribute extends Model
{
    use HasFactory;

    use HasFactory;

    protected $fillable = ['name', 'type', 'options'];

    protected $casts = [
        'options' => 'array',
        'type' => AttributeType::class,
    ];

    public function jobAttributeValues()
    {
        return $this->hasMany(JobAttributeValue::class);
    }
}
