<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

if (!function_exists('tcg_semester_for_month')) {
    function tcg_semester_for_month(string $tcg, string $mes): ?array
    {
        $season = DB::table('tcg_seasons')
            ->where('tcg_slug', $tcg)
            ->first();

        if (!$season) {
            return null;
        }

        $seasonStart = Carbon::parse($season->season_start_date)->startOfMonth();
        $anchorMonth = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();

        // Anchor is before the season even started → invalid
        if ($anchorMonth->lt($seasonStart)) {
            return null;
        }

        // How many full months since season start
        $monthsDiff = $seasonStart->diffInMonths($anchorMonth);

        // Which semester block are we in (0-based)
        $semesterIndex = intdiv($monthsDiff, 6);

        // Calculate semester window
        $semesterStart = $seasonStart
            ->copy()
            ->addMonths($semesterIndex * 6);

        $semesterEnd = $semesterStart
            ->copy()
            ->addMonths(6)
            ->subDay()
            ->endOfDay();

        return [
            'start' => $semesterStart,
            'end'   => $semesterEnd,
            'index' => $semesterIndex + 1, // human-friendly
        ];
    }
}
