<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Order;
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
        // Create permissions
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
        Permission::create(['name' => 'branch.menu', 'group_name' => 'branch']);
        Permission::create(['name' => 'report.menu', 'group_name' => 'report']);

        // Create user roles
        Role::create(['name' => 'SuperAdmin'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'Owner'])->givePermissionTo(['pos.menu', 'employee.menu', 'customer.menu', 'supplier.menu', 'salary.menu', 'category.menu', 'product.menu', 'orders.menu', 'stock.menu', 'attendence.menu', 'user.menu', 'branch.menu', 'report.menu']);
        Role::create(['name' => 'ASS'])->givePermissionTo(['pos.menu', 'employee.menu', 'customer.menu', 'supplier.menu', 'salary.menu', 'category.menu', 'product.menu', 'orders.menu', 'stock.menu', 'attendence.menu', 'user.menu']);
        Role::create(['name' => 'Sales'])->givePermissionTo(['pos.menu', 'customer.menu', 'orders.menu']);

        // Create branches
        for ($i = 0; $i < 8; $i++) {
            Branch::create([
                "region" => ['malang', 'bekasi', 'bogor', 'tangerang', "cilegon", 'semarang', 'bandung', 'surabaya'][$i]
            ]);
        }

        // Create account with different roles
        $admin = User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => '$2y$10$6c2Yc4tuj1Sqj8AqosGUQ.EbzkLk9qW77JkOPOuSGYO3UoU6KLDEe',
            'role_id' => 1,
            'branch_id' => 1,
        ]);
        $owner = User::factory()->create([
            'name' => 'Suk Lok',
            'username' => 'suklok',
            'email' => 'suklok@gmail.com',
            'password' => '$2y$10$6c2Yc4tuj1Sqj8AqosGUQ.EbzkLk9qW77JkOPOuSGYO3UoU6KLDEe',
            'role_id' => 2,
            'branch_id' => 1,
        ]);
        $manager1 = User::factory()->create([
            'name' => 'Victor',
            'username' => 'victor',
            'email' => 'victor@gmail.com',
            'password' => '$2y$10$6c2Yc4tuj1Sqj8AqosGUQ.EbzkLk9qW77JkOPOuSGYO3UoU6KLDEe',
            'role_id' => 3,
            'branch_id' => 8,
        ]);
        $manager2 = User::factory()->create([
            'name' => 'Poltak',
            'username' => 'poltak',
            'email' => 'poltak@gmail.com',
            'password' => '$2y$10$6c2Yc4tuj1Sqj8AqosGUQ.EbzkLk9qW77JkOPOuSGYO3UoU6KLDEe',
            'role_id' => 3,
            'branch_id' => 2,
        ]);
        $manager3 = User::factory()->create([
            'name' => 'Elita',
            'username' => 'elita',
            'email' => 'elita@gmail.com',
            'password' => '$2y$10$6c2Yc4tuj1Sqj8AqosGUQ.EbzkLk9qW77JkOPOuSGYO3UoU6KLDEe',
            'role_id' => 3,
            'branch_id' => 6,
        ]);
        $sales1 = User::factory()->create([
            'name' => 'Budi',
            'username' => 'budi',
            'email' => 'budi@gmail.com',
            'password' => '$2y$10$6c2Yc4tuj1Sqj8AqosGUQ.EbzkLk9qW77JkOPOuSGYO3UoU6KLDEe',
            'role_id' => 4,
            'branch_id' => 8,
        ]);
        $sales2 = User::factory()->create([
            'name' => 'Santoso',
            'username' => 'santoso',
            'email' => 'santoso@gmail.com',
            'password' => '$2y$10$6c2Yc4tuj1Sqj8AqosGUQ.EbzkLk9qW77JkOPOuSGYO3UoU6KLDEe',
            'role_id' => 4,
            'branch_id' => 2,
        ]);
        $sales3 = User::factory()->create([
            'name' => 'Hadi',
            'username' => 'hadi',
            'email' => 'hadi@gmail.com',
            'password' => '$2y$10$6c2Yc4tuj1Sqj8AqosGUQ.EbzkLk9qW77JkOPOuSGYO3UoU6KLDEe',
            'role_id' => 4,
            'branch_id' => 6,
        ]);

        // Assign roles to each users
        $admin->assignRole('SuperAdmin');
        $owner->assignRole('Owner');
        $manager1->assignRole('ASS');
        $manager2->assignRole('ASS');
        $manager3->assignRole('ASS');
        $sales1->assignRole('Sales');
        $sales2->assignRole('Sales');
        $sales3->assignRole('Sales');

        // Create default supplier
        Supplier::factory()->create([
            'name' => 'PT Karyamega Putra Mandiri',
            'email' => 'karyamegaputramandiri@yahoo.com',
            'phone' => '081234567890',
            'shopname' => 'PT Karyamega Putra Mandiri',
            'type' => 'Manufaktur',
            'account_holder' => '',
            'account_number' => '',
            'bank_name' => '',
            'bank_branch' => '',
            'city' => 'Malang',
            'address' => 'Jl. Raya Karangpandan No.368, Bendo, Karangpandan, Kec. Pakisaji, Kabupaten Malang, Jawa Timur 65162',
        ]);

        // Create default product categories
        Category::factory()->create(['name' => 'Rokok Putih']);
        Category::factory()->create(['name' => 'Rokok Kretek']);
        Category::factory()->create(['name' => 'Rokok Klembak']);
        Category::factory()->create(['name' => 'Sigaret Kretek Tangan']);
        Category::factory()->create(['name' => 'Sigaret Kretek Mesin']);
        Category::factory()->create(['name' => 'Rokok Filter']);
        Category::factory()->create(['name' => 'Rokok Non-Filter']);
        
        Employee::factory(100)->create();
        Customer::factory(100)->create();
        for ($i = 0; $i < 100; $i++) {
            Product::factory()->create([
                'product_code' => IdGenerator::generate([
                    'table' => 'products',
                    'field' => 'product_code',
                    'length' => 4,
                    'prefix' => 'PC'
                ])
            ]);
        }
        // Order::factory(100)->create();

    }
}
