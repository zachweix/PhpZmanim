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
use InvalidArgumentException;
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
trait Noon
{
	public function getSunTransit(): Carbon|null
	{
		$noon = $this->astronomicalCalculator->getUTCNoon($this->getAdjustedDate(), $this->geoLocation);

		return $this->toAdjustedCarbon($noon, Zmanim::NOON);
	}

	// The following are from ZmanimCalendar

	public function getChatzos(?Carbon $startOfDay = null, ?Carbon $endOfDay = null): Carbon|null
	{
		if (is_null($startOfDay) && is_null($endOfDay)) {
			if ($this->useAstronomicalChatzos) {
				return $this->getSunTransit();
			}

			return $this->getChatzosHayomAsHalfDay() ?? $this->getSunTransit();
		}

		if (is_null($startOfDay) || is_null($endOfDay)) {
			throw new InvalidArgumentException('You must either provide a startOfDay and endOfDay or leave them all blank');
		}

		$shaahZmanis = $this->getTemporalHour($startOfDay, $endOfDay);

		return $this->getTimeOffset($startOfDay, $shaahZmanis * 6);
	}

	public function getChatzosHayomAsHalfDay(): Carbon|null
	{
		return $this->getChatzos($this->getSeaLevelSunrise(), $this->getSeaLevelSunset());
	}

	public function getChatzosHayom(): Carbon|null
	{
		return $this->getChatzos();
	}

	// The following are from ComprehensiveZmanimCalendar

	public function getFixedLocalChatzosHayom(): Carbon|null
	{
		return $this->getLocalMeanTime(12);
	}
}