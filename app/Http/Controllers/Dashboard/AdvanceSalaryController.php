<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\AdvanceSalary;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class AdvanceSalaryController extends Controller
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

        $advance_salary = [];

        if (auth()->user()->isSuperAdmin() || auth()->user()->isOwner()) {
            $advance_salary = AdvanceSalary::with(['employee']);
        } else {
            $advance_salary = AdvanceSalary::with(['employee'])
                ->join('employees AS e1', 'advance_salaries.employee_id', '=', 'e1.id')
                ->select('advance_salaries.*') // Ensure only advance_salaries columns are selected to avoid ambiguity
                ->where('e1.branch_id', auth()->user()->branch_id);
        }

        return view('advance-salary.index', [
            'advance_salaries' => $advance_salary->filter(request(['search']))
                ->sortable(['date' => 'desc'])
                ->paginate($row)->appends(request()->query()),
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('advance-salary.create', [
            'employees' => Employee::all()
                ->when(auth()->user()->branch_id != 1, fn($q) => $q
                    ->where('branch_id', auth()->user()->branch_id))
                ->sortBy('name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'employee_id' => 'required',
            'date' => 'required|date_format:Y-m-d|max:10',
            'advance_salary' => 'numeric|nullable'
        ];

        if ($request->date) {
            // format date only shows the year and month
            $getYm = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y-m');
        } else {
            $validatedData = $request->validate($rules);
        }


        $advanced = AdvanceSalary::where('employee_id', $request->employee_id)
            ->whereDate('date', 'LIKE', $getYm . '%')
            ->get();

        if ($advanced->isEmpty()) {
            $validatedData = $request->validate($rules);
            AdvanceSalary::create($validatedData);

            return Redirect::route('advance-salary.create')->with('success', 'Berhasil menambahkan gaji!');
        } else {
            return Redirect::route('advance-salary.create')->with('warning', 'Pinjaman sudah dibayar!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AdvanceSalary $advanceSalary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdvanceSalary $advanceSalary)
    {
        return view('advance-salary.edit', [
            'employees' => Employee::all()->sortBy('name'),
            'advance_salary' => $advanceSalary,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdvanceSalary $advanceSalary)
    {
        $rules = [
            'employee_id' => 'required',
            'date' => 'required|date_format:Y-m-d|max:10|',
            'advance_salary' => 'required|numeric'
        ];

        // format date only shows the YM (year and month)
        $newYm = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y-m');
        $oldYm = Carbon::createFromFormat('Y-m-d', $advanceSalary->date)->format('Y-m');

        $advanced = AdvanceSalary::where('employee_id', $request->id)
            ->whereDate('date', 'LIKE', $newYm . '%')
            ->first();

        if (!$advanced && $newYm == $oldYm) {
            $validatedData = $request->validate($rules);
            AdvanceSalary::where('id', $advanceSalary->id)->update($validatedData);

            return Redirect::route('pay-salary.index', $advanceSalary->id)->with('success', 'Pinjaman berhasil diubah!');
        } else {
            return Redirect::route('pay-salary.index', $advanceSalary->id)->with('warning', 'Pinjaman sudah dibayar!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdvanceSalary $advanceSalary)
    {
        AdvanceSalary::destroy($advanceSalary->id);

        return Redirect::route('advance-salary.index')->with('success', 'Gaji karyawan telah dihapus!');
    }
}
