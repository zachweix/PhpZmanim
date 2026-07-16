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

use Carbon\Carbon;
use InvalidArgumentException;
use PhpZmanim\HebrewCalendar\JewishCalendar;

class ZmanimCalendar extends AstronomicalCalendar
{
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private bool $useElevation = false;
	private int $candleLightingOffset = 18;
	private bool $useAstronomicalChatzos = true;
	private bool $useAstronomicalChatzosForOtherZmanim = false;

	const ZENITH_16_POINT_1 = AstronomicalCalendar::GEOMETRIC_ZENITH + 16.1;
	const ZENITH_8_POINT_5 = AstronomicalCalendar::GEOMETRIC_ZENITH + 8.5;
	const ZENITH_1_POINT_583 = AstronomicalCalendar::GEOMETRIC_ZENITH + 1.583;

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function getUseElevation(): bool
	{
		return $this->useElevation;
	}

	public function setUseElevation(bool $useElevation): self
	{
		$this->useElevation = $useElevation;

		return $this;
	}

	public function getCandleLightingOffset(): int
	{
		return $this->candleLightingOffset;
	}

	public function setCandleLightingOffset(int $candleLightingOffset): self
	{
		$this->candleLightingOffset = $candleLightingOffset;

		return $this;
	}

	public function getUseAstronomicalChatzos(): bool
	{
		return $this->useAstronomicalChatzos;
	}

	public function setUseAstronomicalChatzos(bool $useAstronomicalChatzos): self
	{
		$this->useAstronomicalChatzos = $useAstronomicalChatzos;

		return $this;
	}

	public function getUseAstronomicalChatzosForOtherZmanim(): bool
	{
		return $this->useAstronomicalChatzosForOtherZmanim;
	}

	public function setUseAstronomicalChatzosForOtherZmanim(bool $useAstronomicalChatzosForOtherZmanim): self
	{
		$this->useAstronomicalChatzosForOtherZmanim = $useAstronomicalChatzosForOtherZmanim;

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| UTILITIES
	|--------------------------------------------------------------------------
	*/

	protected function getElevationAdjustedSunrise(): Carbon|null
	{
		return $this->useElevation ? $this->getSunrise() : $this->getSeaLevelSunrise();
	}

	protected function getElevationAdjustedSunset(): Carbon|null
	{
		return $this->useElevation ? $this->getSunset() : $this->getSeaLevelSunset();
	}

	protected function getSunriseBaalHatanya(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_1_POINT_583);
	}

	protected function getSunsetBaalHatanya(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_1_POINT_583);
	}

	public function getTzais(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_8_POINT_5);
	}

	public function getAlosHashachar(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_16_POINT_1);
	}

	public function getAlos72(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -72 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getTzais72(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 72 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	/*
	 * Java's getChatzosHayom() (no arguments) and getChatzos(begin, end) are merged into this single method.
	 * Called with no arguments it returns chatzos hayom; called with a start and end of day it returns the
	 * midpoint between them (Java's getSunTransit(begin, end), reimplemented here since our AstronomicalCalendar
	 * only exposes the no-argument getSunTransit()).
	 */
	public function getChatzos(?Carbon $startOfDay = null, ?Carbon $endOfDay = null): Carbon|null
	{
		if (is_null($startOfDay) && is_null($endOfDay)) {
			if ($this->useAstronomicalChatzos) {
				return $this->getSunTransit();
			}

			return $this->getChatzosAsHalfDay() ?? $this->getSunTransit();
		}

		if (is_null($startOfDay) || is_null($endOfDay)) {
			throw new InvalidArgumentException('You must either provide a startOfDay and endOfDay or leave them all blank');
		}

		$shaahZmanis = $this->getTemporalHour($startOfDay, $endOfDay);

		return $this->getTimeOffset($startOfDay, $shaahZmanis * 6);
	}

	public function getChatzosAsHalfDay(): Carbon|null
	{
		return $this->getChatzos($this->getSeaLevelSunrise(), $this->getSeaLevelSunset());
	}

	public function getChatzosHalayla(): Carbon|null
	{
		if ($this->useAstronomicalChatzos) {
			return $this->getSolarMidnight();
		}

		$tomorrow = $this->copy()->addDays(1);

		return $this->getChatzos($this->getSeaLevelSunset(), $tomorrow->getSeaLevelSunrise())
			?? $this->getSolarMidnight();
	}

	public function getSofZmanShma(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($startOfDay, $this->getChatzos(), 3);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 3);
	}

	public function getSofZmanShmaGRA(): Carbon|null
	{
		return $this->getSofZmanShma($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getSofZmanShmaMGA(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos72(), $this->getTzais72(), true);
	}

	public function getSofZmanTfila(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($startOfDay, $this->getChatzos(), 4);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 4);
	}

	public function getSofZmanTfilaGRA(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getSofZmanTfilaMGA(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos72(), $this->getTzais72(), true);
	}

	/*
	 * TODO: Java gates this on it being erev Pesach (Nissan 14) via JewishCalendar and returns null otherwise.
	 * That date check is intentionally omitted for now and should be added back.
	 */
	public function getSofZmanBiurChametz(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($startOfDay, $this->getChatzos(), 5);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 5);
	}

	/*
	 * TODO: Java gates this on it being erev Pesach (Nissan 14) via JewishCalendar and returns null otherwise.
	 * That date check is intentionally omitted for now and should be added back.
	 */
	public function getSofZmanAchilasChametz(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		return $this->getSofZmanTfila($startOfDay, $endOfDay, $synchronous);
	}

	public function getCandleLighting(): Carbon|null
	{
		return $this->getTimeOffset($this->getSeaLevelSunset(), -$this->candleLightingOffset * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getMinchaGedola(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($this->getChatzos(), $endOfDay, 0.5);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 6.5);
	}

	public function getMinchaGedolaGRA(): Carbon|null
	{
		return $this->getMinchaGedola($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getSamuchLeMinchaKetana(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($this->getChatzos(), $endOfDay, 3);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 9);
	}

	public function getMinchaKetana(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($this->getChatzos(), $endOfDay, 3.5);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 9.5);
	}

	public function getMinchaKetanaGRA(): Carbon|null
	{
		return $this->getMinchaKetana($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getPlagHamincha(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($this->getChatzos(), $endOfDay, 4.75);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 10.75);
	}

	public function getPlagHaminchaGRA(): Carbon|null
	{
		return $this->getPlagHamincha($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getShaahZmanisGra(): float|null
	{
		$startOfDay = $this->getElevationAdjustedSunrise();
		$endOfDay = $this->getElevationAdjustedSunset();
		if (is_null($startOfDay) || is_null($endOfDay)) {
			return null;
		}

		return $this->getTemporalHour($startOfDay, $endOfDay);
	}

	public function getShaahZmanisMGA(): float|null
	{
		$startOfDay = $this->getAlos72();
		$endOfDay = $this->getTzais72();
		if (is_null($startOfDay) || is_null($endOfDay)) {
			return null;
		}

		return $this->getTemporalHour($startOfDay, $endOfDay);
	}

	public function getZmanisBasedOffset(float $hours): Carbon|null
	{
		$shaahZmanis = $this->getShaahZmanisGra();
		if (is_null($shaahZmanis) || $hours == 0) {
			return null;
		}

		if ($hours > 0) {
			return $this->getTimeOffset($this->getElevationAdjustedSunset(), $shaahZmanis * $hours);
		}

		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), $shaahZmanis * $hours);
	}

	// Note that jewishCalendar may change and this will need to change too
	public function isAssurBemlacha(Carbon $currentTime, Carbon $tzais, bool $inIsrael): bool
	{
		$jewishCalendar = new JewishCalendar();
		$jewishCalendar->setGregorianDate($this->date->year, $this->date->month, $this->date->day);
		$jewishCalendar->setInIsrael($inIsrael);

		if ($jewishCalendar->hasCandleLighting() && $currentTime->gte($this->getElevationAdjustedSunset())) {
			return true;
		}

		return $jewishCalendar->isAssurBemelacha() && $currentTime->lte($tzais);
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
			$twilight = $this->getSunsetOffsetByDegrees(AstronomicalCalendar::GEOMETRIC_ZENITH + $degrees);
		} else {
			$twilight = $this->getSunriseOffsetByDegrees(AstronomicalCalendar::GEOMETRIC_ZENITH + $degrees);
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
