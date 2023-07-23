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

namespace PhpZmanim\Calendar;

use ArgumentCountError;
use Carbon\Carbon;
use Exception;
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calculator\NoaaCalculator;
use PhpZmanim\Calculator\SunTimesCalculator;
use PhpZmanim\Geo\GeoLocation;

/**
 * See https://github.com/KosherJava/zmanim/blob/master/src/main/java/com/kosherjava/zmanim/AstronomicalCalendar.java
 * for more detailed explanations regarding the methods and variables on this page.
 */
class AstronomicalCalendar {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private Carbon $calendar;
	private GeoLocation $geoLocation; 
	private AstronomicalCalculator $astronomicalCalculator;

	const GEOMETRIC_ZENITH = 90;
	const CIVIL_ZENITH = 96;
	const NAUTICAL_ZENITH = 102;
	const ASTRONOMICAL_ZENITH = 108;

	const MINUTE_MILLIS = 60 * 1000;
	const HOUR_MILLIS = self::MINUTE_MILLIS * 60;

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTOR
	|--------------------------------------------------------------------------
	*/

	public function __construct(GeoLocation $geoLocation = null, $year = null, $month = null, $day = null) {
		if (is_null($geoLocation)) {
			$geoLocation = new GeoLocation();
		}

		if (!is_null($year) && !is_null($month) && !is_null($day)) {
			$calendar = Carbon::createFromDate($year, $month, $day);
		}

		$this->setCalendar($calendar ?? Carbon::now("UTC"));
		$this->setGeoLocation($geoLocation); // duplicate call
		$this->setAstronomicalCalculator(AstronomicalCalculator::getDefault());
	}

	/*
	|--------------------------------------------------------------------------
	| SUNRISE
	|--------------------------------------------------------------------------
	*/

	public function getSunrise() {
		$sunrise = $this->getUTCSunrise(self::GEOMETRIC_ZENITH);
		if (is_nan($sunrise)) {
			return null;
		} else {
			return $this->getDateFromTime($sunrise, true);
		}
	}

	public function getSeaLevelSunrise() {
		$sunrise = $this->getUTCSeaLevelSunrise(self::GEOMETRIC_ZENITH);
		if (is_nan($sunrise)) {
			return null;
		} else {
			return $this->getDateFromTime($sunrise, true);
		}
	}

	public function getBeginCivilTwilight() {
		return $this->getSunriseOffsetByDegrees(self::CIVIL_ZENITH);
	}

	public function getBeginNauticalTwilight() {
		return $this->getSunriseOffsetByDegrees(self::NAUTICAL_ZENITH);
	}

	public function getBeginAstronomicalTwilight() {
		return $this->getSunriseOffsetByDegrees(self::ASTRONOMICAL_ZENITH);
	}

	/*
	|--------------------------------------------------------------------------
	| SUNSET
	|--------------------------------------------------------------------------
	*/

	public function getSunset() {
		$sunset = $this->getUTCSunset(self::GEOMETRIC_ZENITH);
		if (is_nan($sunset)) {
			return null;
		} else {
			return $this->getDateFromTime($sunset, false);
		}
	}

	public function getSeaLevelSunset() {
		$sunset = $this->getUTCSeaLevelSunset(self::GEOMETRIC_ZENITH);
		if (is_nan($sunset)) {
			return null;
		} else {
			return $this->getDateFromTime($sunset, false);
		}
	}

	public function getEndCivilTwilight() {
		return $this->getSunsetOffsetByDegrees(self::CIVIL_ZENITH);
	}

	public function getEndNauticalTwilight() {
		return $this->getSunsetOffsetByDegrees(self::NAUTICAL_ZENITH);
	}

	public function getEndAstronomicalTwilight() {
		return $this->getSunsetOffsetByDegrees(self::ASTRONOMICAL_ZENITH);
	}

	/*
	|--------------------------------------------------------------------------
	| UTILITIES
	|--------------------------------------------------------------------------
	*/

	public function getTimeOffset($time, $offset) {
		if ($time == null || $offset == null) {
			return null;
		}

		return Carbon::createFromTimestamp($time->format("U.u") + ($offset / 1000), $time->getTimeZone());
	}

	public function getSunriseOffsetByDegrees($offsetZenith) {
		$dawn = $this->getUTCSunrise($offsetZenith);
		if (is_nan($dawn)) {
			return null;
		} else {
			return $this->getDateFromTime($dawn, true);
		}
	}

	public function getSunsetOffsetByDegrees($offsetZenith) {
		$sunset = $this->getUTCSunset($offsetZenith);
		if (is_nan($sunset)) {
			return null;
		} else {
			return $this->getDateFromTime($sunset, false);
		}
	}

	public function getUTCSunrise($zenith) {
		return $this->getAstronomicalCalculator()->getUTCSunrise($this->getAdjustedCalendar(), $this->getGeoLocation(), $zenith, true);
	}

	public function getUTCSeaLevelSunrise($zenith) {
		return $this->getAstronomicalCalculator()->getUTCSunrise($this->getAdjustedCalendar(), $this->getGeoLocation(), $zenith, false);
	}

	public function getUTCSunset($zenith) {
		return $this->getAstronomicalCalculator()->getUTCSunset($this->getAdjustedCalendar(), $this->getGeoLocation(), $zenith, true);
	}

	public function getUTCSeaLevelSunset($zenith) {
		return $this->getAstronomicalCalculator()->getUTCSunset($this->getAdjustedCalendar(), $this->getGeoLocation(), $zenith, false);
	}

	public function getTemporalHour(Carbon $startOfDay = null, Carbon $endOfDay = null) {
		if ($startOfDay == null && $endOfDay == null) {
			$startOfDay = $this->getSeaLevelSunrise();
			$endOfDay = $this->getSeaLevelSunset();
		}

		if ($startOfDay == null || $endOfDay == null) {
			return null;
		}

		$startOfDayTotal = $startOfDay->getPreciseTimestamp();
		$endOfDayTotal = $endOfDay->getPreciseTimestamp();

		$dayTimeHours = $endOfDayTotal - $startOfDayTotal;
		return $dayTimeHours / 12000;
	}

	public function getSunTransit(Carbon $startOfDay = null, Carbon $endOfDay = null) {
		if ($startOfDay == null && $endOfDay == null) {
			$noon = $this->getAstronomicalCalculator()->getUTCNoon($this->getAdjustedCalendar(), $this->getGeoLocation());

			return $this->getDateFromTime($noon, false);
		}

		$temporalHour = $this->getTemporalHour($startOfDay, $endOfDay);
		if (is_null($temporalHour)) {
			return null;
		}

		return $this->getTimeOffset($startOfDay, $temporalHour * 6);
	}

	protected function getDateFromTime($time, $isSunrise) {
		if (is_nan($time)) {
			return null;
		}

		$calculatedTime = $time;

		$hours = (int) $calculatedTime; // retain only the hours
		$calculatedTime -= $hours;
		$minutes = (int) ($calculatedTime *= 60); // retain only the minutes
		$calculatedTime -= $minutes;
		$seconds = (int) ($calculatedTime *= 60); // retain only the seconds
		$calculatedTime -= $seconds;
		$microseconds = $calculatedTime * 1000000;

		// Check if a date transition has occurred, or is about to occur - this indicates the date of the event is
		// actually not the target date, but the day prior or after

		$adjustedCalendar = $this->getAdjustedCalendar();
		$calendar = Carbon::create($adjustedCalendar->year, $adjustedCalendar->month, $adjustedCalendar->day, $hours, $minutes, $seconds, "UTC");

		$localTimeHours = (int) $this->getGeoLocation()->getLongitude() / 15;
		if ($isSunrise && $localTimeHours + $hours > 18) {
			$calendar->subDay();
		} else if (!$isSunrise && $localTimeHours + $hours < 6) {
			$calendar->addDay();
		}

		$calendar->setTime($hours, $minutes, $seconds, $microseconds);
		$calendar->setTimeZone($this->getGeoLocation()->getTimeZone());
		return $calendar;
	}

	public function getSunriseSolarDipFromOffset($minutes) {
		$offsetByDegrees = $this->getSeaLevelSunrise();
		$offsetByTime = $this->getTimeOffset($this->getSeaLevelSunrise(), -($minutes * self::MINUTE_MILLIS));

		$degrees = 0;
		$incrementor = 0.0001;
		while (
			$offsetByDegrees == null ||
			($minutes < 0.0 && $offsetByDegrees->lt($offsetByTime)) ||
			($minutes > 0.0 && $offsetByDegrees->gt($offsetByTime))
		) {
			if ($minutes > 0.0) {
				$degrees = $degrees + $incrementor;
			} else {
				$degrees = $degrees - $incrementor;
			}
			$offsetByDegrees = $this->getSunriseOffsetByDegrees(self::GEOMETRIC_ZENITH + $degrees);
		}
		return $degrees;
	}

	public function getSunsetSolarDipFromOffset($minutes) {
		$offsetByDegrees = $this->getSeaLevelSunset();
		$offsetByTime = $this->getTimeOffset($this->getSeaLevelSunset(), $minutes * GeoLocation::MINUTE_MILLIS);

		$degrees = 0;
		$incrementor = 0.0001;
		while (
			$offsetByDegrees == null ||
			($minutes > 0.0 && $offsetByDegrees->lt($offsetByTime)) ||
			($minutes < 0.0 && $offsetByDegrees->gt($offsetByTime))
		) {
			if ($minutes > 0.0) {
				$degrees = $degrees + $incrementor;
			} else {
				$degrees = $degrees - $incrementor;
			}
			$offsetByDegrees = $this->getSunsetOffsetByDegrees(self::GEOMETRIC_ZENITH + $degrees);
		}
		return $degrees;
	}

	/*
	|--------------------------------------------------------------------------
	| HELPERS
	|--------------------------------------------------------------------------
	*/

	private function getAdjustedCalendar() {
		$offset = $this->getGeoLocation()->getAntimeridianAdjustment();
		if ($offset == 0) {
			return $this->getCalendar();
		}
		$adjustedCalendar = $this->getCalendar()->copy();
		$adjustedCalendar->addDays($offset);
		return $adjustedCalendar;
	}

	public function equals($astronomicalCalendar) {
		if ($this == $astronomicalCalendar) {
			return true;
		}
		if (!($object instanceof AstronomicalCalendar)) {
			return false;
		}

		return $this->getCalendar()->eq($astronomicalCalendar->getCalendar()) &&
			$this->getGeoLocation()->equals($astronomicalCalendar->getGeoLocation()) &&
			$this->getAstronomicalCalculator()->getCalculatorName() == $astronomicalCalendar->getAstronomicalCalculator()->getCalculatorName();
	}

	public function getGeoLocation() {
		return $this->geoLocation;
	}

	public function setGeoLocation(GeoLocation $geoLocation) {
		$this->geoLocation = $geoLocation;
	}

	public function getAstronomicalCalculator() {
		return $this->astronomicalCalculator;
	}

	public function setAstronomicalCalculator(AstronomicalCalculator $astronomicalCalculator) {
		$this->astronomicalCalculator = $astronomicalCalculator;
	}

	public function getCalendar() {
		return $this->calendar;
	}

	public function setCalendar(Carbon $calendar) {
		$calendar->startOfDay();
		$this->calendar = $calendar;
	}

	public function setDate($year, $month, $day) {
		$this->getCalendar()->setDate($year, $month, $day);
	}

	public function addDays($value) {
		$this->getCalendar()->addDays($value);
	}

	public function subDays($value) {
		$this->getCalendar()->subDays($value);
	}

	public function copy() {
		$astronomicalCalculator = clone $this;
		$astronomicalCalculator->setGeoLocation($this->getGeoLocation()->copy());
		$astronomicalCalculator->setCalendar($this->getCalendar()->copy());
		$astronomicalCalculator->setAstronomicalCalculator($this->getAstronomicalCalculator()->copy());

		return $astronomicalCalculator;
	}

	public function setCalculatorType($type) {
		switch ($type) {
			case 'SunTimes':
				$this->setAstronomicalCalculator(new SunTimesCalculator());
				break;
			
			case 'Noaa':
				$this->setAstronomicalCalculator(new NoaaCalculator());
				break;
			
			default:
				throw new \Exception("Only SunTimes and Noaa are implemented currently");
				break;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| MAGIC GETTERS
	|--------------------------------------------------------------------------
	*/

	public function get($zman, ...$args) {
		$method_name = "get" . ucfirst($zman);
		if (method_exists($this, $method_name)) {
			return $this->$method_name(...$args);
		} else {
			throw new Exception("Requested Zman does not exist");
		}
	}

	public function __get($arg) {
		$response = null;

		try {
			$response = $this->get($arg);
		} catch (ArgumentCountError $e) {
			$response = null;
		} catch (Exception $e) {
			$response = null;
		}

		return $response;
	}
}
