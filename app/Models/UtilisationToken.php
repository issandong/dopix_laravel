<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilisationToken extends Model
{
    protected $table = 'utilisation_tokens';

    protected $fillable = [
        'user_id',
        'period',
        'medicine_name',
        'image_url',
        'ingredients',
        'overall_status',
        'check_date',
        'source',
        'ai_confidence',
        'tokens_used',
        'estimated_cost',
    ];

    protected $casts = [
        'ingredients' => 'array', // [{ name, status, details, detectionTime }]
        'check_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
