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
trait SofZmanTfila
{
	// The following are from ZmanimCalendar

	public function getSofZmanTfila(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($startOfDay, $this->getChatzosHayom(), 4);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 4);
	}

	public function getSofZmanTfilaGRA(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getSofZmanTfilaMGA(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	// The following are from ComprehensiveZmanimCalendar

	public function getSofZmanTfilaMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanTfilaMGA();
	}

	public function getSofZmanTfilaMGA72MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos72Zmanis(), $this->getTzais72Zmanis(), true);
	}

	public function getSofZmanTfilaMGA90Minutes(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos90Minutes(), $this->getTzais90Minutes(), true);
	}

	public function getSofZmanTfilaMGA90MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos90Zmanis(), $this->getTzais90Zmanis(), true);
	}

	public function getSofZmanTfilaMGA96Minutes(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos96Minutes(), $this->getTzais96Minutes(), true);
	}

	public function getSofZmanTfilaMGA96MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos96Zmanis(), $this->getTzais96Zmanis(), true);
	}

	public function getSofZmanTfilaMGA120Minutes(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos120Minutes(), $this->getTzais120Minutes(), true);
	}

	public function getSofZmanTfilaMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getSofZmanTfilaMGA18Degrees(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos18Degrees(), $this->getTzais18Degrees(), true);
	}

	public function getSofZmanTfilaMGA19Point8Degrees(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees(), true);
	}

	public function getSofZmanTfila2HoursBeforeChatzos(): Carbon|null
	{
		return $this->getTimeOffset($this->getChatzosHayom(), -2 * Zman::HOUR_MILLIS);
	}

	public function getSofZmanTfilaGRASunriseToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getElevationAdjustedSunrise(), $this->getFixedLocalChatzosHayom(), 4);
	}
}