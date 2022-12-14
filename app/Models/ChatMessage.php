<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property int $chat_room_id
 */
class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'chat_room_id', 'message'];

    public function room() {
        return $this->hasOne('App\Models\ChatRoom', 'id', 'chat_room_id');
    }

    public function user() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
