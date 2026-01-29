<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    // public function index()
    // {
    //     $branches = Branch::latest()->paginate(10);

    //     return view('management_data.branch.index', compact('branches'));
    // }
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $status  = $request->get('status');
        $search  = $request->get('search');

        $branches = Branch::query()
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('is_active', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('code')
            ->paginate($perPage)
            ->withQueryString();

        return view('management_data.branch.index', compact(
            'branches',
            'perPage',
            'status',
            'search'
        ));
    }

    public function create()
    {
        return view('management_data.branch.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:50|unique:branches,code',
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string',
            'is_active' => 'boolean',
        ]);

        Branch::create([
            'name'      => $validated['name'],
            'code'      => $validated['code'],
            'phone'     => $validated['phone'],
            'address'   => $validated['address'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil ditambahkan');
    }

    public function show(Branch $branch)
    {
        $branch->load('users'); // jika ada relasi users

        return view('management_data.branch.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        return view('management_data.branch.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string',
            'is_active' => 'boolean',
        ]);

        $branch->update([
            'name'      => $validated['name'],
            'code'      => $validated['code'],
            'phone'     => $validated['phone'],
            'address'   => $validated['address'],
            'is_active' => $validated['is_active'] ?? $branch->is_active,
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil diperbarui');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil dihapus');
    }

    public function activate(Branch $branch)
    {
        $branch->update(['is_active' => true]);

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil diaktifkan');
    }

    public function deactivate(Branch $branch)
    {
        $branch->update(['is_active' => false]);

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil dinonaktifkan');
    }

    public function search(Request $request)
    {
        $q = $request->input('q');

        return Branch::query()
            ->when($q, function ($query) use ($q) {
                $query->where('code', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            })
            ->orderBy('code')
            ->limit(20)
            ->get()
            ->map(function ($branch) {
                return [
                    'id'   => $branch->id,
                    'text' => "{$branch->code} - {$branch->name}",
                ];
            });
    }
}
