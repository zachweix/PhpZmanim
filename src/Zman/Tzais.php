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
trait Tzais
{
	// The following are from ZmanimCalendar

	public function getTzais(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_8_POINT_5);
	}

	public function getTzais72(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 72 * Zman::MINUTE_MILLIS);
	}

	// The following are from ComprehensiveZmanimCalendar


	public function getTzais72Minutes(): Carbon|null
	{
		return $this->getTzais72();
	}

	public function getTzaisGeonim8Point5Degrees(): Carbon|null
	{
		return $this->getTzais();
	}

	public function getTzais50Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 50 * Zman::MINUTE_MILLIS);
	}

	public function getTzais60Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 60 * Zman::MINUTE_MILLIS);
	}

	public function getTzais90Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 90 * Zman::MINUTE_MILLIS);
	}

	public function getTzais96Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 96 * Zman::MINUTE_MILLIS);
	}

	public function getTzais120Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 120 * Zman::MINUTE_MILLIS);
	}

	public function getTzais72Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(1.2);
	}

	public function getTzais90Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(1.5);
	}

	public function getTzais96Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(1.6);
	}

	public function getTzais120Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(2.0);
	}

	public function getTzais16Point1Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_16_POINT_1);
	}

	public function getTzais18Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ASTRONOMICAL_ZENITH);
	}

	public function getTzais19Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_19_POINT_8);
	}

	public function getTzais26Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_26_DEGREES);
	}

	public function getTzaisGeonim3Point7Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_3_POINT_7);
	}

	public function getTzaisGeonim3Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_3_POINT_8);
	}

	public function getTzaisGeonim4Point42Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_4_POINT_42);
	}

	public function getTzaisGeonim4Point66Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_4_POINT_66);
	}

	public function getTzaisGeonim4Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_4_POINT_8);
	}

	public function getTzaisGeonim5Point95Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_5_POINT_95);
	}

	public function getTzaisGeonim6Point45Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_6_POINT_45);
	}

	public function getTzaisGeonim7Point083Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_7_POINT_083);
	}

	public function getTzaisGeonim7Point67Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_7_POINT_67);
	}

	public function getTzaisGeonim9Point3Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_9_POINT_3);
	}

	public function getTzaisGeonim9Point75Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_9_POINT_75);
	}
}