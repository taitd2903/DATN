<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'user_id', 'reason', 'image', 'status','return_process_status','rejection_reason','return_request_status','bank_account',
        'account_holder',
        'bank_name',];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    
}
