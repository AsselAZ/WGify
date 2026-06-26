<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $fillable = [
        'name',
        'address',
        'currency',
        'invite_code',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}