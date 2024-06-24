<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class BranchController extends Controller
{
    public function index()
    {
        return view('branch.index', [
            'branches' => Branch::paginate(10)
        ]);
    }

    public function create()
    {
        return view('branch.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'region' => 'required|string|max:255|unique:branches,region',
        ];
        $validatedData = $request->validate($rules);        

        Branch::create($validatedData);

        return Redirect::route('branch.index')->with('success', 'Daerah operasional baru telah dibuat!');
    }

    public function edit(Branch $branch, $id)
    {
        return view('branch.edit', [
            'branch' => $branch->find($id)
        ]);
    }

    public function update(Request $request, Branch $branch)
    {
        dd();
    }

    public function destroy(Branch $branch, $id)
    {
        $branch = Branch::find($id);
        $branch->delete();
        return Redirect::route('branch.index')->with('success', 'Daerah operasional telah dihapus!');
    }

    
}
