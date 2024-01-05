<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * Funcion Group 1 - 0..* Tasks
     */
    public function tasks() { 
        return $this->hasMany(Task::class); 
    }

    /**
     * Funcion Group 1 - 0..* Rewards
     */
    public function rewards() { 
        return $this->hasMany(Reward::class); 
    }

    /**
     * Funcion User 1 - 0..1 Group
     */
    public function creator() { 
        return $this->belongsTo(User::class, 'creator_id'); 
    }

    /**
     * Funcion User 0..1 - 0..1 Group
     */
    public function couple() { 
        return $this->belongsTo(User::class, 'couple_id'); 
    }
}
