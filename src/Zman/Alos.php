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
trait Alos
{
	// The following are from ZmanimCalendar

	public function getAlosHashachar(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_16_POINT_1);
	}

	public function getAlos72(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -72 * Zman::MINUTE_MILLIS);
	}

	// The following are from ComprehensiveZmanimCalendar

	public function getAlos72Minutes(): Carbon|null
	{
		return $this->getAlos72();
	}

	public function getAlos16Point1Degrees(): Carbon|null
	{
		return $this->getAlosHashachar();
	}

	public function getAlos60Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -60 * Zman::MINUTE_MILLIS);
	}

	public function getAlos90Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -90 * Zman::MINUTE_MILLIS);
	}

	public function getAlos96Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -96 * Zman::MINUTE_MILLIS);
	}

	public function getAlos120Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -120 * Zman::MINUTE_MILLIS);
	}

	public function getAlos72Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-1.2);
	}

	public function getAlos90Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-1.5);
	}

	public function getAlos96Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-1.6);
	}

	public function getAlos120Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-2.0);
	}

	public function getAlos18Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ASTRONOMICAL_ZENITH);
	}

	public function getAlos19Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_19_DEGREES);
	}

	public function getAlos19Point8Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_19_POINT_8);
	}

	public function getAlos26Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_26_DEGREES);
	}
}