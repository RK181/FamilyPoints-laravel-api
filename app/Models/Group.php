<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    /**
     * Funcion Group 1 - 0..* Tasks
     */
    public function tasks(): HasMany 
    { 
        return $this->hasMany(Task::class); 
    }

    /**
     * Funcion Group 1 - 0..* Rewards
     */
    public function rewards(): HasMany
    { 
        return $this->hasMany(Reward::class); 
    }

    /**
     * Funcion User 1 - 0..1 Group
     */
    public function creator(): BelongsTo
    { 
        return $this->belongsTo(User::class, 'creator_id'); 
    }

    /**
     * Funcion User 0..1 - 0..1 Group
     */
    public function couple(): BelongsToMany
    { 
        return $this->belongsToMany(User::class, 'group_user', 'group_id','couple_id'); 
    }
}
