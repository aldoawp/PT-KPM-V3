<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Kyslik\ColumnSortable\Sortable;

class Employee extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'address',
        'experience',
        'photo',
        'salary',
        'vacation',
        'city',
        'branch_id',
        'user_id',
    ];

    public $sortable = [
        'id',
        'name',
        'email',
        'phone',
        'salary',
        'city',
    ];

    protected $guarded = [
        'id'
    ];

    public function advance_salaries()
    {
        return $this->hasMany(AdvanceSalary::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('salary', 'like', '%' . $search . '%')
                ->orWhere('branches.region', 'like', '%' . $search . '%');
        });
    }
}
