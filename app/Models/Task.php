<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'due_date',
        'file_url',
        'is_completed',
        'user_id',
    ];
}
