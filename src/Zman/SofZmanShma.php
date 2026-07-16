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
trait SofZmanShma
{
	// The following are from ZmanimCalendar

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

	// The following are from ComprehensiveZmanimCalendar

	public function getSofZmanShmaMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanShmaMGA();
	}

	public function getSofZmanShmaMGA72MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos72Zmanis(), $this->getTzais72Zmanis(), true);
	}

	public function getSofZmanShmaMGA90Minutes(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos90Minutes(), $this->getTzais90Minutes(), true);
	}

	public function getSofZmanShmaMGA90MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos90Zmanis(), $this->getTzais90Zmanis(), true);
	}

	public function getSofZmanShmaMGA96Minutes(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos96Minutes(), $this->getTzais96Minutes(), true);
	}

	public function getSofZmanShmaMGA96MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos96Zmanis(), $this->getTzais96Zmanis(), true);
	}

	public function getSofZmanShmaMGA120Minutes(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos120Minutes(), $this->getTzais120Minutes(), true);
	}

	public function getSofZmanShmaMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getSofZmanShmaMGA18Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos18Degrees(), $this->getTzais18Degrees(), true);
	}

	public function getSofZmanShmaMGA19Point8Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees(), true);
	}

	public function getSofZmanShma3HoursBeforeChatzos(): Carbon|null
	{
		return $this->getTimeOffset($this->getChatzosHayom(), -3 * Zman::HOUR_MILLIS);
	}

	public function getSofZmanShmaAlos16Point1ToSunset(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getElevationAdjustedSunset(), false);
	}

	public function getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getTzaisGeonim7Point083Degrees(), false);
	}

	public function getSofZmanShmaMGA18DegreesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos18Degrees(), $this->getFixedLocalChatzosHayom(), 3);
	}

	public function getSofZmanShmaMGA16Point1DegreesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos16Point1Degrees(), $this->getFixedLocalChatzosHayom(), 3);
	}

	public function getSofZmanShmaMGA90MinutesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos90Minutes(), $this->getFixedLocalChatzosHayom(), 3);
	}

	public function getSofZmanShmaMGA72MinutesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos72Minutes(), $this->getFixedLocalChatzosHayom(), 3);
	}

	public function getSofZmanShmaGRASunriseToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getElevationAdjustedSunrise(), $this->getFixedLocalChatzosHayom(), 3);
	}
}