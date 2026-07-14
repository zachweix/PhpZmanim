<?php

/**
 * Zmanim PHP API
 * Copyright (C) 2019-2023 Zachary Weixelbaum
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
use InvalidArgumentException;
use PhpZmanim\GeoLocation;

class NoaaCalculator extends AstronomicalCalculator
{
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	const JULIAN_DAY_JAN_1_2000 = 2451545.0;
	const JULIAN_DAYS_PER_CENTURY = 36525.0;

	const SUNRISE = 0;
	const SUNSET = 1;
	const NOON = 2;
	const MIDNIGHT = 3;

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getUTCSunrise(Carbon $date, GeoLocation $geo, float $zenith, bool $adjustForElevation): float
	{
		return $this->getUTCSunRiseSet($date, $geo, $zenith, $adjustForElevation, self::SUNRISE);
	}

	public function getUTCSunset(Carbon $date, GeoLocation $geo, float $zenith, bool $adjustForElevation): float
	{
		return $this->getUTCSunRiseSet($date, $geo, $zenith, $adjustForElevation, self::SUNSET);
	}

	public function getUTCNoon(Carbon $date, GeoLocation $geo): float
	{
		$noon = $this->getSolarNoonMidnightUTC(self::getJulianDay($date), -$geo->getLongitude(), self::NOON);
		$noon = $noon / 60;
		return fmod(fmod($noon, 24) + 24, 24);
	}

	public function getUTCMidnight(Carbon $date, GeoLocation $geo): float
	{
		$midnight = $this->getSolarNoonMidnightUTC(self::getJulianDay($date), -$geo->getLongitude(), self::MIDNIGHT);
		$midnight = $midnight / 60;
		return fmod(fmod($midnight, 24) + 24, 24);
	}

	public function getTimeAtAzimuth(Carbon $date, GeoLocation $geo, float $azimuth): float
	{
		if ($azimuth != 90.0 && $azimuth != 270.0) {
			throw new InvalidArgumentException('The azimuth must be 90 or 270. Other azimuth values are not supported');
		}

		$julianDay = self::getJulianDay($date); 
		$solarNoonBase = 0.5 - ($geo->getLongitude() / 360.0);
		$dateTime = $solarNoonBase + (($azimuth == 90.0) ? 0.25 : 0.75);
		
		for ($i = 0; $i < 3; $i++) {
			$julianCenturies = self::getJulianCenturiesFromJulianDay($julianDay + $dateTime);
			$ratio = tan(deg2rad(self::getSunDeclination($julianCenturies))) / tan(deg2rad($geo->getLatitude()));

			if (is_nan($ratio) || $ratio > 1.0 || $ratio < -1.0) {
				return NAN;
			}

			$offset = (($azimuth == 90.0) ? -1.0 : 1.0) * (rad2deg(acos($ratio)) / 360.0);
			$dateTime = $solarNoonBase + $offset - (self::getEquationOfTime($julianCenturies) / 1440.0);
		}

		return fmod(fmod($dateTime * 24, 24) + 24, 24);
	}

	public function getSolarElevation(Carbon $datetime, GeoLocation $geo): float
	{
		return $this->getSolarElevationAzimuth($datetime, $geo, false);
	}

	public function getSolarAzimuth(Carbon $datetime, GeoLocation $geo): float
	{
		return $this->getSolarElevationAzimuth($datetime, $geo, true);
	}

	/*
	|--------------------------------------------------------------------------
	| STATIC FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	private static function getJulianDay(Carbon $date): float
	{
		$year = $date->year;
		$month = $date->month;
		$day = $date->day;

		if ($month <= 2) {
			$year -= 1;
			$month += 12;
		}

		$a = (int) ($year / 100);
		$b = (int) (2 - $a + (int) ($a / 4));

		return floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $b - 1524.5;
	}

	private static function getJulianCenturiesFromJulianDay(float $julianDay): float
	{
		return ($julianDay - self::JULIAN_DAY_JAN_1_2000) / self::JULIAN_DAYS_PER_CENTURY;
	}

	private static function getSunGeometricMeanLongitude(float $julianCenturies): float
	{
		$longitude = 280.46646 + $julianCenturies * (36000.76983 + 0.0003032 * $julianCenturies);
		return fmod(fmod($longitude, 360) + 360, 360);
	}

	private static function getSunGeometricMeanAnomaly(float $julianCenturies): float
	{
		return 357.52911 + $julianCenturies * (35999.05029 - 0.0001537 * $julianCenturies);
	}

	private static function getEarthOrbitEccentricity(float $julianCenturies): float
	{
		return 0.016708634 - $julianCenturies * (0.000042037 + 0.0000001267 * $julianCenturies);
	}

	private static function getSunEquationOfCenter(float $julianCenturies): float
	{
		$m = self::getSunGeometricMeanAnomaly($julianCenturies);

		$sinm = sin(deg2rad($m));
		$sin2m = sin(deg2rad($m + $m));
		$sin3m = sin(deg2rad($m + $m + $m));

		return $sinm * (1.914602 - $julianCenturies * (0.004817 + 0.000014 * $julianCenturies)) + $sin2m
				* (0.019993 - 0.000101 * $julianCenturies) + $sin3m * 0.000289;
	}

	private static function getSunTrueLongitude(float $julianCenturies): float
	{
		$sunLongitude = self::getSunGeometricMeanLongitude($julianCenturies);
		$center = self::getSunEquationOfCenter($julianCenturies);

		return $sunLongitude + $center;
	}

	private static function getSunApparentLongitude(float $julianCenturies): float
	{
		$sunTrueLongitude = self::getSunTrueLongitude($julianCenturies);
		$omega = 125.04 - 1934.136 * $julianCenturies;
		return $sunTrueLongitude - 0.00569 - 0.00478 * sin(deg2rad($omega));
	}

	private static function getMeanObliquityOfEcliptic(float $julianCenturies): float
	{
		$seconds = 21.448 - $julianCenturies
				* (46.8150 + $julianCenturies * (0.00059 - $julianCenturies * (0.001813)));
		return 23.0 + (26.0 + ($seconds / 60.0)) / 60.0;
	}

	private static function getObliquityCorrection(float $julianCenturies): float
	{
		$obliquityOfEcliptic = self::getMeanObliquityOfEcliptic($julianCenturies);
		$omega = 125.04 - 1934.136 * $julianCenturies;
		return $obliquityOfEcliptic + 0.00256 * cos(deg2rad($omega));
	}

	private static function getSunDeclination(float $julianCenturies): float
	{
		$obliquityCorrection = self::getObliquityCorrection($julianCenturies);
		$lambda = self::getSunApparentLongitude($julianCenturies);
		$sint = sin(deg2rad($obliquityCorrection)) * sin(deg2rad($lambda));
		return rad2deg(asin($sint));
	}

	private static function getEquationOfTime(float $julianCenturies): float
	{
		$epsilon = self::getObliquityCorrection($julianCenturies);
		$geomMeanLongSun = self::getSunGeometricMeanLongitude($julianCenturies);
		$eccentricityEarthOrbit = self::getEarthOrbitEccentricity($julianCenturies);
		$geomMeanAnomalySun = self::getSunGeometricMeanAnomaly($julianCenturies);
		$y = tan(deg2rad($epsilon) / 2.0);
		$y *= $y;
		$sin2l0 = sin(deg2rad(2.0 * $geomMeanLongSun));
		$sinm = sin(deg2rad($geomMeanAnomalySun));
		$cos2l0 = cos(deg2rad(2.0 * $geomMeanLongSun));
		$sin4l0 = sin(deg2rad(4.0 * $geomMeanLongSun));
		$sin2m = sin(deg2rad(2.0 * $geomMeanAnomalySun));
		$equationOfTime = $y * $sin2l0 - 2.0 * $eccentricityEarthOrbit * $sinm + 4.0 * $eccentricityEarthOrbit * $y
				* $sinm * $cos2l0 - 0.5 * $y * $y * $sin4l0 - 1.25 * $eccentricityEarthOrbit * $eccentricityEarthOrbit * $sin2m;
		return rad2deg($equationOfTime) * 4.0;
	}

	private static function getSunHourAngle(float $latitude, float $solarDeclination, float $zenith, $solarEvent): float
	{
		$ratio = cos(deg2rad($zenith)) / (cos(deg2rad($latitude)) * cos(deg2rad($solarDeclination))) - tan(deg2rad($latitude))
				* tan(deg2rad($solarDeclination));
		$hourAngle = acos($ratio);
		
		if ($solarEvent == self::SUNSET) {
			$hourAngle = -$hourAngle;
		}
		return $hourAngle;
	}

	/*
	|--------------------------------------------------------------------------
	| CALCULATIONS
	|--------------------------------------------------------------------------
	*/

	private function getUTCSunRiseSet(Carbon $date, GeoLocation $geo, float $zenith, bool $adjustForElevation, $solarEvent): float
	{
		$elevation = $adjustForElevation ? $geo->getElevation() : 0;
		$adjustedZenith = $this->adjustZenith($zenith, $elevation, $date);
		$riseSet = $this->getSunRiseSetUTC($date, $geo->getLatitude(), -$geo->getLongitude(), $adjustedZenith, $solarEvent);

		$riseSet = $riseSet / 60;

		return fmod(fmod($riseSet, 24) + 24, 24);
	}

	private function getSolarNoonMidnightUTC(float $julianDay, float $longitude, $solarEvent): float
	{
		$tnoon = self::getJulianCenturiesFromJulianDay($julianDay + $longitude / 360.0);
		$equationOfTime = self::getEquationOfTime($tnoon);
		$solNoonUTC = ($longitude * 4) - $equationOfTime;

		for ($i = 0; $i < 2; $i++) {
			$newt = self::getJulianCenturiesFromJulianDay($julianDay + $solNoonUTC / 1440.0);
			$equationOfTime = self::getEquationOfTime($newt);
			$solNoonUTC = ($solarEvent == self::NOON ? 720 : 1440) + ($longitude * 4) - $equationOfTime;
		}
		return $solNoonUTC;
	}

	private function getSunRiseSetUTC(Carbon $date, float $latitude, float $longitude, float $zenith, $solarEvent): float
	{
		$julianDay = self::getJulianDay($date);

		$noonmin = $this->getSolarNoonMidnightUTC($julianDay, $longitude, self::NOON);
		$tnoon = self::getJulianCenturiesFromJulianDay($julianDay + $noonmin / 1440.0);
		$equationOfTime = self::getEquationOfTime($tnoon);
		$solarDeclination = self::getSunDeclination($tnoon);
		$hourAngle = self::getSunHourAngle($latitude, $solarDeclination, $zenith, $solarEvent);
		$delta = $longitude - rad2deg($hourAngle);
		$timeDiff = 4 * $delta;
		$timeUTC = 720 + $timeDiff - $equationOfTime;

		$newt = self::getJulianCenturiesFromJulianDay($julianDay + $timeUTC / 1440.0);
		$equationOfTime = self::getEquationOfTime($newt);
		$solarDeclination = self::getSunDeclination($newt);
		$hourAngle = self::getSunHourAngle($latitude, $solarDeclination, $zenith, $solarEvent);
		$delta = $longitude - rad2deg($hourAngle);
		$timeDiff = 4 * $delta;
		$timeUTC = 720 + $timeDiff - $equationOfTime;
		return $timeUTC;
	}

	private function getSolarElevationAzimuth(Carbon $datetime, GeoLocation $geo, bool $isAzimuth): float
	{
		$lat = $geo->getLatitude();
		$lon = $geo->getLongitude();
		$utc = $datetime->copy()->utc();
		$fractionalDay = $utc->secondsSinceMidnight() / 86400.0;
		$jd = self::getJulianDay($utc) + $fractionalDay;
		$jc = self::getJulianCenturiesFromJulianDay($jd);
		$decl = self::getSunDeclination($jc);
		$eot = self::getEquationOfTime($jc);
		$trueSolarTime = fmod(($fractionalDay + $eot / 1440.0 + $lon / 360.0) + 2, 1);
		$hourAngle = $trueSolarTime * 2 * M_PI - M_PI;
		$cosZenith = sin(deg2rad($lat)) * sin(deg2rad($decl)) + cos(deg2rad($lat)) * cos(deg2rad($decl)) * cos($hourAngle);
		$zenithDeg = rad2deg(acos(max(-1, min(1, $cosZenith))));

		if (!$isAzimuth) {
			return (90.0 - $zenithDeg) + $this->adjustElevationForRefraction(90.0 - $zenithDeg);
		}

		$azDenom = cos(deg2rad($lat)) * sin(deg2rad($zenithDeg));
		if (abs($azDenom) > 0.001) {
			$az = (sin(deg2rad($lat)) * cos(deg2rad($zenithDeg)) - sin(deg2rad($decl))) / $azDenom;
			$azimuth = 180 - rad2deg(acos(max(-1, min(1, $az)))) * ($hourAngle > 0 ? -1 : 1);
		} else {
			$azimuth = $lat > 0 ? 180 : 0;
		}
		return fmod($azimuth + 360, 360);
	}

	private function adjustElevationForRefraction(float $elevation): float
	{
		if ($elevation > 85.0) {
			return 0.0;
		}

		$te = tan(deg2rad($elevation));

		if ($elevation > 5.0) {
			$correction = 58.1 / $te - 0.07 / pow($te, 3) + 0.000086 / pow($te, 5);
		} else if ($elevation > -0.575) {
			$correction = 1735.0 + $elevation * (-518.2 + $elevation * (103.4 + $elevation * (-12.79 + 0.711 * $elevation)));
		} else {
			$correction = -20.774 / $te;
		}
		return $correction / 3600.0;
	}
}