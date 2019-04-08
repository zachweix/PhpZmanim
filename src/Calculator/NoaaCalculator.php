<?php

/**
 * Zmanim PHP API
 * Copyright (C) 2019 Zachary Weixelbaum
 *
 * This library is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; either version 2.1 of the License, or (at your option)
 * any later version.
 *
 * This library is distributed in the hope that it will be useful,but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with this library; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA, or connect to:
 * http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html
 */

namespace PhpZmanim\Calculator;

use Carbon\Carbon;
use PhpZmanim\Geo\GeoLocation;

class NoaaCalculator extends AstronomicalCalculator {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	const CALCULATOR_NAME = "US National Oceanic and Atmospheric Administration Algorithm";
	const JULIAN_DAY_JAN_1_2000 = 2451545.0;
	const JULIAN_DAYS_PER_CENTURY = 36525.0;

	/*
	|--------------------------------------------------------------------------
	| STATIC FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	private static function getJulianDay($calendar) {
		$year = $calendar->year;
		$month = $calendar->month;
		$day = $calendar->day;

		if ($month <= 2) {
			$year -= 1;
			$month += 12;
		}

		$a = (int) ($year / 100);
		$b = (int) (2 - $a + (int) ($a / 4));

		return floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $b - 1524.5;
	}

	private static function getJulianCenturiesFromJulianDay($julianDay) {
		return ($julianDay - self::JULIAN_DAY_JAN_1_2000) / self::JULIAN_DAYS_PER_CENTURY;
	}

	private static function getJulianDayFromJulianCenturies($julianCenturies) {
		return $julianCenturies * self::JULIAN_DAYS_PER_CENTURY + self::JULIAN_DAY_JAN_1_2000;
	}

	private static function getSunGeometricMeanLongitude($julianCenturies) {
		$longitude = 280.46646 + $julianCenturies * (36000.76983 + 0.0003032 * $julianCenturies);
		while ($longitude > 360.0) {
			$longitude -= 360.0;
		}
		while ($longitude < 0.0) {
			$longitude += 360.0;
		}

		return $longitude; // in degrees
	}

	private static function getSunGeometricMeanAnomaly($julianCenturies) {
		return 357.52911 + $julianCenturies * (35999.05029 - 0.0001537 * $julianCenturies); // in degrees
	}

	private static function getEarthOrbitEccentricity($julianCenturies) {
		return 0.016708634 - $julianCenturies * (0.000042037 + 0.0000001267 * $julianCenturies); // unitless
	}

	private static function getSunEquationOfCenter($julianCenturies) {
		$m = self::getSunGeometricMeanAnomaly($julianCenturies);

		$mrad = deg2rad($m);
		$sinm = sin($mrad);
		$sin2m = sin($mrad + $mrad);
		$sin3m = sin($mrad + $mrad + $mrad);

		return $sinm * (1.914602 - $julianCenturies * (0.004817 + 0.000014 * $julianCenturies)) + $sin2m
				* (0.019993 - 0.000101 * $julianCenturies) + $sin3m * 0.000289;// in degrees
	}

	private static function getSunTrueLongitude($julianCenturies) {
		$sunLongitude = self::getSunGeometricMeanLongitude($julianCenturies);
		$center = self::getSunEquationOfCenter($julianCenturies);

		return $sunLongitude + $center; // in degrees
	}

	private static function getSunApparentLongitude($julianCenturies) {
		$sunTrueLongitude = self::getSunTrueLongitude($julianCenturies);

		$omega = 125.04 - 1934.136 * $julianCenturies;
		$lambda = $sunTrueLongitude - 0.00569 - 0.00478 * sin(deg2rad($omega));
		return $lambda; // in degrees
	}

	private static function getMeanObliquityOfEcliptic($julianCenturies) {
		$seconds = 21.448 - $julianCenturies
				* (46.8150 + $julianCenturies * (0.00059 - $julianCenturies * (0.001813)));
		return 23.0 + (26.0 + ($seconds / 60.0)) / 60.0; // in degrees
	}

	private static function getObliquityCorrection($julianCenturies) {
		$obliquityOfEcliptic = self::getMeanObliquityOfEcliptic($julianCenturies);

		$omega = 125.04 - 1934.136 * $julianCenturies;
		return $obliquityOfEcliptic + 0.00256 * cos(deg2rad($omega)); // in degrees
	}

	private static function getSunDeclination($julianCenturies) {
		$obliquityCorrection = self::getObliquityCorrection($julianCenturies);
		$lambda = self::getSunApparentLongitude($julianCenturies);

		$sint = sin(deg2rad($obliquityCorrection)) * sin(deg2rad($lambda));
		$theta = rad2deg(asin($sint));
		return $theta; // in degrees
	}

	private static function getEquationOfTime($julianCenturies) {
		$epsilon = self::getObliquityCorrection($julianCenturies);
		$geomMeanLongSun = self::getSunGeometricMeanLongitude($julianCenturies);
		$eccentricityEarthOrbit = self::getEarthOrbitEccentricity($julianCenturies);
		$geomMeanAnomalySun = self::getSunGeometricMeanAnomaly($julianCenturies);

		$y = tan(deg2rad($epsilon) / 2.0);
		$y *= $y;

		$sin2l0 = sin(2.0 * deg2rad($geomMeanLongSun));
		$sinm = sin(deg2rad($geomMeanAnomalySun));
		$cos2l0 = cos(2.0 * deg2rad($geomMeanLongSun));
		$sin4l0 = sin(4.0 * deg2rad($geomMeanLongSun));
		$sin2m = sin(2.0 * deg2rad($geomMeanAnomalySun));

		$equationOfTime = $y * $sin2l0 - 2.0 * $eccentricityEarthOrbit * $sinm + 4.0 * $eccentricityEarthOrbit * $y
				* $sinm * $cos2l0 - 0.5 * $y * $y * $sin4l0 - 1.25 * $eccentricityEarthOrbit * $eccentricityEarthOrbit * $sin2m;
		return rad2deg($equationOfTime) * 4.0; // in minutes of time
	}

	private static function getSunHourAngleAtSunrise($lat, $solarDec, $zenith) {
		$latRad = deg2rad($lat);
		$sdRad = deg2rad($solarDec);

		return (acos(cos(deg2rad($zenith)) / (cos($latRad) * cos($sdRad)) - tan($latRad)
				* tan($sdRad))); // in radians
	}

	private static function getSunHourAngleAtSunset($lat, $solarDec, $zenith) {
		$latRad = deg2rad($lat);
		$sdRad = deg2rad($solarDec);

		$hourAngle = (acos(cos(deg2rad($zenith)) / (cos($latRad) * cos($sdRad))
				- tan($latRad) * tan($sdRad)));
		return -$hourAngle; // in radians
	}

	private static function getSolarElevation($calendar, $lat, $lon) {
		$julianDay = self::getJulianDay($calendar);
		$julianCenturies = self::getJulianCenturiesFromJulianDay($julianDay);

		$eot = self::getEquationOfTime($julianCenturies);

		$longitude = ($calendar->hour + 12.0)
				+ ($calendar->minute + $eot + $calendar->second / 60.0) / 60.0;

		$longitude = -($longitude * 360.0 / 24.0) % 360.0;
		$hourAngle_rad = deg2rad($lon - $longitude);
		$declination = self::getSunDeclination($julianCenturies);
		$dec_rad = deg2rad($declination);
		$lat_rad = deg2rad($lat);

		return rad2deg(asin((sin($lat_rad) * sin($dec_rad))
				+ (cos($lat_rad) * cos($dec_rad) * cos($hourAngle_rad))));
	}

	private static function getSolarAzimuth($calendar, $lat, $lon) {
		$julianDay = self::getJulianDay($calendar);
		$julianCenturies = self::getJulianCenturiesFromJulianDay($julianDay);

		$eot = self::getEquationOfTime($julianCenturies);

		$longitude = ($calendar->hour + 12.0)
				+ ($calendar->minute + $eot + $calendar->second / 60.0) / 60.0;

		$longitude = -($longitude * 360.0 / 24.0) % 360.0;
		$hourAngle_rad = deg2rad($lon - $longitude);
		$declination = self::getSunDeclination($julianCenturies);
		$dec_rad = deg2rad($declination);
		$lat_rad = deg2rad($lat);

		return rad2deg(atan(sin($hourAngle_rad)
				/ ((cos($hourAngle_rad) * sin($lat_rad)) - (tan($dec_rad) * cos($lat_rad)))))+180;
	}

	private static function getSolarNoonUTC($julianCenturies, $longitude) {
		// First pass uses approximate solar noon to calculate eqtime
		$tnoon = self::getJulianCenturiesFromJulianDay(self::getJulianDayFromJulianCenturies($julianCenturies) + $longitude
				/ 360.0);
		$eqTime = self::getEquationOfTime($tnoon);
		$solNoonUTC = 720.0 + ($longitude * 4.0) - $eqTime; // min

		$newt = self::getJulianCenturiesFromJulianDay(self::getJulianDayFromJulianCenturies($julianCenturies) - 0.5
				+ $solNoonUTC / 1440.0);

		$eqTime = self::getEquationOfTime($newt);
		return 720.0 + ($longitude * 4.0) - $eqTime; // min
	}

	private static function getUTCPosition($calendar, $geoLocation, $zenith, $isSunrise) {
		$julianDay = self::getJulianDay($calendar);
		$latitude = $geoLocation->getLatitude();
		$longitude = -$geoLocation->getLongitude();

		$julianCenturies = self::getJulianCenturiesFromJulianDay($julianDay);

		// Find the time of solar noon at the location, and use that declination. This is better than start of the
		// Julian day

		$noonmin = self::getSolarNoonUTC($julianCenturies, $longitude);
		$tnoon = self::getJulianCenturiesFromJulianDay($julianDay + $noonmin / 1440.0);

		// First calculates sunrise and approx length of day

		$eqTime = self::getEquationOfTime($tnoon);
		$solarDec = self::getSunDeclination($tnoon);
		if ($isSunrise) {
			$hourAngle = self::getSunHourAngleAtSunrise($latitude, $solarDec, $zenith);
		} else {
			$hourAngle = self::getSunHourAngleAtSunset($latitude, $solarDec, $zenith);
		}

		$delta = $longitude - rad2deg($hourAngle);
		$timeDiff = 4.0 * $delta;
		$timeUTC = 720.0 + $timeDiff - $eqTime;

		// Second pass includes fractional Julian Day in gamma calc

		$newt = self::getJulianCenturiesFromJulianDay(self::getJulianDayFromJulianCenturies($julianCenturies) + $timeUTC
				/ 1440.0);
		$eqTime = self::getEquationOfTime($newt);
		$solarDec = self::getSunDeclination($newt);
		if ($isSunrise) {
			$hourAngle = self::getSunHourAngleAtSunrise($latitude, $solarDec, $zenith);
		} else {
			$hourAngle = self::getSunHourAngleAtSunset($latitude, $solarDec, $zenith);
		}

		$delta = $longitude - rad2deg($hourAngle);
		$timeDiff = 4.0 * $delta;
		$timeUTC = 720.0 + $timeDiff - $eqTime; // in minutes

		$timeUTC = $timeUTC / 60.0;

		// ensure that the time is >= 0 and < 24
		while ($timeUTC < 0.0) {
			$timeUTC += 24.0;
		}
		while ($timeUTC >= 24.0) {
			$timeUTC -= 24.0;
		}
		return $timeUTC;
	}

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getUTCSunrise(Carbon $calendar, GeoLocation $geoLocation, $zenith, $adjustForElevation) {
		$elevation = $adjustForElevation ? $geoLocation->getElevation() : 0;
		$adjustedZenith = $this->adjustZenith($zenith, $elevation);
		$sunrise = self::getUTCPosition($calendar, $geoLocation, $adjustedZenith, true);
		
		return $sunrise;
	}

	public function getUTCSunset(Carbon $calendar, GeoLocation $geoLocation, $zenith, $adjustForElevation) {
		$elevation = $adjustForElevation ? $geoLocation->getElevation() : 0;
		$adjustedZenith = $this->adjustZenith($zenith, $elevation);
		$sunset = self::getUTCPosition($calendar, $geoLocation, $adjustedZenith, false);
		
		return $sunset;
	}
}