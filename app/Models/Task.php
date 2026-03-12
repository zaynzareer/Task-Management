<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    use SoftDeletes;

    // These fields can be set using mass assignment (create/update).
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'priority',
        'user_id',
    ];

    // Cast values so API responses are consistent.
    protected $casts = [
        'due_date' => 'date',
        'priority' => 'integer',
    ];

    public function user(): BelongsTo
    {
        // Every task belongs to one user.
        return $this->belongsTo(User::class);
    }
}
