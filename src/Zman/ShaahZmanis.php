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
trait ShaahZmanis
{
	// The following are from ZmanimCalendar

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

	// The following are from ComprehensiveZmanimCalendar

	public function getShaahZmanis72Minutes(): float|null
	{
		return $this->getShaahZmanisMGA();
	}

	public function getShaahZmanis19Point8Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees());
	}

	public function getShaahZmanis18Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos18Degrees(), $this->getTzais18Degrees());
	}

	public function getShaahZmanis26Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos26Degrees(), $this->getTzais26Degrees());
	}

	public function getShaahZmanis16Point1Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getShaahZmanis60Minutes(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos60Minutes(), $this->getTzais60Minutes());
	}

	public function getShaahZmanis72MinutesZmanis(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos72Zmanis(), $this->getTzais72Zmanis());
	}

	public function getShaahZmanis90Minutes(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos90Minutes(), $this->getTzais90Minutes());
	}

	public function getShaahZmanis90MinutesZmanis(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos90Zmanis(), $this->getTzais90Zmanis());
	}

	public function getShaahZmanis96Minutes(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos96Minutes(), $this->getTzais96Minutes());
	}

	public function getShaahZmanis96MinutesZmanis(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos96Zmanis(), $this->getTzais96Zmanis());
	}

	public function getShaahZmanis120Minutes(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos120Minutes(), $this->getTzais120Minutes());
	}

	public function getShaahZmanis120MinutesZmanis(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos120Zmanis(), $this->getTzais120Zmanis());
	}

	public function getShaahZmanisAteretTorah(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point8Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos16Point1Degrees(), $this->getTzaisGeonim3Point8Degrees());
	}

	public function getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point7Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos16Point1Degrees(), $this->getTzaisGeonim3Point7Degrees());
	}

	public function getShaahZmanisAlos16Point1DegreesToTzaisGeonim7Point083Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos16Point1Degrees(), $this->getTzaisGeonim7Point083Degrees());
	}

	public function getShaahZmanisBaalHatanya(): float|null
	{
		return $this->temporalHourOrNull($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	private function temporalHourOrNull(?Carbon $startOfDay, ?Carbon $endOfDay): float|null
	{
		if ($startOfDay == null || $endOfDay == null) {
			return null;
		}

		return $this->getTemporalHour($startOfDay, $endOfDay);
	}
}