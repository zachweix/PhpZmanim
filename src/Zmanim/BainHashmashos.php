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
use PhpZmanim\Zmanim;

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
trait BainHashmashos
{
	// The following are from ComprehensiveZmanimCalendar

	public function getBainHashmashosRT13Point24Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zmanim::ZENITH_13_POINT_24);
	}

	public function getBainHashmashosRT58Point5Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 58.5 * Zmanim::MINUTE_MILLIS);
	}

	public function getBainHashmashosRT13Point5MinutesBefore7Point083Degrees(): Carbon|null
	{
		return $this->getTimeOffset($this->getTzaisGeonim7Point083Degrees(), -13.5 * Zmanim::MINUTE_MILLIS);
	}

	public function getBainHashmashosRT2Stars(): Carbon|null
	{
		$alos19Point8 = $this->getAlos19Point8Degrees();
		$sunrise = $this->getElevationAdjustedSunrise();
		if ($alos19Point8 == null || $sunrise == null) {
			return null;
		}

		$alosToSunrise = ($sunrise->getPreciseTimestamp() - $alos19Point8->getPreciseTimestamp()) / 1000;

		return $this->getTimeOffset($this->getElevationAdjustedSunset(), $alosToSunrise * (5 / 18));
	}

	public function getBainHashmashosYereim18Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), -18 * Zmanim::MINUTE_MILLIS);
	}

	public function getBainHashmashosYereim16Point875Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), -16.875 * Zmanim::MINUTE_MILLIS);
	}

	public function getBainHashmashosYereim13Point5Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), -13.5 * Zmanim::MINUTE_MILLIS);
	}

	public function getBainHashmashosYereim3Point05Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zmanim::ZENITH_MINUS_3_POINT_05);
	}

	public function getBainHashmashosYereim2Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zmanim::ZENITH_MINUS_2_POINT_8);
	}

	public function getBainHashmashosYereim2Point1Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zmanim::ZENITH_MINUS_2_POINT_1);
	}
}