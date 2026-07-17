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
trait Plag
{
	// The following are from ZmanimCalendar

	public function getPlagHamincha(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($this->getChatzosHayom(), $endOfDay, 4.75);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 10.75);
	}

	public function getPlagHaminchaGRA(): Carbon|null
	{
		return $this->getPlagHamincha($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	// The following are from ComprehensiveZmanimCalendar

	public function getPlagHamincha60Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos60Minutes(), $this->getTzais60Minutes(), true);
	}

	public function getPlagHamincha72Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	public function getPlagHamincha72MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos72Zmanis(), $this->getTzais72Zmanis(), true);
	}

	public function getPlagHamincha90Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos90Minutes(), $this->getTzais90Minutes(), true);
	}

	public function getPlagHamincha90MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos90Zmanis(), $this->getTzais90Zmanis(), true);
	}

	public function getPlagHamincha96Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos96Minutes(), $this->getTzais96Minutes(), true);
	}

	public function getPlagHamincha96MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos96Zmanis(), $this->getTzais96Zmanis(), true);
	}

	public function getPlagHamincha120Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos120Minutes(), $this->getTzais120Minutes(), true);
	}

	public function getPlagHamincha120MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos120Zmanis(), $this->getTzais120Zmanis(), true);
	}

	public function getPlagHamincha16Point1Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getPlagHamincha18Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos18Degrees(), $this->getTzais18Degrees(), true);
	}

	public function getPlagHamincha19Point8Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees(), true);
	}

	public function getPlagHamincha26Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos26Degrees(), $this->getTzais26Degrees(), true);
	}

	public function getPlagAlosToSunset(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getElevationAdjustedSunset(), false);
	}

	public function getPlagAlos16Point1DegreesToTzaisGeonim7Point083Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getTzaisGeonim7Point083Degrees(), false);
	}

	public function getPlagAhavatShalom(): Carbon|null
	{
		$shaahZmanis = $this->getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point8Degrees();
		if ($shaahZmanis == null) {
			return null;
		}

		return $this->getTimeOffset($this->getTzaisGeonim3Point8Degrees(), -$shaahZmanis * 1.25);
	}

	public function getPlagHaminchaGRAFixedLocalChatzosToSunset(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getFixedLocalChatzosHayom(), $this->getElevationAdjustedSunset(), 4.75);
	}
}