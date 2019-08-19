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

namespace PhpZmanim\Calendar;

use Carbon\Carbon;
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Geo\GeoLocation;

/**
 * See https://github.com/KosherJava/zmanim/blob/master/src/net/sourceforge/zmanim/AstronomicalCalendar.java
 * for more detailed explanations regarding the methods and variables on this page.
 */
class AstronomicalCalendar {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private $calendar;
	private $geoLocation; 
	private $astronomicalCalculator;

	const GEOMETRIC_ZENITH = 90;
	const CIVIL_ZENITH = 96;
	const NAUTICAL_ZENITH = 102;
	const ASTRONOMICAL_ZENITH = 108;

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

		$this->setCalendar($calendar ?? Carbon::now());
		$this->setGeoLocation($geoLocation); // duplicate call
		$this->setAstronomicalCalculator(AstronomicalCalculator::getDefault());
	}

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
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

	public function getCalendar() {
		return $this->calendar;
	}

	public function setCalendar(Carbon $calendar) {
		$calendar->startOfDay();
		$this->calendar = $calendar;
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

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getUTCSunrise($zenith) {
		return $this->getAstronomicalCalculator()->getUTCSunrise($this->getAdjustedCalendar(), $this->getGeoLocation(), $zenith, true);
	}

	public function getUTCSeaLevelSunrise($zenith) {
		return $this->getAstronomicalCalculator()->getUTCSunrise($this->getAdjustedCalendar(), $this->getGeoLocation(), $zenith, false);
	}

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

	public function getSunriseOffsetByDegrees($offsetZenith) {
		$dawn = $this->getUTCSunrise($offsetZenith);
		if (is_nan($dawn)) {
			return null;
		} else {
			return $this->getDateFromTime($dawn, true);
		}
	}

	public function getSunriseSolarDipFromOffset($minutes) {
		$offsetByDegrees = $this->getSeaLevelSunrise();
		$offsetByTime = $this->getTimeOffset($this->getSeaLevelSunrise(), -($minutes * GeoLocation::MINUTE_MILLIS));

		$degrees = 0;
		$incrementor = 0.0001;
		while ($offsetByDegrees == null || $offsetByDegrees->gt($offsetByTime)) {
			$degrees = $degrees + $incrementor;
			$offsetByDegrees = $this->getSunriseOffsetByDegrees(self::GEOMETRIC_ZENITH + $degrees);
		}
		return $degrees;
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

	public function getUTCSunset($zenith) {
		return $this->getAstronomicalCalculator()->getUTCSunset($this->getAdjustedCalendar(), $this->getGeoLocation(), $zenith, true);
	}

	public function getUTCSeaLevelSunset($zenith) {
		return $this->getAstronomicalCalculator()->getUTCSunset($this->getAdjustedCalendar(), $this->getGeoLocation(), $zenith, false);
	}

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

	public function getSunsetOffsetByDegrees($offsetZenith) {
		$sunset = $this->getUTCSunset($offsetZenith);
		if (is_nan($sunset)) {
			return null;
		} else {
			return $this->getDateFromTime($sunset, false);
		}
	}

	public function getSunsetSolarDipFromOffset($minutes) {
		$offsetByDegrees = $this->getSeaLevelSunset();
		$offsetByTime = $this->getTimeOffset($this->getSeaLevelSunset(), $minutes * GeoLocation::MINUTE_MILLIS);

		$degrees = 0;
		$incrementor = 0.0001;
		while ($offsetByDegrees == null || $offsetByDegrees->lt($offsetByTime)) {
			$degrees = $degrees + $incrementor;
			$offsetByDegrees = $this->getSunsetOffsetByDegrees(self::GEOMETRIC_ZENITH + $degrees);
		}
		return $degrees;
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
	| HELPER METHODS
	|--------------------------------------------------------------------------
	*/

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
		$microseconds = $calculatedTime * 1000000; // remaining microseconds

		// Check if a date transition has occurred, or is about to occur - this indicates the date of the event is
		// actually not the target date, but the day prior or after

		$adjustedCalendar = $this->getAdjustedCalendar();
		$calendar = Carbon::create($adjustedCalendar->year, $adjustedCalendar->month, $adjustedCalendar->day, $hours, $minutes, $seconds, "UTC");

		$localOffset = (($this->getGeoLocation()->getLocalMeanTimeOffset() + $this->getGeoLocation()->getStandardTimeOffset()) / GeoLocation::HOUR_MILLIS);
		if ($isSunrise && $localOffset + $hours > 18) {
			$calendar->subDay();
		} else if (!$isSunrise && $localOffset + $hours < 6) {
			$calendar->addDay();
		}

		$calendar->setTime($hours, $minutes, $seconds, $microseconds);
		$calendar->setTimeZone($this->getGeoLocation()->getTimeZone());
		return $calendar;
	}

	public function getTimeOffset($time, $offset) {
		if ($time == null || $offset == null) {
			return null;
		}

		return Carbon::createFromTimestamp($time->format("U.u") + $offset, $time->getTimeZone());
	}

	public function getTemporalHour(Carbon $startOfDay = null, Carbon $endOfDay = null) {
		if ($startOfDay == null && $endOfDay == null) {
			$startOfDay = $this->getSeaLevelSunrise();
			$endOfDay = $this->getSeaLevelSunset();
		}

		if ($startOfDay == null || $endOfDay == null) {
			return null;
		}

		$startOfDayTotal = $startOfDay->format('U.u');
		$endOfDayTotal = $endOfDay->format('U.u');

		$dayTimeHours = $endOfDayTotal - $startOfDayTotal;
		return $dayTimeHours / 12;
	}

	public function getSunTransit(Carbon $startOfDay = null, Carbon $endOfDay = null) {
		if ($startOfDay == null && $endOfDay == null) {
			$startOfDay = $this->getSeaLevelSunrise();
			$endOfDay = $this->getSeaLevelSunset();
		}

		if ($startOfDay == null || $endOfDay == null) {
			return null;
		}

		$temporalHour = $this->getTemporalHour($startOfDay, $endOfDay);
		return $this->getTimeOffset($startOfDay, $temporalHour * 6);
	}

	public function copy() {
		$astronomicalCalculator = clone $this;
		$astronomicalCalculator->setGeoLocation($this->getGeoLocation->copy());
		$astronomicalCalculator->setCalendar($this->getCalendar->copy());
		$astronomicalCalculator->setAstronomicalCalculator($this->getAstronomicalCalculator->copy());

		return $astronomicalCalculator;
	}
}