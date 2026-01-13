<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::all();

        return view('management_data.branch.index',compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
   public function create()
    {
        return view('management_data.branch.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $branch = Branch::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        //
    }
}
