<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'discount_price'];

    // Định nghĩa mối quan hệ nhiều-nhiều với sản phẩm
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }
}
