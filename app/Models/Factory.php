<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'supplied_quantity',
        'start_date',
        'end_date',
        'delivery_date',
        'receiver_name',
        'manager_user_id',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }
}