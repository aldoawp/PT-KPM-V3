<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
    use HasFactory;

    public $fillable = [
        'branch_id',
        'supplier_id',
        'total',
        'user_id'
    ];

    public function returnDetails()
    {
        return $this->hasMany(ReturnDetail::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
