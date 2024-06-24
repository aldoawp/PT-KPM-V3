<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        return view('employees.index', [
            'employees' => Employee::filter(request(['search']))->sortable()->paginate($row)->appends(request()->query()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create', [
            'branches' => Branch::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'photo' => 'image|file|max:1024',
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:employees,email',
            'phone' => 'required|string|max:15|unique:employees,phone',
            'experience' => 'string|nullable',
            'salary' => 'required|numeric',
            'vacation' => 'max:50|nullable',
            'branch_id' => 'required|exists:branches,id',
            'address' => 'required|max:100',
        ];

        // Validate request data
        $validatedData = $request->validate($rules);

        // Add the current user ID to the validated data
        $validatedData['user_id'] = auth()->user()->id;

        // Handle photo upload if a file is provided
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = 'public/employees/';
            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        // Create a new employee record
        Employee::create($validatedData);

        // Redirect to the employees index page with a success message
        return Redirect::route('employees.index')->with('success', 'Employee has been created!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', [
            'employee' => $employee,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', [
            'employee' => $employee,
            'branches' => Branch::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $rules = [
            'photo' => 'image|file|max:1024',
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:20|unique:employees,phone,' . $employee->id,
            'experience' => 'string|nullable',
            'salary' => 'numeric',
            'vacation' => 'max:50|nullable',
            'branch_id' => 'required|exists:branches,id',
            'address' => 'required|max:100',
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload image with Storage.
         */
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = 'public/employees/';

            /**
             * Delete photo if exists.
             */
            if ($employee->photo) {
                Storage::delete($path . $employee->photo);
            }

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        Employee::where('id', $employee->id)->update($validatedData);

        return Redirect::route('employees.index')->with('success', 'Employee has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        /**
         * Delete photo if exists.
         */
        if ($employee->photo) {
            Storage::delete('public/employees/' . $employee->photo);
        }

        Employee::destroy($employee->id);

        return Redirect::route('employees.index')->with('success', 'Employee has been deleted!');
    }
}
