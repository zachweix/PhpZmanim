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

namespace PhpZmanim\Zmanim;

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
trait MinchaKetana
{
	// The following are from ZmanimCalendar

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

	// The following are from ComprehensiveZmanimCalendar

	public function getMinchaKetana16Point1Degrees(): Carbon|null
	{
		return $this->getMinchaKetana($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getMinchaKetana72Minutes(): Carbon|null
	{
		return $this->getMinchaKetana($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	public function getMinchaKetanaAhavatShalom(): Carbon|null
	{
		$shaahZmanis = $this->getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point8Degrees();
		if ($shaahZmanis == null) {
			return null;
		}

		return $this->getTimeOffset($this->getTzaisGeonim3Point8Degrees(), -$shaahZmanis * 2.5);
	}

	public function getMinchaKetanaGRAFixedLocalChatzosToSunset(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getFixedLocalChatzosHayom(), $this->getElevationAdjustedSunset(), 3.5);
	}

	public function getSamuchLeMinchaKetanaGRA(): Carbon|null
	{
		return $this->getSamuchLeMinchaKetana($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getSamuchLeMinchaKetana16Point1Degrees(): Carbon|null
	{
		return $this->getSamuchLeMinchaKetana($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getSamuchLeMinchaKetana72Minutes(): Carbon|null
	{
		return $this->getSamuchLeMinchaKetana($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}
}