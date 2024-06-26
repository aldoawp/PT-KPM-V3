<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->isSalesRole()) {
            return abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $customers = [];

        if (auth()->user()->isSuperAdmin() || auth()->user()->isOwner()) {
            $customers = Customer::filter(request(['search']));
        } else {
            $customers =
                auth()->user()->branch->customers()->filter(request(['search']));
        }

        return view('customers.index', [
            'customers' => $customers
                ->with('branch')
                ->sortable()
                ->paginate($row)
                ->appends(request()->query())
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->has('previous_url') && !auth()->user()->isSalesRole()) {
            Session::put('previous_url', $request->query('previous_url'));
        } else {
            Session::put('previous_url', route('pos.salesPos'));
        }

        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'photo' => 'image|file|max:1024',
            'name' => 'required|string|max:50',
            // 'email' => 'email|max:50|unique:customers,email',
            'phone' => 'required|string|max:15|unique:customers,phone',
            'shopname' => 'required|string|max:50',
            'account_holder' => 'max:50',
            'account_number' => 'max:25',
            'bank_name' => 'max:25',
            'bank_branch' => 'max:50',
            'city' => 'required|string|max:50',
            'address' => 'required|string|max:100',
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload image with Storage.
         */
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = 'public/customers/';

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        $validatedData['branch_id'] = auth()->user()->branch_id;

        Customer::create($validatedData);

        // Check if there is a 'previous_url' in the session
        // if ($request->session()->has('previous_url')) {
        //     $previousUrl = $request->session()->get('previous_url');
        //     $request->session()->forget('previous_url');
        //     return redirect($previousUrl)->with('success', 'Pelanggan telah dibuat!')->withInput();
        // }

        // Redirect for when the user is Sales
        if (auth()->user()->isSalesRole()) {
            return Redirect::route('pos.salesPos')->with('success', 'Pelanggan telah dibuat!');
        }

        // Redirect for when the user is not Sales
        if (url()->previous() == 'http://127.0.0.1:8000/customers/create?previous_url=http%3A%2F%2F127.0.0.1%3A8000%2Fpos%2Fsales') {
            return Redirect::route('pos.salesPos')->with('success', 'Pelanggan telah dibuat!');
        } else {
            return Redirect::route('customers.index')->with('success', 'Pelanggan telah dibuat!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        if (auth()->user()->isSalesRole()) {
            return abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        return view('customers.show', [
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        if (auth()->user()->isSalesRole()) {
            return abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        return view('customers.edit', [
            'customer' => $customer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        if (auth()->user()->isSalesRole()) {
            return abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        $rules = [
            'photo' => 'image|file|max:1024',
            'name' => 'required|string|max:50',
            'email' => 'email|max:50|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:15|unique:customers,phone,' . $customer->id,
            'shopname' => 'required|string|max:50',
            'account_holder' => 'max:50',
            'account_number' => 'max:25',
            'bank_name' => 'max:25',
            'bank_branch' => 'max:50',
            'city' => 'required|string|max:50',
            'address' => 'required|string|max:100',
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload image with Storage.
         */
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = 'public/customers/';

            /**
             * Delete photo if exists.
             */
            if ($customer->photo) {
                Storage::delete($path . $customer->photo);
            }

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        Customer::where('id', $customer->id)->update($validatedData);

        return Redirect::route('customers.index')->with('success', 'Pelanggan telah diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        if (auth()->user()->isSalesRole()) {
            return abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        /**
         * Delete photo if exists.
         */
        if ($customer->photo) {
            Storage::delete('public/customers/' . $customer->photo);
        }

        Customer::destroy($customer->id);

        return Redirect::route('customers.index')->with('success', 'Pelanggan telah dihapus!');
    }
}
