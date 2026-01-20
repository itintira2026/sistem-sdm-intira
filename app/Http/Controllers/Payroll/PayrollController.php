<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GajihPokok;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    // public function index()
    // {
    //     $branches = Branch::latest()->paginate(10);

    //     return view('payroll.index', compact('branches'));
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

        return view('payroll.index', compact(
            'branches',
            'perPage',
            'status',
            'search'
        ));
    }

    public function show(GajihPokok $gajihPokok)
    {
        $gajihPokok->load([
            'branchUser.user.roles',
            'branchUser.branch'
        ]);

        return view('payroll.gajih_pokok.show', compact('gajihPokok'));
    }
}
