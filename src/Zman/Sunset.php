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
trait Sunset
{
	public function getSunset(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::GEOMETRIC_ZENITH);
	}

	public function getEndCivilTwilight(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::CIVIL_ZENITH);
	}

	public function getEndNauticalTwilight(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::NAUTICAL_ZENITH);
	}

	public function getEndAstronomicalTwilight(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ASTRONOMICAL_ZENITH);
	}

	public function getSeaLevelSunset(): Carbon|null
	{
		return $this->toAdjustedCarbon($this->getUTCSunset(Zman::GEOMETRIC_ZENITH, false), Zman::SUNSET);
	}

	public function getSunsetOffsetByDegrees(float $offsetZenith): Carbon|null
	{
		return $this->toAdjustedCarbon($this->getUTCSunset($offsetZenith), Zman::SUNSET);
	}

	// The following are from ZmanimCalendar

	protected function getElevationAdjustedSunset(): Carbon|null
	{
		return $this->useElevation ? $this->getSunset() : $this->getSeaLevelSunset();
	}

	protected function getSunsetBaalHatanya(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_1_POINT_583);
	}
}