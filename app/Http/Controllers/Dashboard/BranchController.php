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
            'address' => 'required|string|max:255',
        ];

        $validatedData = $request->validate($rules);

        Branch::create($validatedData);

        return Redirect::route('branch.index')->with('success', 'Daerah operasional baru telah dibuat!');
    }

    public function edit(int $id)
    {
        return view('branch.edit', [
            'branch' => Branch::find($id)
        ]);
    }

    public function update(Request $request, int $id)
    {
        $rules = [
            'region' => 'required|string|max:255|unique:branches,region',
            'address' => 'required|string|max:255',
        ];

        $validatedData = $request->validate($rules);

        $branch = Branch::find($id);

        $branch->region = $validatedData['region'];
        $branch->address = $validatedData['address'];

        $branch->save();

        return Redirect::route('branch.index')->with('success', 'Daerah operasional telah diperbarui!');
    }

    public function destroy(int $id)
    {
        $branch = Branch::find($id);
        $branch->delete();

        return Redirect::route('branch.index')->with('success', 'Daerah operasional telah dihapus!');
    }
}
