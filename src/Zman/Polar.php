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
trait Polar
{
	// The following are from ComprehensiveZmanimCalendar

	public function getPolarSunriseBenIshChai(): Carbon|null
	{
		if ($this->getElevationAdjustedSunrise() == null) {
			return $this->getTimeAtAzimuth90Or270(90);
		}

		return null;
	}

	public function getPolarSunsetBenIshChai(): Carbon|null
	{
		if ($this->getElevationAdjustedSunset() == null) {
			return $this->getTimeAtAzimuth90Or270(270);
		}

		return null;
	}

	public function getPolarPlagHaminchaBenIshChai(): Carbon|null
	{
		return $this->getPlagHamincha($this->getPolarSunriseBenIshChai(), $this->getPolarSunsetBenIshChai(), true);
	}

	public function getPolarStartOfDayTeshuvosVehanhagos(): Carbon|null
	{
		if ($this->getElevationAdjustedSunrise() != null || $this->getElevationAdjustedSunset() != null) {
			return null;
		}

		$chatzosHayom = $this->getChatzosHayom();
		$chatzosHalayla = $this->getChatzosHalayla();
		$calculator = $this->getAstronomicalCalculator();
		$chatzosHayomSolarElevation = $calculator->getSolarElevation($chatzosHayom, $this->getGeoLocation());
		$chatzosHalaylaSolarElevation = $calculator->getSolarElevation($chatzosHalayla, $this->getGeoLocation());
		$sunriseElevation = $calculator->getSolarRadius() + $calculator->getRefraction();

		if ($chatzosHayomSolarElevation < (0 - $sunriseElevation) && $chatzosHalaylaSolarElevation < (0 - $sunriseElevation)
				&& $this->getAlos16Point1Degrees() == null && $this->getElevationAdjustedSunrise() == null) {
			return $chatzosHayom;
		}

		if ($chatzosHayomSolarElevation > (0 - $sunriseElevation) && $chatzosHalaylaSolarElevation > (0 - $sunriseElevation)) {
			return $chatzosHalayla;
		}

		return null;
	}

	public function getPolarPlagHaminchaTeshuvosVehanhagos(): Carbon|null
	{
		$polarStartOfDay = $this->getPolarStartOfDayTeshuvosVehanhagos();
		if ($polarStartOfDay == null) {
			return null;
		}

		$yesterday = $polarStartOfDay->copy()->subDay();

		return $this->getPlagHamincha($yesterday, $polarStartOfDay, true);
	}
}