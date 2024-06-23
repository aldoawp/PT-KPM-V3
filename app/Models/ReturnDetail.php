<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnDetail extends Model
{
    use HasFactory;

    public $fillable = [
        'return_id',
        'product_id',
        'quantity',
    ];

    public $timestamps = false;

    public function returns()
    {
        return $this->belongsTo(Returns::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
