<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'task';
    protected $status = ['new', 'in_progress', 'done'];
    protected $fillable = ['title','description','status_id','user_id', 'priority'];

    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
