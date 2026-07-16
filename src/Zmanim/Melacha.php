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
use PhpZmanim\JewishDate;
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
trait Melacha
{
	// The following are from ZmanimCalendar

	public function getCandleLighting(): Carbon|null
	{
		return $this->getTimeOffset($this->getSeaLevelSunset(), -$this->candleLightingOffset * Zmanim::MINUTE_MILLIS);
	}

	// Note that jewishCalendar may change and this will need to change too
	public function isAssurBemlacha(Carbon $currentTime, Carbon $tzais, bool $inIsrael): bool
	{
		$jewishDate = new JewishDate();
		$jewishDate->setGregorianDate($this->date->year, $this->date->month, $this->date->day);
		$jewishDate->setInIsrael($inIsrael);

		if ($jewishDate->hasCandleLighting() && $currentTime->gte($this->getElevationAdjustedSunset())) {
			return true;
		}

		return $jewishDate->isAssurBemelacha() && $currentTime->lte($tzais);
	}
}