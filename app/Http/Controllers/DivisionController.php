<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Part G2 — Kelola Divisi (CRUD sederhana).
 * Guard: gak bisa hapus divisi yang masih punya user terkait, biar
 * gak ada user tiba-tiba "kehilangan" divisinya secara diam-diam.
 */
class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount('users')->orderBy('name')->get();

        return view('divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('divisions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:divisions,code',
        ]);

        Division::create($validated);

        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function edit(Division $division)
    {
        return view('divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('divisions', 'code')->ignore($division->id)],
        ]);

        $division->update($validated);

        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Division $division)
    {
        if ($division->users()->exists()) {
            return back()->with('error', 'Tidak bisa hapus divisi yang masih punya user terkait. Pindahkan usernya dulu.');
        }

        $division->delete();

        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil dihapus.');
    }
}
