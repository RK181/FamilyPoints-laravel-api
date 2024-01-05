<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    public function rewards() { 
        return $this->hasMany(Reward::class); 
    }

    /**
     * Funcion User 0..1 - 0..* Tasks
     */
    public function tasks() { 
        return $this->hasMany(Task::class); 
    }

    /**
     * Funcion User 1 - 0..1 Group
     */
    public function userGroup() { 
        return $this->hasOne(Group::class); 
    }

    /**
     * Funcion User 0..1 - 0..1 Group
     */
    public function coupleGroup() { 
        return $this->belongsTo(Group::class); 
    }
}
