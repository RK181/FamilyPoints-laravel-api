<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Funcion User 1 - 0..* Rewards
     */
    public function rewards(): HasMany
    { 
        return $this->hasMany(Reward::class); 
    }

    /**
     * Funcion User 0..1 - 0..* Tasks
     */
    public function tasks(): HasMany 
    { 
        return $this->hasMany(Task::class); 
    }

    /**
     * Funcion User 1 - 0..1 Group
     */
    public function userGroup(): HasOne
    { 
        return $this->hasOne(Group::class, 'creator_id'); 
    }

    /**
     * Funcion User 0..1 - 0..1 Group
     */
    public function coupleGroup(): BelongsToMany
    { 
        return $this->belongsToMany(Group::class, 'group_user', 'couple_id'); 
    }
}
