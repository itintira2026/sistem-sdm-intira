<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
      public function index()
    {
        $branches = Branch::latest()->paginate(10);

        return view('payroll.index', compact('branches'));
    }
}
