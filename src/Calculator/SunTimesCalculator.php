<?php

/**
 * Zmanim PHP API
 * Copyright (C) 2019-2026 Zachary Weixelbaum
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

use BadMethodCallException;
use Carbon\Carbon;
use PhpZmanim\GeoLocation;

class SunTimesCalculator extends AstronomicalCalculator
{
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	const DEG_PER_HOUR = 360.0 / 24.0;

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getUTCSunrise(Carbon $date, GeoLocation $geo, float $zenith, bool $adjustForElevation): float
	{
		$elevation = $adjustForElevation ? $geo->getElevation() : 0;
		$adjustedZenith = $this->adjustZenith($zenith, $elevation, $date);
		return $this->getTimeUTC($date, $geo, $adjustedZenith, true);
	}

	public function getUTCSunset(Carbon $date, GeoLocation $geo, float $zenith, bool $adjustForElevation): float
	{
		$elevation = $adjustForElevation ? $geo->getElevation() : 0;
		$adjustedZenith = $this->adjustZenith($zenith, $elevation, $date);
		return $this->getTimeUTC($date, $geo, $adjustedZenith, false);
	}

	public function getUTCNoon(Carbon $date, GeoLocation $geo): float
	{
		$sunrise = $this->getUTCSunrise($date, $geo, 90, false);
		$sunset = $this->getUTCSunset($date, $geo, 90, false);
		$noon = $sunrise + (($sunset - $sunrise) / 2);
		if ($noon < $sunrise) {
			$noon -= 12;
		}
		return fmod(fmod($noon, 24) + 24, 24);
	}

	public function getUTCMidnight(Carbon $date, GeoLocation $geo): float
	{
		return fmod($this->getUTCNoon($date, $geo) + 12, 24);
	}

	public function getTimeAtAzimuth(Carbon $date, GeoLocation $geo, float $azimuth): float
	{
		throw new BadMethodCallException('The SunTimesCalculator class does not implement the getTimeAtAzimuth method. Use the NoaaCalculator instead.');
	}

	public function getSolarElevation(Carbon $datetime, GeoLocation $geo): float
	{
		throw new BadMethodCallException('The SunTimesCalculator class does not implement the getSolarElevation method. Use the NoaaCalculator instead.');
	}

	public function getSolarAzimuth(Carbon $datetime, GeoLocation $geo): float
	{
		throw new BadMethodCallException('The SunTimesCalculator class does not implement the getSolarAzimuth method. Use the NoaaCalculator instead.');
	}

	/*
	|--------------------------------------------------------------------------
	| STATIC FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	private static function getHoursFromMeridian(float $longitude): float
	{
		return $longitude / self::DEG_PER_HOUR;
	}

	private static function getApproxTimeDays(int $dayOfYear, float $hoursFromMeridian, bool $isSunrise): float
	{
		if ($isSunrise) {
			return $dayOfYear + ((6.0 - $hoursFromMeridian) / 24);
		} else {
			return $dayOfYear + ((18.0 - $hoursFromMeridian) / 24);
		}
	}

	private static function getMeanAnomaly(int $dayOfYear, float $longitude, bool $isSunrise): float
	{
		return (0.9856 * self::getApproxTimeDays($dayOfYear, self::getHoursFromMeridian($longitude), $isSunrise)) - 3.289;
	}

	private static function getSunTrueLongitude(float $sunMeanAnomaly): float
	{
		$l = $sunMeanAnomaly + (1.916 * sin(deg2rad($sunMeanAnomaly))) + (0.020 * sin(deg2rad(2 * $sunMeanAnomaly))) + 282.634;
		return fmod(fmod($l, 360) + 360, 360);
	}

	private static function getSunRightAscensionHours(float $sunTrueLongitude): float
	{
		$a = 0.91764 * tan(deg2rad($sunTrueLongitude));
		$ra = rad2deg(atan($a));

		$lQuadrant = floor($sunTrueLongitude / 90.0) * 90.0;
		$raQuadrant = floor($ra / 90.0) * 90.0;
		$ra = $ra + ($lQuadrant - $raQuadrant);

		return $ra / self::DEG_PER_HOUR;
	}

	private static function getCosLocalHourAngle(float $sunTrueLongitude, float $latitude, float $zenith): float
	{
		$sinDec = 0.39782 * sin(deg2rad($sunTrueLongitude));
		$cosDec = cos(asin($sinDec));
		return (cos(deg2rad($zenith)) - ($sinDec * sin(deg2rad($latitude)))) / ($cosDec * cos(deg2rad($latitude)));
	}

	private static function getLocalMeanTime(float $localHour, float $sunRightAscensionHours, float $approxTimeDays): float
	{
		return $localHour + $sunRightAscensionHours - (0.06571 * $approxTimeDays) - 6.622;
	}

	/*
	|--------------------------------------------------------------------------
	| CALCULATIONS
	|--------------------------------------------------------------------------
	*/

	private function getTimeUTC(Carbon $date, GeoLocation $geo, float $zenith, bool $isSunrise): float
	{
		$dayOfYear = $date->dayOfYear;
		$sunMeanAnomaly = self::getMeanAnomaly($dayOfYear, $geo->getLongitude(), $isSunrise);
		$sunTrueLong = self::getSunTrueLongitude($sunMeanAnomaly);
		$sunRightAscensionHours = self::getSunRightAscensionHours($sunTrueLong);
		$cosLocalHourAngle = self::getCosLocalHourAngle($sunTrueLong, $geo->getLatitude(), $zenith);

		if ($isSunrise) {
			$localHourAngle = 360.0 - rad2deg(acos($cosLocalHourAngle));
		} else {
			$localHourAngle = rad2deg(acos($cosLocalHourAngle));
		}
		$localHour = $localHourAngle / self::DEG_PER_HOUR;

		$localMeanTime = self::getLocalMeanTime($localHour, $sunRightAscensionHours,
				self::getApproxTimeDays($dayOfYear, self::getHoursFromMeridian($geo->getLongitude()), $isSunrise));
		$processedTime = $localMeanTime - self::getHoursFromMeridian($geo->getLongitude());
		return fmod(fmod($processedTime, 24) + 24, 24);
	}
}
