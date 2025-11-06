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
use PhpZmanim\HebrewCalendar\JewishCalendar;

/**
 * See https://github.com/KosherJava/zmanim/blob/master/src/net/sourceforge/zmanim/ZmanimCalendar.java
 * for more detailed explanations regarding the methods and variables on this page.
 */
class ZmanimCalendar extends AstronomicalCalendar {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private $useElevation = true;
	private $candleLightingOffset = 18;
	private $useAstronomicalChatzos = true;
	private $useAstronomicalChatzosForOtherZmanim = false;

	const ZENITH_16_POINT_1 = AstronomicalCalendar::GEOMETRIC_ZENITH + 16.1;
	const ZENITH_8_POINT_5 = AstronomicalCalendar::GEOMETRIC_ZENITH + 8.5;

	/*
	|--------------------------------------------------------------------------
	| UTILITIES
	|--------------------------------------------------------------------------
	*/

	public function isUseElevation() {
		return $this->useElevation;
	}

	public function setUseElevation($useElevation) {
		$this->useElevation = $useElevation;

		return $this;
	}

	public function isUseAstronomicalChatzos() {
		return $this->useAstronomicalChatzos;
	}

	public function setUseAstronomicalChatzos($useAstronomicalChatzos) {
		$this->useAstronomicalChatzos = $useAstronomicalChatzos;

		return $this;
	}

	public function isUseAstronomicalChatzosForOtherZmanim() {
		return $this->useAstronomicalChatzosForOtherZmanim;
	}

	public function setUseAstronomicalChatzosForOtherZmanim($useAstronomicalChatzosForOtherZmanim) {
		$this->useAstronomicalChatzosForOtherZmanim = $useAstronomicalChatzosForOtherZmanim;

		return $this;
	}

	protected function getElevationAdjustedSunrise() {
		if ($this->isUseElevation()) {
			return $this->getSunrise();
		} else {
			return $this->getSeaLevelSunrise();
		}
	}

	protected function getElevationAdjustedSunset() {
		if ($this->isUseElevation()) {
			return $this->getSunset();
		} else {
			return $this->getSeaLevelSunset();
		}
	}

	public function getTzais() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_8_POINT_5);
	}

	public function getAlosHashachar() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_16_POINT_1);
	}

	public function getAlos72() {
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -72 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getChatzos() {
		if ($this->useAstronomicalChatzos) {
			return $this->getSunTransit();
		} else {
			return $this->getChatzosAsHalfDay() ?? $this->getSunTransit();
		}
	}

	public function getChatzosAsHalfDay() {
		return $this->getSunTransit($this->getSeaLevelSunrise(), $this->getSeaLevelSunset());
	}

	public function getSofZmanShma($startOfDay, $endOfDay) {
		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 3);
	}

	public function getSofZmanShmaGRA() {
		return $this->getSofZmanShma($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset());
	}

	public function getSofZmanShmaMGA() {
		return $this->getSofZmanShma($this->getAlos72(), $this->getTzais72());
	}

	public function getTzais72() {
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 72 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getCandleLighting() {
		return $this->getTimeOffset($this->getSeaLevelSunset(), -$this->getCandleLightingOffset() * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getSofZmanTfila($startOfDay, $endOfDay) {
		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 4);
	}

	public function getSofZmanTfilaGRA() {
		return $this->getSofZmanTfila($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset());
	}

	public function getSofZmanTfilaMGA() {
		return $this->getSofZmanTfila($this->getAlos72(), $this->getTzais72());
	}

	public function getMinchaGedola($startOfDay = null, $endOfDay = null) {
		if($this->isUseAstronomicalChatzosForOtherZmanim()) {
			$chatzos = $this->getSunTransit();
			$sunset = $endOfDay ?? $this->getSunset();

			return $this->getHalfDayBasedZman($chatzos, $sunset, 0.5);
		}

		if (is_null($startOfDay) && is_null($endOfDay)) {
			$startOfDay = $this->getElevationAdjustedSunrise();
			$endOfDay = $this->getElevationAdjustedSunset();
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 6.5);
	}

	public function getSamuchLeMinchaKetana($startOfDay, $endOfDay) {
		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 9);
	}

	public function getMinchaKetana($startOfDay = null, $endOfDay = null) {
		if (is_null($startOfDay) && is_null($endOfDay)) {
			$startOfDay = $this->getElevationAdjustedSunrise();
			$endOfDay = $this->getElevationAdjustedSunset();
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 9.5);
	}

	public function getPlagHamincha($startOfDay = null, $endOfDay = null) {
		if (is_null($startOfDay) && is_null($endOfDay)) {
			$startOfDay = $this->getElevationAdjustedSunrise();
			$endOfDay = $this->getElevationAdjustedSunset();
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 10.75);
	}

	public function getShaahZmanisGra() {
		return $this->getTemporalHour($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset());
	}

	public function getShaahZmanisMGA() {
		return $this->getTemporalHour($this->getAlos72(), $this->getTzais72());
	}

	public function getCandleLightingOffset() {
		return $this->candleLightingOffset;
	}

	public function setCandleLightingOffset($candleLightingOffset) {
		$this->candleLightingOffset = $candleLightingOffset;

		return $this;
	}

	public function isAssurBemlacha(Carbon $currentTime, Carbon $tzais, $inIsrael) {
		$jewishCalendar = new JewishCalendar();
		$jewishCalendar->setGregorianDate($this->getCalendar()->year, $this->getCalendar()->month, $this->getCalendar()->day);
		$jewishCalendar->setInIsrael($inIsrael);

		if ($jewishCalendar->hasCandleLighting() && $currentTime->gt($this->getElevationAdjustedSunset())) { // erev shabbos, YT or YT sheni after shkiah
			return true;
		}

		if ($jewishCalendar->isAssurBemelacha() && $currentTime->lt($tzais)) { // is shabbos or YT and it is before tzais
			return true;
		}

		return false;
	}

	public function getShaahZmanisBasedZman($startOfDay, $endOfDay, $hours) {
		$shaahZmanis = $this->getTemporalHour($startOfDay, $endOfDay);
		return $this->getTimeOffset($startOfDay, $shaahZmanis * $hours);
	}

	public function getPercentOfShaahZmanisFromDegrees($degrees, $sunset) {
		$seaLevelSunrise = $this->getSeaLevelSunrise();
		$seaLevelSunset = $this->getSeaLevelSunset();
		$twilight = null;
		if($sunset) {
			$twilight = $this->getSunsetOffsetByDegrees(AstronomicalCalendar::GEOMETRIC_ZENITH + $degrees);
		} else {
			$twilight = $this->getSunriseOffsetByDegrees(AstronomicalCalendar::GEOMETRIC_ZENITH + $degrees);
		}
		if($seaLevelSunrise == null || $seaLevelSunset == null || $twilight == null) {
			return null;
		}
		$shaahZmanis = ($seaLevelSunset->getPreciseTimestamp() - $seaLevelSunrise->getPreciseTimestamp()) / 12000.0;
		$riseSetToTwilight;
		if($sunset) {
			$riseSetToTwilight = ($twilight->getPreciseTimestamp() - $seaLevelSunset->getPreciseTimestamp()) / 1000;
		} else {
			$riseSetToTwilight = ($seaLevelSunrise->getPreciseTimestamp() - $twilight->getPreciseTimestamp()) / 1000;
		}
		return $riseSetToTwilight / $shaahZmanis;
	}

	public function getHalfDayBasedZman($startOfHalfDay, $endOfHalfDay, $hours) {
		if ($startOfHalfDay == null || $endOfHalfDay == null) {
			return null;
		}
		$shaahZmanis = ($endOfHalfDay->getPreciseTimestamp() - $startOfHalfDay->getPreciseTimestamp()) / 6;

		return $this->getTimeOffset($startOfHalfDay, $shaahZmanis * $hours);
	}
}