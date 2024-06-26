<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Order extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'customer_id',
        'order_status',
        'total_products',
        'sub_total',
        'vat',
        'invoice_no',
        'total',
        'payment_status',
        'pay',
        'due',
        'branch_id',
        'user_id'
    ];

    public $sortable = [
        'customer_id',
        'created_at',
        'pay',
        'due',
        'total',
        'branch_id',
        'user_id'
    ];

    protected $guarded = [
        'id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->leftJoin('branches', 'branches.id', '=', 'customers.branch_id')
                ->leftJoin('users', 'users.id', '=', 'orders.user_id')
                ->where('invoice_no', 'like', '%' . $search . '%')
                ->orWhere('payment_status', 'like', '%' . $search . '%')
                ->orWhere('customers.name', 'like', '%' . $search . '%')
                ->orWhere('branches.region', 'like', '%' . $search . '%')
                ->orWhere('users.name', 'like', '%' . $search . '%');
        });
    }
}
