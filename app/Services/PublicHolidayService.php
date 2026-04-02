<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Fetches PH/AU public holidays via Nager.Date (server-side, same-origin for the dashboard).
 */
class PublicHolidayService
{
    private const CACHE_TTL_SECONDS = 86400;

    /**
     * @return array{ph: list<array{date: string, localName: string, name: string, source: string}>, au: list<array{date: string, localName: string, name: string, source: string}>}
     */
    public static function forYear(int $year): array
    {
        $key = 'public_holidays_nager_v3_'.$year;

        return Cache::remember($key, self::CACHE_TTL_SECONDS, function () use ($year) {
            $ph = self::fetchCountry($year, 'PH');
            $au = self::fetchCountry($year, 'AU');

            return ['ph' => $ph, 'au' => $au];
        });
    }

    /**
     * @return list<array{date: string, localName: string, name: string, source: string}>
     */
    private static function fetchCountry(int $year, string $countryCode): array
    {
        $url = "https://date.nager.at/api/v3/PublicHolidays/{$year}/{$countryCode}";
        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->get($url);
        } catch (\Throwable) {
            return [];
        }

        if (! $response->successful()) {
            return [];
        }

        $raw = $response->json();
        if (! is_array($raw)) {
            return [];
        }

        $out = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $date = isset($row['date']) ? (string) $row['date'] : '';
            if ($date === '' || strlen($date) < 10) {
                continue;
            }
            $out[] = [
                'date' => substr($date, 0, 10),
                'localName' => isset($row['localName']) ? (string) $row['localName'] : '',
                'name' => isset($row['name']) ? (string) $row['name'] : '',
                'source' => $countryCode,
            ];
        }

        return $out;
    }
}
