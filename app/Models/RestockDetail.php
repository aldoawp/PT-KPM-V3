<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockDetail extends Model
{
    use HasFactory;

    public $fillable = [
        'restock_id',
        'product_id',
        'quantity',
    ];

    public $timestamps = false;

    public function restock()
    {
        return $this->belongsTo(Restock::class, 'restock_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
