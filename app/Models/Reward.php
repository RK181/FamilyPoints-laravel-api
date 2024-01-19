<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reward extends Model
{
    use HasFactory;

    protected $casts = [
        'expite_at' => 'datetime:d/m/Y',
    ];

    /**
     * Funcion User 1 - 0..* Rewards
     */
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    /**
     * Funcion Group 1 - 0..* Rewards
     */
    public function group(): BelongsTo
    { 
        return $this->belongsTo(Group::class); 
    }
}