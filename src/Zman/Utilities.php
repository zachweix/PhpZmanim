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

namespace PhpZmanim\Zman;

use Carbon\Carbon;
use PhpZmanim\Zman;

/**
 * @property Carbon $date;
 * @property GeoLocation $geoLocation;
 * @property AstronomicalCalculator $astronomicalCalculator;
 * @property bool $useElevation;
 * @property int $candleLightingOffset;
 * @property bool $useAstronomicalChatzos;
 * @property bool $useAstronomicalChatzosForOtherZmanim;
 * @property float $ateretTorahSunsetOffset;
 */
trait Utilities
{
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
			$solarEvent === Zman::SUNRISE  && $localTimeHours > 18 => -1,
			$solarEvent === Zman::SUNSET   && $localTimeHours < 6  => 1,
			$solarEvent === Zman::MIDNIGHT && $localTimeHours < 12 => 1,
			$solarEvent === Zman::NOON     && $localTimeHours < 0  => 1,
			$solarEvent === Zman::NOON     && $localTimeHours > 24 => -1,
			default => 0,
		};

		// The computed time is UTC fractional hours; anchor midnight in UTC, then convert to the location's zone.
		return Carbon::create($date->year, $date->month, $date->day, 0, 0, 0, 'UTC')
			->addDays($dayOffset)
			->addMicroseconds((int) round($time * Zman::HOUR_MILLIS * 1000))
			->setTimezone($this->geoLocation->getTimezone());
	}

	/**
	 * @deprecated See KosherJava for details.
	 */
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

		$offsetByTime = $this->getTimeOffset($seaLevelSunrise, -($minutes * Zman::MINUTE_MILLIS));

		$degrees = 0.0;
		$incrementor = 0.0001;

		do {
			$degrees += $minutes > 0.0 ? $incrementor : -$incrementor;

			$offsetByDegrees = $this->getSunriseOffsetByDegrees(Zman::GEOMETRIC_ZENITH + $degrees);

			if ($offsetByDegrees === null || abs($degrees) > 30.0) {
				return NAN;
			}
		} while (
			($minutes > 0.0 && $offsetByDegrees->gt($offsetByTime)) ||
			($minutes < 0.0 && $offsetByDegrees->lt($offsetByTime))
		);

		return $degrees;
	}

	/**
	 * @deprecated See KosherJava for details.
	 */
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

		$offsetByTime = $this->getTimeOffset($seaLevelSunset, $minutes * Zman::MINUTE_MILLIS);

		$degrees = 0.0;
		$incrementor = 0.0001;

		do {
			$degrees += $minutes > 0.0 ? $incrementor : -$incrementor;

			$offsetByDegrees = $this->getSunsetOffsetByDegrees(Zman::GEOMETRIC_ZENITH + $degrees);

			if ($offsetByDegrees === null || abs($degrees) > 30.0) {
				return NAN;
			}
		} while (
			($minutes > 0.0 && $offsetByDegrees->lt($offsetByTime)) ||
			($minutes < 0.0 && $offsetByDegrees->gt($offsetByTime))
		);

		return $degrees;
	}

	protected function getTimeOffset(Carbon $time, float $offset): Carbon
	{
		return $time->copy()->addMicroseconds((int) round($offset * 1000));
	}

	protected function getTemporalHour(Carbon $startOfDay, Carbon $endOfDay): float
	{
		$startOfDayTotal = $startOfDay->getPreciseTimestamp();
		$endOfDayTotal = $endOfDay->getPreciseTimestamp();

		$dayTimeHours = $endOfDayTotal - $startOfDayTotal;
		return $dayTimeHours / 12000;
	}

	public function getZmanisBasedOffset(float $hours): Carbon|null
	{
		$shaahZmanis = $this->getShaahZmanisGRA();
		if (is_null($shaahZmanis) || $hours == 0) {
			return null;
		}

		if ($hours > 0) {
			return $this->getTimeOffset($this->getElevationAdjustedSunset(), $shaahZmanis * $hours);
		}

		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), $shaahZmanis * $hours);
	}

	public function getShaahZmanisBasedZman(?Carbon $startOfDay, ?Carbon $endOfDay, float $hours): Carbon|null
	{
		if (is_null($startOfDay) || is_null($endOfDay)) {
			return null;
		}

		$shaahZmanis = $this->getTemporalHour($startOfDay, $endOfDay);

		return $this->getTimeOffset($startOfDay, $shaahZmanis * $hours);
	}

	public function getPercentOfShaahZmanisFromDegrees(float $degrees, bool $sunset): float|null
	{
		$seaLevelSunrise = $this->getSeaLevelSunrise();
		$seaLevelSunset = $this->getSeaLevelSunset();
		if ($sunset) {
			$twilight = $this->getSunsetOffsetByDegrees(Zman::GEOMETRIC_ZENITH + $degrees);
		} else {
			$twilight = $this->getSunriseOffsetByDegrees(Zman::GEOMETRIC_ZENITH + $degrees);
		}
		if ($seaLevelSunrise == null || $seaLevelSunset == null || $twilight == null) {
			return null;
		}
		$shaahZmanis = ($seaLevelSunset->getPreciseTimestamp() - $seaLevelSunrise->getPreciseTimestamp()) / 12000.0;
		if ($sunset) {
			$riseSetToTwilight = ($twilight->getPreciseTimestamp() - $seaLevelSunset->getPreciseTimestamp()) / 1000;
		} else {
			$riseSetToTwilight = ($seaLevelSunrise->getPreciseTimestamp() - $twilight->getPreciseTimestamp()) / 1000;
		}

		return $riseSetToTwilight / $shaahZmanis;
	}

	public function getHalfDayBasedShaahZmanis(?Carbon $startOfHalfDay, ?Carbon $endOfHalfDay): float|null
	{
		if ($startOfHalfDay == null || $endOfHalfDay == null) {
			return null;
		}

		return ($endOfHalfDay->getPreciseTimestamp() - $startOfHalfDay->getPreciseTimestamp()) / 6000;
	}

	public function getHalfDayBasedZman(?Carbon $startOfHalfDay, ?Carbon $endOfHalfDay, float $hours): Carbon|null
	{
		$shaahZmanis = $this->getHalfDayBasedShaahZmanis($startOfHalfDay, $endOfHalfDay);
		if (is_null($shaahZmanis)) {
			return null;
		}

		if ($hours >= 0) {
			return $this->getTimeOffset($startOfHalfDay, $shaahZmanis * $hours);
		}

		return $this->getTimeOffset($endOfHalfDay, $shaahZmanis * $hours);
	}
}