<?php

namespace App\Http\Controllers;

use App\Models\PeriodWorkingDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriodWorkingDayController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $workingDays = PeriodWorkingDay::whereBetween('tanggal', [
            $startOfMonth->toDateString(),
            $endOfMonth->toDateString(),
        ])
            ->pluck('tanggal')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        return view('period-working-days.index', [
            'month' => $month,
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
            'workingDays' => $workingDays,
            'prevMonth' => $startOfMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $startOfMonth->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $request->input('tanggal');
        $existing = PeriodWorkingDay::where('tanggal', $tanggal)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed', 'tanggal' => $tanggal]);
        }

        PeriodWorkingDay::create([
            'tanggal' => $tanggal,
            'set_by' => Auth::id(),
        ]);

        return response()->json(['status' => 'added', 'tanggal' => $tanggal]);
    }
}
