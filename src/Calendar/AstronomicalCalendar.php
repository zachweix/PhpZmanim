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
use InvalidArgumentException;
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calculator\NoaaCalculator;
use PhpZmanim\Calculator\SunTimesCalculator;
use PhpZmanim\GeoLocation;

class AstronomicalCalendar
{
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private Carbon $date;
	private GeoLocation $geoLocation; 
	private AstronomicalCalculator $astronomicalCalculator;

	const GEOMETRIC_ZENITH = 90;
	const CIVIL_ZENITH = 96;
	const NAUTICAL_ZENITH = 102;
	const ASTRONOMICAL_ZENITH = 108;

	const MINUTE_MILLIS = 60 * 1000;
	const HOUR_MILLIS = self::MINUTE_MILLIS * 60;

	const SUNRISE = 0;
	const SUNSET = 1;
	const NOON = 2;
	const MIDNIGHT = 3;

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTOR
	|--------------------------------------------------------------------------
	*/

	public function __construct(Carbon|int|null $year = null, ?int $month = null, ?int $day = null, ?GeoLocation $geoLocation = null, ?AstronomicalCalculator $calculator = null)
	{
		$this->geoLocation = $geoLocation?->copy() ?? GeoLocation::create();
		$this->astronomicalCalculator = $calculator?->copy() ?? AstronomicalCalculator::getDefault();
		$this->setDate($year, $month, $day);
	}

	public static function create(Carbon|int|null $year = null, ?int $month = null, ?int $day = null,
		$latitude = 51.4772, $longitude = 0.0, $elevation = 0.0, $timezone = 'GMT') {
		$geoLocation = GeoLocation::create($latitude, $longitude, $elevation, $timezone);

		return new static($year, $month, $day, $geoLocation);
	}

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function getDate(): Carbon
	{
		return $this->date->copy();
	}

	public function setDate(Carbon|int|null $date = null, ?int $month = null, ?int $day = null): self
	{
		if ($date instanceof Carbon) {
			$this->date = $date->copy()->startOfDay();

			return $this;
		}

		$allNull = is_null($date) && is_null($month) && is_null($day);
		$allSet = !is_null($date) && !is_null($month) && !is_null($day);

		if (!$allNull && !$allSet) {
			throw new InvalidArgumentException('You must either provide a year, month and day or leave them all blank');
		}

		$this->date = ($allNull
			? Carbon::now($this->geoLocation->getTimezone())
			: Carbon::create($date, $month, $day, 0, 0, 0, $this->geoLocation->getTimezone())
		)->startOfDay();

		return $this;
	}

	public function addDays($value): self
	{
		$this->date->addDays($value);

		return $this;
	}

	public function subDays($value): self
	{
		$this->date->subDays($value);

		return $this;
	}

	public function getGeoLocation(): GeoLocation
	{
		return $this->geoLocation->copy();
	}

	public function setGeoLocation(GeoLocation $geoLocation): self
	{
		$this->geoLocation = $geoLocation->copy();

		return $this;
	}

	public function getAstronomicalCalculator(): AstronomicalCalculator
	{
		return $this->astronomicalCalculator->copy();
	}

	public function setAstronomicalCalculator(AstronomicalCalculator $astronomicalCalculator): self
	{
		$this->astronomicalCalculator = $astronomicalCalculator->copy();

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| SUNRISE
	|--------------------------------------------------------------------------
	*/

	public function getSunrise(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::GEOMETRIC_ZENITH);
	}

	public function getBeginCivilTwilight(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::CIVIL_ZENITH);
	}

	public function getBeginNauticalTwilight(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::NAUTICAL_ZENITH);
	}

	public function getBeginAstronomicalTwilight(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ASTRONOMICAL_ZENITH);
	}

	public function getSeaLevelSunrise(): Carbon|null
	{
		return $this->toAdjustedCarbon($this->getUTCSunrise(self::GEOMETRIC_ZENITH, false), self::SUNRISE);
	}

	public function getSunriseOffsetByDegrees(float $offsetZenith): Carbon|null
	{
		return $this->toAdjustedCarbon($this->getUTCSunrise($offsetZenith), self::SUNRISE);
	}

	/*
	|--------------------------------------------------------------------------
	| SUNSET
	|--------------------------------------------------------------------------
	*/

	public function getSunset(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::GEOMETRIC_ZENITH);
	}

	public function getEndCivilTwilight(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::CIVIL_ZENITH);
	}

	public function getEndNauticalTwilight(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::NAUTICAL_ZENITH);
	}

	public function getEndAstronomicalTwilight(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ASTRONOMICAL_ZENITH);
	}

	public function getSeaLevelSunset(): Carbon|null
	{
		return $this->toAdjustedCarbon($this->getUTCSunset(self::GEOMETRIC_ZENITH, false), self::SUNSET);
	}

	public function getSunsetOffsetByDegrees(float $offsetZenith): Carbon|null
	{
		return $this->toAdjustedCarbon($this->getUTCSunset($offsetZenith), self::SUNSET);
	}

	/*
	|--------------------------------------------------------------------------
	| NOON
	|--------------------------------------------------------------------------
	*/

	public function getSunTransit(): Carbon|null
	{
		$noon = $this->astronomicalCalculator->getUTCNoon($this->getAdjustedDate(), $this->geoLocation);

		return $this->toAdjustedCarbon($noon, self::NOON);
	}

	/*
	|--------------------------------------------------------------------------
	| MIDNIGHT
	|--------------------------------------------------------------------------
	*/

	public function getSolarMidnight(): Carbon|null
	{
		$midnight = $this->astronomicalCalculator->getUTCMidnight($this->getAdjustedDate(), $this->geoLocation);

		return $this->toAdjustedCarbon($midnight, self::MIDNIGHT);
	}

	/*
	|--------------------------------------------------------------------------
	| TIME AT AZIMUTH
	|--------------------------------------------------------------------------
	*/

	public function getTimeAtAzimuth90Or270(float $azimuth): Carbon|null
	{
		$time = $this->astronomicalCalculator->getTimeAtAzimuth($this->getAdjustedDate(), $this->geoLocation, $azimuth);

		return $this->toAdjustedCarbon($time, $azimuth == 90 ? self::SUNRISE : self::SUNSET);
	}

	/*
	|--------------------------------------------------------------------------
	| UTILITIES
	|--------------------------------------------------------------------------
	*/

	protected function getUTCSunrise(float $zenith, bool $adjustForElevation = true): float
	{
		return $this->astronomicalCalculator->getUTCSunrise($this->getAdjustedDate(), $this->geoLocation, $zenith, $adjustForElevation);
	}

	protected function getUTCSunset(float $zenith, bool $adjustForElevation = true): float
	{
		return $this->astronomicalCalculator->getUTCSunset($this->getAdjustedDate(), $this->geoLocation, $zenith, $adjustForElevation);
	}

	protected function toAdjustedCarbon(float $time, int $solarEvent): Carbon|null
	{
		if (is_nan($time)) {
			return null;
		}

		$date = $this->getAdjustedDate();
		$localTimeHours = $this->geoLocation->getLongitude() / 15 + $time;

		// A date transition may have occurred: the event actually falls on the day before or after the target date.
		$dayOffset = match (true) {
			$solarEvent === self::SUNRISE  && $localTimeHours > 18 => -1,
			$solarEvent === self::SUNSET   && $localTimeHours < 6  => 1,
			$solarEvent === self::MIDNIGHT && $localTimeHours < 12 => 1,
			$solarEvent === self::NOON     && $localTimeHours < 0  => 1,
			$solarEvent === self::NOON     && $localTimeHours > 24 => -1,
			default => 0,
		};

		// The computed time is UTC fractional hours; anchor midnight in UTC, then convert to the location's zone.
		return Carbon::create($date->year, $date->month, $date->day, 0, 0, 0, 'UTC')
			->addDays($dayOffset)
			->addMicroseconds((int) round($time * self::HOUR_MILLIS * 1000))
			->setTimezone($this->geoLocation->getTimezone());
	}

	public function getSunriseSolarDipFromOffset(float $minutes): float
	{
		if ($minutes == 0.0) {
			return 0.0;
		}
		if (is_nan($minutes)) {
			return NAN;
		}

		$seaLevelSunrise = $this->getSeaLevelSunrise();
		if ($seaLevelSunrise === null) {
			return NAN;
		}

		$offsetByTime = $this->getTimeOffset($seaLevelSunrise, -($minutes * self::MINUTE_MILLIS));

		$degrees = 0.0;
		$incrementor = 0.0001;

		do {
			$degrees += $minutes > 0.0 ? $incrementor : -$incrementor;

			$offsetByDegrees = $this->getSunriseOffsetByDegrees(self::GEOMETRIC_ZENITH + $degrees);

			if ($offsetByDegrees === null || abs($degrees) > 30.0) {
				return NAN;
			}
		} while (
			($minutes > 0.0 && $offsetByDegrees->gt($offsetByTime)) ||
			($minutes < 0.0 && $offsetByDegrees->lt($offsetByTime))
		);

		return $degrees;
	}

	public function getSunsetSolarDipFromOffset(float $minutes): float
	{
		if ($minutes == 0.0) {
			return 0.0;
		}
		if (is_nan($minutes)) {
			return NAN;
		}

		$seaLevelSunset = $this->getSeaLevelSunset();
		if ($seaLevelSunset === null) {
			return NAN;
		}

		$offsetByTime = $this->getTimeOffset($seaLevelSunset, $minutes * self::MINUTE_MILLIS);

		$degrees = 0.0;
		$incrementor = 0.0001;

		do {
			$degrees += $minutes > 0.0 ? $incrementor : -$incrementor;

			$offsetByDegrees = $this->getSunsetOffsetByDegrees(self::GEOMETRIC_ZENITH + $degrees);

			if ($offsetByDegrees === null || abs($degrees) > 30.0) {
				return NAN;
			}
		} while (
			($minutes > 0.0 && $offsetByDegrees->lt($offsetByTime)) ||
			($minutes < 0.0 && $offsetByDegrees->gt($offsetByTime))
		);

		return $degrees;
	}

	protected static function getTimeOffset(Carbon $time, float $offset): Carbon
	{
		return $time->copy()->addMicroseconds((int) round($offset * 1000));
	}

	protected static function getTemporalHour(Carbon $startOfDay, Carbon $endOfDay): float
	{
		$startOfDayTotal = $startOfDay->getPreciseTimestamp();
		$endOfDayTotal = $endOfDay->getPreciseTimestamp();

		$dayTimeHours = $endOfDayTotal - $startOfDayTotal;
		return $dayTimeHours / 12000;
	}

	/*
	|--------------------------------------------------------------------------
	| HELPERS
	|--------------------------------------------------------------------------
	*/

	public function getLocalMeanTime(float $hours): Carbon|null
	{
		if ($hours < 0 || $hours >= 24) {
			throw new InvalidArgumentException('Hours must be between 0 and 23.9999...');
		}

		$date = $this->getAdjustedDate();
		$localMeanTime = Carbon::create($date->year, $date->month, $date->day, 0, 0, 0, 'UTC')
			->addMicroseconds((int) round($hours * self::HOUR_MILLIS * 1000));

		return $this->getTimeOffset($localMeanTime, -($this->geoLocation->getLongitude() * 4 * self::MINUTE_MILLIS));
	}

	protected function getAdjustedDate(): Carbon
	{
		$offset = $this->geoLocation->getAntimeridianAdjustment($this->getMidnightLastNight());

		return $offset == 0 ? $this->date->copy() : $this->date->copy()->addDays($offset);
	}

	protected function getMidnightLastNight(): Carbon
	{
		return $this->date->copy()->startOfDay();
	}

	protected function getMidnightTonight(): Carbon
	{
		return $this->date->copy()->addDay()->startOfDay();
	}

	/*
	|--------------------------------------------------------------------------
	| CLONEABLE AND COMPARABLE
	|--------------------------------------------------------------------------
	*/

	public function __clone()
	{
		$this->date = $this->date->copy();
		$this->geoLocation = $this->geoLocation->copy();
		$this->astronomicalCalculator = $this->astronomicalCalculator->copy();
	}

	public function copy(): self
	{
		return clone $this;
	}

	public function equals(AstronomicalCalendar $astronomicalCalendar): bool
	{
		if ($this === $astronomicalCalendar) {
			return true;
		}

		return $this->date->eq($astronomicalCalendar->getDate()) &&
			$this->geoLocation->equals($astronomicalCalendar->getGeoLocation()) &&
			$this->astronomicalCalculator->equals($astronomicalCalendar->getAstronomicalCalculator());
	}
}
