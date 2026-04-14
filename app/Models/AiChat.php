<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChat extends Model
{
    protected $table = 'ai_chats';

    protected $fillable = [
        'user_id',
        'question',
        'answer',
    ];
}