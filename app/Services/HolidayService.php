<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HolidayService
{
    /**
     * Fetch holidays from Nager.Date API
     * 
     * @param int $year
     * @param string $countryCode Default 'ID' for Indonesia
     * @return array
     */
    public function getHolidays(int $year, string $countryCode = 'ID'): array
    {
        try {
            // Special handling for Indonesia (ID) to get more accurate data (including Cuti Bersama)
            if (strtoupper($countryCode) === 'ID') {
                return $this->getIndonesianHolidays($year);
            }

            $response = Http::get("https://date.nager.at/api/v3/PublicHolidays/{$year}/{$countryCode}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Failed to fetch holidays from Nager.Date: " . $response->status());
            return [];
        } catch (\Exception $e) {
            Log::error("Exception when fetching holidays: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch Indonesian holidays from a more accurate source
     */
    protected function getIndonesianHolidays(int $year): array
    {
        try {
            // Use APIHariLibur_V2 as primary source for ID
            $url = "https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/calendar.json";
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                $holidays = [];

                foreach ($data as $date => $h) {
                    // Filter by year if possible (data usually contains current year)
                    if (str_starts_with($date, (string)$year)) {
                        $holidays[] = [
                            'date' => $date,
                            'localName' => $h['summary'][0] ?? 'Hari Libur',
                            'name' => $h['summary'][0] ?? 'Holiday',
                            'description' => $h['description'][0] ?? '',
                        ];
                    }
                }

                if (!empty($holidays)) {
                    return $holidays;
                }
            }

            // Fallback to Nager.Date if V2 source fails or doesn't have the year
            $response = Http::get("https://date.nager.at/api/v3/PublicHolidays/{$year}/ID");
            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Exception when fetching Indonesian holidays: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get available countries from Nager.Date API
     * 
     * @return array
     */
    public function getAvailableCountries(): array
    {
        try {
            $response = Http::get("https://date.nager.at/api/v3/AvailableCountries");

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
