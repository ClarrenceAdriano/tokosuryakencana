<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'title',
        'status',
    ];

    public function chat_users()
    {
        return $this->hasMany(Chat_user::class,);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}