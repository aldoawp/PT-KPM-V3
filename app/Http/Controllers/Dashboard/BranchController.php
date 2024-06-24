<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BranchController extends Controller
{
    public function index() 
    {
        return view('branch.index');
    }

    public function create() 
    {
        return view('branch.create');
    }

    // public function store(Request $request)
    // {
        
    // }

    // public function destroy(Branch $branch)
    // {
        
    // }
}
