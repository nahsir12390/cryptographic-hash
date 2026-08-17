<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HashInteraction extends Model
{
    protected $fillable = [
        'action',
        'algorithm',
        'input_preview',
        'second_input_preview',
        'hash_output',
        'second_hash_output',
        'differing_bits',
        'differing_percentage',
        'is_collision',
    ];

    protected $casts = [
        'is_collision' => 'boolean',
        'differing_percentage' => 'float',
    ];
}
