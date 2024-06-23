<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\AdvanceSalary;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        for ($i=1; $i <= 4; $i++) {
            Branch::create([
                'id' => $i,
                "region" => ['tangerang', 'surabaya', 'bandung', "jakarta"][$i-1]
            ]);
        }
        
        $admin = \App\Models\User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('1234'),
        ]);

        Employee::factory(5)->create();
        Customer::factory(25)->create();
        Supplier::factory(10)->create();
        Category::factory(5)->create();
        for ($i=0; $i < 10; $i++) {
            Product::factory()->create([
                'product_code' => IdGenerator::generate([
                    'table' => 'products',
                    'field' => 'product_code',
                    'length' => 4,
                    'prefix' => 'PC'
                ])
            ]);
        }

        Permission::create(['name' => 'pos.menu', 'group_name' => 'pos']);
        Permission::create(['name' => 'employee.menu', 'group_name' => 'employee']);
        Permission::create(['name' => 'customer.menu', 'group_name' => 'customer']);
        Permission::create(['name' => 'supplier.menu', 'group_name' => 'supplier']);
        Permission::create(['name' => 'salary.menu', 'group_name' => 'salary']);
        Permission::create(['name' => 'attendence.menu', 'group_name' => 'attendence']);
        Permission::create(['name' => 'category.menu', 'group_name' => 'category']);
        Permission::create(['name' => 'product.menu', 'group_name' => 'product']);
        Permission::create(['name' => 'orders.menu', 'group_name' => 'orders']);
        Permission::create(['name' => 'stock.menu', 'group_name' => 'stock']);
        Permission::create(['name' => 'roles.menu', 'group_name' => 'roles']);
        Permission::create(['name' => 'user.menu', 'group_name' => 'user']);
        Permission::create(['name' => 'database.menu', 'group_name' => 'database']);

        Role::create(['name' => 'SuperAdmin'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'Owner'])->givePermissionTo(['pos.menu', 'employee.menu', 'customer.menu', 'supplier.menu', 'salary.menu', 'category.menu', 'product.menu', 'orders.menu', 'stock.menu', 'attendence.menu', 'user.menu']);
        Role::create(['name' => 'Manager'])->givePermissionTo(['pos.menu', 'employee.menu', 'customer.menu', 'supplier.menu', 'salary.menu', 'category.menu', 'product.menu', 'orders.menu', 'stock.menu', 'attendence.menu']);
        Role::create(['name' => 'Sales'])->givePermissionTo(['pos.menu']);

        $admin->assignRole('SuperAdmin');
    }
}
