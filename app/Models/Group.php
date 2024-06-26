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
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'creator_id',
        'couple_id',
        'id',
    ];

    /**
     * Relacion Group 1 - 0..* Tasks
     */
    public function tasks(): HasMany 
    { 
        return $this->hasMany(Task::class); 
    }

    /**
     * Relacion Group 1 - 0..* Rewards
     */
    public function rewards(): HasMany
    { 
        return $this->hasMany(Reward::class); 
    }

    /**
     * Relacion User 1 - 0..1 Group
     */
    public function creator(): BelongsTo
    { 
        return $this->belongsTo(User::class, 'creator_id'); 
    }

    /**
     * Relacion User 0..1 - 0..1 Group
     */
    public function couple(): BelongsTo
    { 
        return $this->belongsTo(User::class, 'couple_id'); 
    }
}
