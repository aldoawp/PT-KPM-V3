<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Employee;
use App\Models\PaySalary;
use Illuminate\Http\Request;
use App\Models\AdvanceSalary;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class PaySalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The row parameter must be an integer between 1 and 100.');
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

        if (request('search')) {
            Employee::firstWhere('name', request('search'));
        }

        return view('pay-salary.index', [
            'advanceSalaries' => $advance_salary->filter(request(['search']))
                ->sortable(['date' => 'desc'])
                ->paginate($row)->appends(request()->query()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function paySalary(string $id)
    {
        return view('pay-salary.create', [
            'advanceSalary' => AdvanceSalary::with(['employee'])
                ->where('id', $id)
                ->first(),
        ]);
    }

    public function payHistory()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The row parameter must be an integer between 1 and 100.');
        }

        if (request('search')) {
            Employee::firstWhere('name', request('search'));
        }

        $pay_salary = [];

        if (auth()->user()->isSuperAdmin() || auth()->user()->isOwner()) {
            $pay_salary = PaySalary::with(['employee'])
                ->leftJoin('employees AS e1', 'pay_salaries.employee_id', '=', 'e1.id');
        } else {
            $pay_salary = PaySalary::with(['employee'])
                ->leftJoin('employees AS e1', 'pay_salaries.employee_id', '=', 'e1.id')
                ->where('e1.branch_id', auth()->user()->branch_id);
        }

        return view('pay-salary.history', [
            'paySalaries' => $pay_salary
                ->select('pay_salaries.*', 'e1.id as employee_id', 'e1.name as employee_name') // Select necessary columns with aliases
                ->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    public function payHistoryDetail(string $id)
    {
        return view('pay-salary.history-details', [
            'paySalary' => PaySalary::with(['employee'])
                ->where('employee_id', $id)
                ->first(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'date' => 'required|date_format:Y-m-d|max:10',
        ];

        $paySalary = AdvanceSalary::with(['employee'])
            ->where('id', $request->id)
            ->first();

        $validatedData = $request->validate($rules);

        $validatedData['employee_id'] = $paySalary->employee_id;
        $validatedData['paid_amount'] = $paySalary->employee->salary;
        $validatedData['advance_salary'] = $paySalary->advance_salary;
        $validatedData['due_salary'] = $paySalary->employee->salary - $paySalary->advance_salary;

        PaySalary::create($validatedData);

        return Redirect::route('pay-salary.payHistory')->with('success', 'Employee Salary Paid Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaySalary $paySalary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaySalary $paySalary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaySalary $paySalary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaySalary $paySalary)
    {
        PaySalary::destroy($paySalary->id);

        return Redirect::route('pay-salary.payHistory')->with('success', 'Employee History Pay Salary has been deleted!');
    }
}
