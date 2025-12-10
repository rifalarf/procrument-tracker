<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableColumn extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'options' => 'array',
        'is_visible' => 'boolean',
        'is_dynamic' => 'boolean',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
