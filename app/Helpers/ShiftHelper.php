<?php

namespace App\Helpers;

use App\Models\DailyReportFO;
use Illuminate\Support\Facades\Auth;

class ShiftHelper
{
    /**
     * Get selected shift for today from session
     *
     * @return string|null 'pagi' or 'siang' or null
     */
    public static function getTodayShift()
    {
        $sessionKey = config('daily_report_fo.shift_selection.storage_key');
        $storedData = session($sessionKey);

        if (!$storedData) {
            return null;
        }

        // Check if stored shift is for today
        if ($storedData['date'] !== now()->toDateString()) {
            // Clear old session - karena hari berbeda, bisa pilih shift baru
            self::clearShift();
            return null;
        }

        return $storedData['shift'];
    }

    /**
     * Set shift for today
     *
     * @param string $shift 'pagi' or 'siang'
     * @return void
     */
    public static function setTodayShift($shift)
    {
        $sessionKey = config('daily_report_fo.shift_selection.storage_key');

        session([
            $sessionKey => [
                'shift' => $shift,
                'date' => now()->toDateString(),
                'selected_at' => now()->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Clear shift selection
     *
     * @return void
     */
    public static function clearShift()
    {
        $sessionKey = config('daily_report_fo.shift_selection.storage_key');
        session()->forget($sessionKey);
    }

    /**
     * Check if user has already selected shift today
     *
     * @return bool
     */
    public static function hasSelectedShiftToday()
    {
        return self::getTodayShift() !== null;
    }

    /**
     * Check if shift is locked for today (sudah ada laporan)
     * Setelah laporan pertama dibuat, shift tidak bisa diganti lagi hari itu
     *
     * @param int $userId
     * @param string|null $date
     * @return bool
     */
    public static function isShiftLockedToday($userId, $date = null)
    {
        $targetDate = $date ?? now()->toDateString();

        // Check if user already has any report today
        return DailyReportFO::where('user_id', $userId)
            ->whereDate('tanggal', $targetDate)
            ->exists();
    }

    /**
     * Get shift from first report of the day
     * (Untuk determine shift jika session hilang tapi sudah ada laporan)
     *
     * @param int $userId
     * @param string|null $date
     * @return string|null
     */
    public static function getShiftFromReport($userId, $date = null)
    {
        $targetDate = $date ?? now()->toDateString();

        $report = DailyReportFO::where('user_id', $userId)
            ->whereDate('tanggal', $targetDate)
            ->first();

        return $report ? $report->shift : null;
    }

    /**
     * Get or determine shift for user today
     * Priority: session → dari report → null
     *
     * @param int|null $userId
     * @return string|null
     */
    public static function determineShift($userId = null)
    {
        $userId = $userId ?? Auth::id();

        // 1. Check session
        $sessionShift = self::getTodayShift();
        if ($sessionShift) {
            return $sessionShift;
        }

        // 2. Check from existing report (jika session hilang)
        $reportShift = self::getShiftFromReport($userId);
        if ($reportShift) {
            // Restore to session
            self::setTodayShift($reportShift);
            return $reportShift;
        }

        // 3. Belum ada shift hari ini
        return null;
    }

    /**
     * Validate if user can change shift
     * Hanya bisa ganti shift jika belum ada laporan hari ini
     *
     * @param int $userId
     * @return bool
     */
    public static function canChangeShiftToday($userId)
    {
        return !self::isShiftLockedToday($userId);
    }
}
// ```

// ---

// ## 📋 **3. UPDATED CONCEPT SUMMARY**

// ### **A. Shift Times (CORRECTED)**
// ```
// SHIFT PAGI (08:00 - 16:00):
// ├─ Slot 1: 10:00 (window: 10:00-11:00) ⏰
// ├─ Slot 2: 12:00 (window: 12:00-13:00) ⏰
// ├─ Slot 3: 14:00 (window: 14:00-15:00) ⏰
// └─ Slot 4: 16:00 (window: 16:00-17:00) ⏰

// SHIFT SIANG (14:00 - 22:00):
// ├─ Slot 1: 15:00 (window: 15:00-16:00) ⏰
// ├─ Slot 2: 17:00 (window: 17:00-18:00) ⏰
// ├─ Slot 3: 19:00 (window: 19:00-20:00) ⏰
// └─ Slot 4: 21:00 (window: 21:00-22:00) ⏰
// ```

// ### **B. Shift Selection Flow (CORRECTED)**
// ```
// Hari Senin:
// FO Login → Dashboard
//     ↓
// Belum pilih shift hari ini?
//     ↓ YES
// Modal Pilih Shift → Pilih "PAGI"
//     ↓
// Dashboard Slot (10:00, 12:00, 14:00, 16:00)
//     ↓
// Upload Slot 1 (10:00) → SHIFT LOCKED untuk hari ini
//     ↓
// Tidak bisa ganti shift lagi hari ini

// ---

// Hari Selasa (HARI BARU):
// FO Login → Dashboard
//     ↓
// Belum pilih shift hari ini? (Session cleared)
//     ↓ YES
// Modal Pilih Shift → Bisa pilih "SIANG" (berbeda dari kemarin)
//     ↓
// Dashboard Slot (15:00, 17:00, 19:00, 21:00)
// ```

// ### **C. Business Rules (CLARIFIED)**
// ```
// ✅ Shift dipilih 1x per hari (bisa berbeda tiap hari)
// ✅ Setelah upload laporan pertama → shift locked untuk hari itu
// ✅ Besok bisa pilih shift berbeda
// ✅ Session cleared otomatis jika hari berganti
// ✅ Jika session hilang tapi sudah ada laporan → restore shift dari DB
// ```

// ---

// ## 📦 **4. MIGRATIONS (NO CHANGE)**

// Migration tetap sama karena struktur database sudah benar.

// ---

// ## 🛠️ **5. TIME HELPER (NO CHANGE NEEDED)**

// TimeHelper sudah benar, hanya perlu pastikan config slot times sudah diupdate.

// ---

// ## 📦 **6. MODELS (NO CHANGE)**

// Models sudah benar, tidak perlu perubahan.

// ---

// ## ✅ **PHASE 1 FINAL CHECKLIST**

// - [x] **Config** - Slot times corrected (Siang: 15, 17, 19, 21) ✅
// - [x] **Config** - Shift start/end times updated ✅
// - [x] **ShiftHelper** - Revised logic (tidak lock permanent) ✅
// - [x] **Migration** - `daily_report_fo` table ✅
// - [x] **Migration** - `daily_report_fo_photos` table ✅
// - [x] **Migration** - `add_timezone_to_branches` (WIB/WITA/WIT) ✅
// - [x] **TimeHelper** - WIB/WITA/WIT support ✅
// - [x] **Model** - `DailyReportFO` ✅
// - [x] **Model** - `DailyReportFOPhoto` ✅

// ---

// ## 📊 **COMPARISON TABLE - OLD vs CORRECTED**

// | **Shift** | **OLD (SALAH)** | **CORRECTED (BENAR)** |
// |-----------|-----------------|------------------------|
// | **Pagi Start** | 08:00 | 08:00 ✅ |
// | **Pagi End** | 16:00 | 16:00 ✅ |
// | **Pagi Slots** | 10, 12, 14, 16 | 10, 12, 14, 16 ✅ |
// | **Siang Start** | 16:00 ❌ | 14:00 ✅ |
// | **Siang End** | 00:00 ❌ | 22:00 ✅ |
// | **Siang Slots** | 18, 20, 22, 00 ❌ | **15, 17, 19, 21** ✅ |

// ---

// ## 🎯 **VISUAL: Shift Selection Logic**
// ```
// ┌─────────────────────────────────────────┐
// │ HARI SENIN (02 Feb 2026)                │
// ├─────────────────────────────────────────┤
// │ 1. Login → Belum pilih shift            │
// │ 2. Modal: Pilih Shift Pagi ✅           │
// │ 3. Session: {shift: 'pagi', date: '...'}│
// │ 4. Dashboard: Slot 10, 12, 14, 16       │
// │ 5. Upload Slot 1 → Shift LOCKED         │
// │ 6. Tidak bisa ganti ke Siang hari ini   │
// └─────────────────────────────────────────┘

// ┌─────────────────────────────────────────┐
// │ HARI SELASA (03 Feb 2026)               │
// ├─────────────────────────────────────────┤
// │ 1. Login → Session cleared (hari baru)  │
// │ 2. Modal: Pilih Shift Siang ✅          │
// │ 3. Session: {shift: 'siang', date: '...'}│
// │ 4. Dashboard: Slot 15, 17, 19, 21       │
// │ 5. Upload Slot 1 → Shift LOCKED         │
// └─────────────────────────────────────────┘
