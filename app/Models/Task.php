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
        'creator_id',
        'group_id',
        'user_id',
    ];

    protected $casts = [
        'expite_at' => 'date:d/m/Y',
    ];

    /**
     * Relacion User 0..1 - 0..* Tasks
     */
    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class)->withDefault(); 
    }

    /**
     * Relacion User 1 - 0..* Tasks
     */
    public function creator(): BelongsTo
    { 
        return $this->belongsTo(User::class, 'creator_id'); 
    }

    /**
     * Relacion Group 1 - 0..* Tasks
     */
    public function group(): BelongsTo
    { 
        return $this->belongsTo(Group::class); 
    }
}
