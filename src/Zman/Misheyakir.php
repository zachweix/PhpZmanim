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
trait Misheyakir
{
	// The following are from ComprehensiveZmanimCalendar

	public function getMisheyakir12Point85Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_12_POINT_85);
	}

	public function getMisheyakir11Point5Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_11_POINT_5);
	}

	public function getMisheyakir11Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_11_DEGREES);
	}

	public function getMisheyakir10Point2Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_10_POINT_2);
	}

	public function getMisheyakir9Point5Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_9_POINT_5);
	}

	public function getMisheyakir7Point65Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_7_POINT_65);
	}
}