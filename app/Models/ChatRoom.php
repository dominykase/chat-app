<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $is_private
 * @property string $name
 * @property int $id
 */
class ChatRoom extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'name', 'is_private'];

    public function messages() {
        return $this->hasMany('App\Models\ChatMessage');
    }

}
