<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restock extends Model
{
    use HasFactory;

    public $fillable = [
        'branch_id',
        'supplier_id',
        'total',
    ];

    public function restockDetails()
    {
        return $this->hasMany(RestockDetail::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
