<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'payment_method',
        'amount',
        'proof',
        'remarks',
    ];

    /**
     * Get the user that has the deposits.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
