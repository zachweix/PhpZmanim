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

	const ZENITH_16_POINT_1 = AstronomicalCalculator::GEOMETRIC_ZENITH + 16.1;
	const ZENITH_8_POINT_5 = AstronomicalCalculator::GEOMETRIC_ZENITH + 8.5;

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function getUseElevation() {
		return $this->useElevation;
	}

	public function setUseElevation($useElevation) {
		$this->useElevation = $useElevation;
	}

	public function getCandleLightingOffset() {
		return $this->candleLightingOffset;
	}

	public function setCandleLightingOffset($candleLightingOffset) {
		$this->candleLightingOffset = $candleLightingOffset;
	}

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getAlosHashachar() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_16_POINT_1);
	}

	public function getAlos72() {
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -72 * GeoLocation::MINUTE_SECOND);
	}

	public function getSofZmanShma($startOfDay, $endOfDay) {
		$shaahZmanis = $this->getTemporalHour($startOfDay, $endOfDay);
		return $this->getTimeOffset($startOfDay, $shaahZmanis * 3);
	}

	public function getSofZmanShmaMA() {
		return $this->getSofZmanShma($this->getAlos72(), $this->getTzais72());
	}

	public function getSofZmanShmaGra() {
		return $this->getSofZmanShma($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset());
	}

	public function getSofZmanTfila($startOfDay, $endOfDay) {
		$shaahZmanis = $this->getTemporalHour($startOfDay, $endOfDay);
		return $this->getTimeOffset($startOfDay, $shaahZmanis * 4);
	}

	public function getSofZmanTfilaMA() {
		return $this->getSofZmanTfila($this->getAlos72(), $this->getTzais72());
	}

	public function getSofZmanTfilaGra() {
		return $this->getSofZmanTfila($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset());
	}

	public function getChatzos() {
		return $this->getSunTransit();
	}

	public function getMinchaGedola($startOfDay, $endOfDay) {
		$shaahZmanis = $this->getTemporalHour($startOfDay, $endOfDay);
		return $this->getTimeOffset($startOfDay, $shaahZmanis * 6.5);
	}

	public function getMinchaGedolaGra() {
		return $this->getMinchaGedola($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset());
	}

	public function getMinchaKetana($startOfDay, $endOfDay) {
		$shaahZmanis = $this->getTemporalHour($startOfDay, $endOfDay);
		return $this->getTimeOffset($startOfDay, $shaahZmanis * 9.5);
	}

	public function getMinchaKetanaGra() {
		return $this->getMinchaKetana($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset());
	}

	public function getPlagHamincha($startOfDay, $endOfDay) {
		$shaahZmanis = $this->getTemporalHour($startOfDay, $endOfDay);
		return $this->getTimeOffset($startOfDay, $shaahZmanis * 10.75);
	}

	public function getPlagHaminchaGra() {
		return $this->getPlagHamincha($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset());
	}

	public function getCandleLighting() {
		return $this->getTimeOffset($this->getSeaLevelSunset(), -$this->getCandleLightingOffset() * GeoLocation::MINUTE_SECOND);
	}

	public function getTzais() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_8_POINT_5);
	}

	public function getTzais72() {
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 72 * GeoLocation::MINUTE_SECOND);
	}

	/*
	|--------------------------------------------------------------------------
	| HELPER METHODS
	|--------------------------------------------------------------------------
	*/

	protected function getElevationAdjustedSunrise() {
		if ($this->getUseElevation()) {
			return $this->getSunrise();
		} else {
			return $this->getSeaLevelSunrise();
		}
	}

	protected function getElevationAdjustedSunset() {
		if ($this->getUseElevation()) {
			return $this->getSunset();
		} else {
			return $this->getSeaLevelSunset();
		}
	}

	public function getShaahZmanisGra() {
		return $this->getTemporalHour($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset());
	}

	public function getShaahZmanisMA() {
		return $this->getTemporalHour($this->getAlos72(), $this->getTzais72());
	}
}