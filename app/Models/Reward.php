<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reward extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'group_id',
        'user_id',
    ];

    protected $casts = [
        'expite_at' => 'date:d/m/Y',
    ];

    /**
     * Relacion User 1 - 0..* Rewards
     */
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    /**
     * Relacion Group 1 - 0..* Rewards
     */
    public function group(): BelongsTo
    { 
        return $this->belongsTo(Group::class); 
    }
}
