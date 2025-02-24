<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'cities'; // Đảm bảo tên bảng trong database là 'cities'
    protected $fillable = ['name']; // Danh sách cột có thể điền dữ liệu

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
