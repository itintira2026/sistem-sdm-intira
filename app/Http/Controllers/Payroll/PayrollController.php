<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GajihPokok;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
      public function index()
    {
        $branches = Branch::latest()->paginate(10);

        return view('payroll.index', compact('branches'));
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
