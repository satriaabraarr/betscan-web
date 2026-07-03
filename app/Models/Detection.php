<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detection extends Model
{
    protected $fillable = [
        'user_category',
        'input_text',
        'input_image_path',
        'input_type',
        'nlp_result',
        'cnn_result',
        'content_detected',
        'user_vulnerability',
        'content_risk_level',
        'recommendation',
    ];

    protected $casts = [
        'content_detected' => 'boolean',
    ];
}
