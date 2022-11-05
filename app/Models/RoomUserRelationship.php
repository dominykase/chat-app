<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomUserRelationship extends Model
{
    use HasFactory;
    protected $table = 'room_users';
    protected $fillable = ['room_id', 'user_id', 'is_muted', 'is_banned'];

    public function room() {
        return $this->hasOne('App\Models\ChatRoom', 'id', 'room_id');
    }

    public function user() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}