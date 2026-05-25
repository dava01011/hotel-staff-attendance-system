<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiKantor extends Model
{
    use HasFactory;

    protected $table = 'lokasi_kantor';

    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'radius'    => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Format coordinates untuk display
     */
    public function getFormattedCoordinatesAttribute()
    {
        return number_format($this->latitude, 6) . ", " . number_format($this->longitude, 6);
    }

    /**
     * Format radius untuk display (meter ke km jika diperlukan)
     */
    public function getFormattedRadiusAttribute()
    {
        if ($this->radius >= 1000) {
            return number_format($this->radius / 1000, 2) . ' km';
        }
        return $this->radius . ' m';
    }

    /**
     * Get jarak antara dua koordinat dalam meter (Haversine formula)
     * @param float $lat1 Latitude user
     * @param float $lon1 Longitude user
     * @param float $lat2 Latitude lokasi
     * @param float $lon2 Longitude lokasi
     * @return float Jarak dalam meter
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // radius bumi dalam meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Get jarak dengan static method yang lebih sederhana
     */
    public static function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        return self::calculateDistance($lat1, $lon1, $lat2, $lon2);
    }

    /**
     * Check apakah user berada dalam radius lokasi kantor ini
     * @param float $userLat Latitude user
     * @param float $userLng Longitude user
     * @return bool True jika dalam radius, false jika di luar
     */
    public function isWithinRadius($userLat, $userLng)
    {
        $distance = self::calculateDistance(
            $this->latitude,
            $this->longitude,
            $userLat,
            $userLng
        );

        return $distance <= $this->radius;
    }

    /**
     * Check untuk user yang belum terdaftar di lokasi manapun (static)
     * Digunakan untuk geofencing absensi
     * @param float $userLat Latitude user
     * @param float $userLng Longitude user
     * @return Lokasi kantor yang paling dekat, atau null jika semua di luar radius
     */
    public static function isUserWithinAnyOffice($userLat, $userLng)
    {
        $lokasi = self::all();

        foreach ($lokasi as $office) {
            if ($office->isWithinRadius($userLat, $userLng)) {
                return $office;
            }
        }

        return null;
    }

    /**
     * Get lokasi terdekat dengan user
     * @param float $userLat Latitude user
     * @param float $userLng Longitude user
     * @return Model|null Lokasi terdekat
     */
    public static function getNearestOffice($userLat, $userLng)
    {
        $lokasi = self::all();
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($lokasi as $office) {
            $distance = self::calculateDistance(
                $office->latitude,
                $office->longitude,
                $userLat,
                $userLng
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $office;
            }
        }

        return $nearest;
    }

    /**
     * Get semua lokasi dengan jarak dari user
     * @param float $userLat Latitude user
     * @param float $userLng Longitude user
     * @return array Array of lokasi dengan distance dan status (within/outside)
     */
    public static function getAllLocationsWithDistance($userLat, $userLng)
    {
        $lokasi = self::all();
        $results = [];

        foreach ($lokasi as $office) {
            $distance = self::calculateDistance(
                $office->latitude,
                $office->longitude,
                $userLat,
                $userLng
            );

            $results[] = [
                'id' => $office->id,
                'nama' => $office->nama_lokasi,
                'latitude' => $office->latitude,
                'longitude' => $office->longitude,
                'radius' => $office->radius,
                'distance' => round($distance, 2),
                'formatted_distance' => self::formatDistance($distance),
                'within_radius' => $distance <= $office->radius,
                'status' => $distance <= $office->radius ? 'inside' : 'outside',
            ];
        }

        return $results;
    }

    /**
     * Format distance untuk display
     * @param float $distance Jarak dalam meter
     * @return string Distance yang di-format
     */
    public static function formatDistance($distance)
    {
        if ($distance >= 1000) {
            return number_format($distance / 1000, 2) . ' km';
        }
        return round($distance, 0) . ' m';
    }

    /**
     * Check berdasarkan nama lokasi
     * @param string $namaLokasi Nama lokasi kantor
     * @param float $userLat Latitude user
     * @param float $userLng Longitude user
     * @return bool True jika user dalam radius lokasi dengan nama tertentu
     */
    public static function isUserInLocation($namaLokasi, $userLat, $userLng)
    {
        $lokasi = self::where('nama_lokasi', $namaLokasi)->first();

        if (!$lokasi) {
            return false;
        }

        return $lokasi->isWithinRadius($userLat, $userLng);
    }

    /**
     * Get info lokasi untuk geofencing validation
     * Digunakan di AbsensiController
     */
    public static function getGeofenceInfo($userLat, $userLng)
    {
        $nearestOffice = self::getNearestOffice($userLat, $userLng);
        $allLocations = self::getAllLocationsWithDistance($userLat, $userLng);
        $withinAny = self::isUserWithinAnyOffice($userLat, $userLng);

        return [
            'within_any_office' => $withinAny !== null,
            'nearest_office' => $nearestOffice,
            'all_locations' => $allLocations,
            'geofence_status' => $withinAny ? 'inside' : 'outside',
            'message' => $withinAny
                ? "Anda berada dalam area {$withinAny->nama_lokasi}"
                : "Anda berada di luar area kantor. Absensi mungkin tidak diizinkan."
        ];
    }
}
