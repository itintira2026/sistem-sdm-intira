<?php

namespace App\Helpers;

use App\Models\Branch;
use Carbon\Carbon;

class TimeHelper
{
    /**
     * Convert WIB/WITA/WIT to Laravel timezone string
     *
     * @param string $timezone WIB|WITA|WIT
     * @return string
     */
    public static function getTimezoneStringForView($timezone)
    {
        return self::getTimezoneString($timezone);
    }
    protected static function getTimezoneString($timezone)
    {
        $timezones = config('daily_report_fo.timezones', [
            'WIB' => 'Asia/Jakarta',
            'WITA' => 'Asia/Makassar',
            'WIT' => 'Asia/Jayapura',
        ]);

        return $timezones[$timezone] ?? 'Asia/Jakarta';
    }

    /**
     * Get current time for specific branch (with timezone)
     *
     * @param int $branchId
     * @return Carbon
     */
    public static function getBranchTime($branchId)
    {
        $branch = Branch::find($branchId);
        $timezone = $branch->timezone ?? 'WIB'; // Default WIB

        $timezoneString = self::getTimezoneString($timezone);

        return now()->timezone($timezoneString);
    }

    /**
     * Get slot configuration
     *
     * @param string $shift 'pagi' or 'siang'
     * @param int $slotNumber 1, 2, 3, or 4
     * @return array|null
     */
    public static function getSlotConfig($shift, $slotNumber)
    {
        $shifts = config('daily_report_fo.shifts');

        if (!isset($shifts[$shift]) || !isset($shifts[$shift]['slots'][$slotNumber])) {
            return null;
        }

        return [
            'shift' => $shift,
            'slot_number' => $slotNumber,
            'slot_time' => $shifts[$shift]['slots'][$slotNumber],
            'shift_label' => $shifts[$shift]['label'],
            'shift_start' => $shifts[$shift]['start_time'],
            'shift_end' => $shifts[$shift]['end_time'],
        ];
    }

    /**
     * Get all slots for a specific shift
     *
     * @param string $shift
     * @return array
     */
    public static function getShiftSlots($shift)
    {
        $shifts = config('daily_report_fo.shifts');

        if (!isset($shifts[$shift])) {
            return [];
        }

        $slots = [];
        foreach ($shifts[$shift]['slots'] as $slotNumber => $slotTime) {
            $slots[] = self::getSlotConfig($shift, $slotNumber);
        }

        return $slots;
    }

    /**
     * Check if slot is currently open (dalam window upload)
     *
     * @param string $slotTime e.g., '10:00'
     * @param int $branchId
     * @param string|null $date Format: Y-m-d (default: today)
     * @return bool
     */
    public static function isSlotOpen($slotTime, $branchId, $date = null)
    {
        $branchTime = self::getBranchTime($branchId);
        $branch = Branch::find($branchId);
        $timezoneString = self::getTimezoneString($branch->timezone ?? 'WIB');

        // Default to today if date not specified
        $targetDate = $date ?? $branchTime->toDateString();

        // Parse slot time in branch timezone
        $slotStart = Carbon::parse($targetDate . ' ' . $slotTime, $timezoneString);

        // Handle midnight slot (00:00)
        if ($slotTime === '00:00') {
            $slotStart->addDay();
        }

        $windowMinutes = config('daily_report_fo.upload_window_minutes', 60);
        $slotEnd = $slotStart->copy()->addMinutes($windowMinutes);

        return $branchTime->between($slotStart, $slotEnd);
    }

    /**
     * Check if slot has already passed (window sudah tutup)
     *
     * @param string $slotTime
     * @param int $branchId
     * @param string|null $date
     * @return bool
     */
    public static function hasSlotPassed($slotTime, $branchId, $date = null)
    {
        $branchTime = self::getBranchTime($branchId);
        $branch = Branch::find($branchId);
        $timezoneString = self::getTimezoneString($branch->timezone ?? 'WIB');

        $targetDate = $date ?? $branchTime->toDateString();

        $slotStart = Carbon::parse($targetDate . ' ' . $slotTime, $timezoneString);

        // Handle midnight slot
        if ($slotTime === '00:00') {
            $slotStart->addDay();
        }

        $windowMinutes = config('daily_report_fo.upload_window_minutes', 60);
        $slotEnd = $slotStart->copy()->addMinutes($windowMinutes);

        return $branchTime->greaterThan($slotEnd);
    }

    /**
     * Get slot status: 'waiting', 'open', or 'closed'
     *
     * @param string $slotTime
     * @param int $branchId
     * @param string|null $date
     * @return string
     */
    public static function getSlotStatus($slotTime, $branchId, $date = null)
    {
        if (self::isSlotOpen($slotTime, $branchId, $date)) {
            return 'open';
        }

        if (self::hasSlotPassed($slotTime, $branchId, $date)) {
            return 'closed';
        }

        return 'waiting';
    }

    /**
     * Get remaining time until slot opens (in seconds)
     *
     * @param string $slotTime
     * @param int $branchId
     * @param string|null $date
     * @return int|null Returns null if slot already opened/closed
     */
    public static function getTimeUntilSlotOpens($slotTime, $branchId, $date = null)
    {
        $status = self::getSlotStatus($slotTime, $branchId, $date);

        if ($status !== 'waiting') {
            return null;
        }

        $branchTime = self::getBranchTime($branchId);
        $branch = Branch::find($branchId);
        $timezoneString = self::getTimezoneString($branch->timezone ?? 'WIB');

        $targetDate = $date ?? $branchTime->toDateString();

        $slotStart = Carbon::parse($targetDate . ' ' . $slotTime, $timezoneString);

        // Handle midnight slot
        if ($slotTime === '00:00') {
            $slotStart->addDay();
        }

        return $branchTime->diffInSeconds($slotStart, false);
    }

    /**
     * Get remaining time in slot window (in seconds)
     *
     * @param string $slotTime
     * @param int $branchId
     * @param string|null $date
     * @return int|null Returns null if slot not open
     */
    public static function getRemainingTimeInSlot($slotTime, $branchId, $date = null)
    {
        if (!self::isSlotOpen($slotTime, $branchId, $date)) {
            return null;
        }

        $branchTime = self::getBranchTime($branchId);
        $branch = Branch::find($branchId);
        $timezoneString = self::getTimezoneString($branch->timezone ?? 'WIB');

        $targetDate = $date ?? $branchTime->toDateString();

        $slotStart = Carbon::parse($targetDate . ' ' . $slotTime, $timezoneString);

        // Handle midnight slot
        if ($slotTime === '00:00') {
            $slotStart->addDay();
        }

        $windowMinutes = config('daily_report_fo.upload_window_minutes', 60);
        $slotEnd = $slotStart->copy()->addMinutes($windowMinutes);

        return $branchTime->diffInSeconds($slotEnd, false);
    }

    /**
     * Format seconds to HH:MM:SS
     *
     * @param int $seconds
     * @return string
     */
    public static function formatCountdown($seconds)
    {
        if ($seconds < 0) {
            return '00:00:00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    /**
     * Get slot window range (start - end time)
     *
     * @param string $slotTime
     * @param int $branchId
     * @param string|null $date
     * @return array ['start' => Carbon, 'end' => Carbon]
     */
    public static function getSlotWindowRange($slotTime, $branchId, $date = null)
    {
        $branch = Branch::find($branchId);
        $timezoneString = self::getTimezoneString($branch->timezone ?? 'WIB');

        $branchTime = self::getBranchTime($branchId);
        $targetDate = $date ?? $branchTime->toDateString();

        $slotStart = Carbon::parse($targetDate . ' ' . $slotTime, $timezoneString);

        // Handle midnight slot
        if ($slotTime === '00:00') {
            $slotStart->addDay();
        }

        $windowMinutes = config('daily_report_fo.upload_window_minutes', 60);
        $slotEnd = $slotStart->copy()->addMinutes($windowMinutes);

        return [
            'start' => $slotStart,
            'end' => $slotEnd,
        ];
    }
}
