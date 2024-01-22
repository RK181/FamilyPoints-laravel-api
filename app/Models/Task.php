<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
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
    ];

    protected $casts = [
        'expite_at' => 'datetime:d/m/Y',
    ];

    /**
     * Funcion User 0..1 - 0..* Tasks
     * If NO user return NULL
     */
    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class)->withDefault(); 
    }

    /**
     * Funcion Group 1 - 0..* Tasks
     */
    public function group(): BelongsTo
    { 
        return $this->belongsTo(Group::class); 
    }
}
