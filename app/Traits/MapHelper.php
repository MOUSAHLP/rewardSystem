<?php

namespace App\Traits;

trait MapHelper
{
    /**
     * Calculate the Haversine distance between two sets of coordinates.
     *
     * @param float $latFrom Latitude of the first point
     * @param float $lonFrom Longitude of the first point
     * @param float $latTo Latitude of the second point
     * @param float $lonTo Longitude of the second point
     * @return float Distance in kilometers
     */
    protected static function calculateHaversineDistance($latFrom, $lonFrom, $latTo, $lonTo)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        // Calculate the differences in latitude and longitude
        $deltaLat = deg2rad($latTo - $latFrom);
        $deltaLon = deg2rad($lonTo - $lonFrom);

        // Calculate intermediate values
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos(deg2rad($latFrom)) * cos(deg2rad($latTo)) * sin($deltaLon / 2) * sin($deltaLon / 2);
        $centralAngle = 2 * asin(sqrt($a));

        // Calculate the distance
        $distance = $earthRadius * $centralAngle;

        return $distance;
    }

    /**
     * Check if two sets of coordinates are within a specified distance threshold.
     *
     * @param float $latFrom Latitude of the first point
     * @param float $lonFrom Longitude of the first point
     * @param float $latTo Latitude of the second point
     * @param float $lonTo Longitude of the second point
     * @param float $threshold Distance threshold in kilometers
     * @return bool True if the coordinates are within the threshold, false otherwise
     */
    protected static function areCoordinatesWithinThreshold($latFrom, $lonFrom, $latTo, $lonTo, $threshold)
    {
        $distance = self::calculateHaversineDistance($latFrom, $lonFrom, $latTo, $lonTo);
        return $distance <= $threshold;
    }
}
