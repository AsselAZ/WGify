<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'apartment_id',
        'title',
        'assigned_to',
        'due_date',
        'status',
        'overdue_notification_sent',
    ];
}