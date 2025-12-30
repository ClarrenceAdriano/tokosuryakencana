<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat_user extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
