<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Abonnement extends Model
{
    protected $table = 'abonnements';

    protected $fillable = [
        'user_id',
        'tier',
        'status',
        'start_date',
        'end_date',
        'auto_renew',
        'stripe_subscription_id',
        'payment_method',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
