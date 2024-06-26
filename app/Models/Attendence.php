<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Attendence extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'employee_id',
        'date',
        'status',
    ];

    public $sortable = [
        'employee_id',
        'date',
        'status',
    ];

    protected $guarded = [
        'id'
    ];

    protected $with = [
        'employee',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'branch_id', 'id');
    }

    public function getRouteKeyName()
    {
        return 'date';
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->leftJoin('branches', 'attendences.branch_id', '=', 'branches.id')
                ->join('users AS u1', 'attendences.branch_id', '=', 'u1.branch_id')
                ->orWhere('branches.region', 'like', '%' . $search . '%');
        });
    }
}
