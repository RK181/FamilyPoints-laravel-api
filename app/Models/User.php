<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
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
        'created_at',
        'updated_at',
        'id',
        'email_verified_at',
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
     * Relacion User 1 - 0..* Rewards
     */
    public function rewards(): HasMany
    { 
        return $this->hasMany(Reward::class); 
    }

    /**
     * Relacion User(Creator) 1 - 0..* Tasks
     */
    public function tasks(): HasMany 
    { 
        return $this->hasMany(Task::class, 'creator_id'); 
    }

    /**
     * Relacion User 0..1 - 0..* Tasks
     */
    public function completedTasks(): HasMany 
    { 
        return $this->hasMany(Task::class); 
    }

    /**
     * Relacion User(Creator) 1 - 0..1 Group
     * Relacion User(Couple) 0..1 - 0..1 Group (el grupo contiene el id de la pareja)
     */
    public function group(): HasOne
    {
        $group = $this->hasOne(Group::class, 'creator_id')->withDefault();
        if ($group->exists()) {
            return $group;
        }
        return $this->hasOne(Group::class, 'couple_id')->withDefault();
    }
}
