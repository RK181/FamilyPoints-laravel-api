<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $casts = [
        'expite_at' => 'datetime:d/m/Y',
    ];

    /**
     * Funcion User 1 - 0..* Rewards
     */
    public function user() { 
        return $this->belongsTo(User::class); 
    }

    /**
     * Funcion Group 1 - 0..* Rewards
     */
    public function group() { 
        return $this->belongsTo(Group::class); 
    }
}
