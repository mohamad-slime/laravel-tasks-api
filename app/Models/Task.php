<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'user_id',
        'due_date',
        'completed_at',
    ];

    protected $dates = [
        'due_date',
        'completed_at',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Query Scopes
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        });

        $query->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        });

        $query->when($filters['priority'] ?? null, function ($query, $priority) {
            $query->where('priority', $priority);
        });

        $query->when($filters['due_date'] ?? null, function ($query, $dueDate) {
            $query->whereDate('due_date', $dueDate);
        });

        return $query;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('due_date', '>=', now())
            ->where('status', '!=', self::STATUS_COMPLETED)
            ->orderBy('due_date');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', self::STATUS_COMPLETED);
    }

    // Status management methods
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now()
        ]);
    }

    public function markAsInProgress(): bool
    {
        return $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'completed_at' => null
        ]);
    }

    public function markAsPending(): bool
    {
        return $this->update([
            'status' => self::STATUS_PENDING,
            'completed_at' => null
        ]);
    }

    // Helper methods
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isOverdue(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        return !$this->isCompleted() && $this->due_date->isPast();
    }
}
