<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'receiver_id', 'message', 'is_admin'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
