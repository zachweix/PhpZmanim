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

class SunTimesCalculator extends AstronomicalCalculator {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	const CALCULATOR_NAME = "US Naval Almanac Algorithm";
	const DEG_PER_HOUR = 360.0 / 24.0;

	/*
	|--------------------------------------------------------------------------
	| STATIC FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	private static function sinDeg($deg) {
		return sin($deg * 2.0 * pi() / 360.0);
	}

	private static function acosDeg($x) {
		return acos($x) * 360.0 / (2 * pi());
	}

	private static function asinDeg($x) {
		return asin($x) * 360.0 / (2 * pi());
	}

	private static function tanDeg($deg) {
		return tan($deg * 2.0 * pi() / 360.0);
	}

	private static function cosDeg($deg) {
		return cos($deg * 2.0 * pi() / 360.0);
	}

	private static function getHoursFromMeridian($longitude) {
		return $longitude / self::DEG_PER_HOUR;
	}

	private static function getApproxTimeDays($dayOfYear, $hoursFromMeridian, $isSunrise) {
		if ($isSunrise) {
			return $dayOfYear + ((6.0 - $hoursFromMeridian) / 24);
		} else {
			return $dayOfYear + ((18.0 - $hoursFromMeridian) / 24);
		}
	}

	private static function getMeanAnomaly($dayOfYear, $longitude, $isSunrise) {
		$hoursFromMeridian = self::getHoursFromMeridian($longitude);
		$approxTimeDays = self::getApproxTimeDays($dayOfYear, $hoursFromMeridian, $isSunrise);
		$meanAnomaly = (0.9856 * $approxTimeDays) - 3.289;
		return $meanAnomaly;
	}

	private static function getSunTrueLongitude($sunMeanAnomaly) {
		$l = $sunMeanAnomaly + (1.916 * self::sinDeg($sunMeanAnomaly)) + (0.020 * self::sinDeg(2 * $sunMeanAnomaly)) + 282.634;

		if ($l >= 360.0) {
			$l -= 360.0;
		}
		if ($l < 0) {
			$l += 360.0;
		}

		return $l;
	}

	private static function getSunRightAscensionHours($sunTrueLongitude) {
		$a = 0.91764 * self::tanDeg($sunTrueLongitude);
		$ra = 360.0 / (2.0 * pi()) * atan($a);

		$lQuadrant = floor($sunTrueLongitude / 90.0) * 90.0;
		$raQuadrant = floor($ra / 90.0) * 90.0;
		$ra = $ra + ($lQuadrant - $raQuadrant);

		return $ra / self::DEG_PER_HOUR;
	}

	private static function getCosLocalHourAngle($sunTrueLongitude, $latitude, $zenith) {
		$sinDec = 0.39782 * self::sinDeg($sunTrueLongitude);
		$cosDec = self::cosDeg(self::asinDeg($sinDec));

		$cosLocalHourAngle = (self::cosDeg($zenith) - ($sinDec * self::sinDeg($latitude))) / ($cosDec * self::cosDeg($latitude));
		return $cosLocalHourAngle;
	}

	private static function getLocalMeanTime($localHour, $sunRightAscensionHours, $approxTimeDays) {
		return $localHour + $sunRightAscensionHours - (0.06571 * $approxTimeDays) - 6.622;
	}

	private static function getTimeUTC($calendar, $geoLocation, $zenith, $isSunrise) {
		$dayOfYear = $calendar->dayOfYear;
		$sunMeanAnomaly = self::getMeanAnomaly($dayOfYear, $geoLocation->getLongitude(), $isSunrise);
		$sunTrueLong = self::getSunTrueLongitude($sunMeanAnomaly);
		$sunRightAscensionHours = self::getSunRightAscensionHours($sunTrueLong);
		$cosLocalHourAngle = self::getCosLocalHourAngle($sunTrueLong, $geoLocation->getLatitude(), $zenith);

		$localHourAngle = 0;
		if ($isSunrise) {
			$localHourAngle = 360.0 - self::acosDeg($cosLocalHourAngle);
		} else {
			$localHourAngle = self::acosDeg($cosLocalHourAngle);
		}

		$localHour = $localHourAngle / self::DEG_PER_HOUR;

		$approxTimeDays = self::getApproxTimeDays($dayOfYear, self::getHoursFromMeridian($geoLocation->getLongitude()), $isSunrise);
		$localMeanTime = self::getLocalMeanTime($localHour, $sunRightAscensionHours, $approxTimeDays);
		$processedTime = $localMeanTime - self::getHoursFromMeridian($geoLocation->getLongitude());

		while ($processedTime < 0.0) {
			$processedTime += 24.0;
		}
		while ($processedTime >= 24.0) {
			$processedTime -= 24.0;
		}

		return $processedTime;
	}

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getUTCSunrise(Carbon $calendar, GeoLocation $geoLocation, $zenith, $adjustForElevation) {
		$elevation = $adjustForElevation ? $geoLocation->getElevation() : 0;
		$adjustedZenith = $this->adjustZenith($zenith, $elevation);
		$doubleTime = self::getTimeUTC($calendar, $geoLocation, $adjustedZenith, true);
		return $doubleTime;
	}

	public function getUTCSunset(Carbon $calendar, GeoLocation $geoLocation, $zenith, $adjustForElevation) {
		$elevation = $adjustForElevation ? $geoLocation->getElevation() : 0;
		$adjustedZenith = $this->adjustZenith($zenith, $elevation);
		$doubleTime = self::getTimeUTC($calendar, $geoLocation, $adjustedZenith, false);
		return $doubleTime;
	}
}