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
trait MinchaGedola
{
	// The following are from ZmanimCalendar

	public function getMinchaGedola(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($this->getChatzos(), $endOfDay, 0.5);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 6.5);
	}

	public function getMinchaGedolaGRA(): Carbon|null
	{
		return $this->getMinchaGedola($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	// The following are from ComprehensiveZmanimCalendar

	public function getMinchaGedola30Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getChatzosHayom(), 30 * Zmanim::MINUTE_MILLIS);
	}

	public function getMinchaGedola72Minutes(): Carbon|null
	{
		if ($this->getUseAstronomicalChatzosForOtherZmanim()) {
			return $this->getHalfDayBasedZman($this->getChatzosHayom(), $this->getTzais72Minutes(), 0.5);
		}

		return $this->getMinchaGedola($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	public function getMinchaGedola16Point1Degrees(): Carbon|null
	{
		if ($this->getUseAstronomicalChatzosForOtherZmanim()) {
			return $this->getHalfDayBasedZman($this->getChatzosHayom(), $this->getTzais16Point1Degrees(), 0.5);
		}

		return $this->getMinchaGedola($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getMinchaGedolaAhavatShalom(): Carbon|null
	{
		$chatzos = $this->getChatzosHayom();
		$minchaGedola30 = $this->getMinchaGedola30Minutes();
		$shaahZmanis = $this->getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point7Degrees();
		if ($chatzos == null || $minchaGedola30 == null || $shaahZmanis == null) {
			return null;
		}

		$minchaGedolaAhavatShalom = $this->getTimeOffset($chatzos, $shaahZmanis / 2);

		return $minchaGedola30->gt($minchaGedolaAhavatShalom) ? $minchaGedola30 : $minchaGedolaAhavatShalom;
	}

	public function getMinchaGedolaGreaterThan30(?Carbon $minchaGedola): Carbon|null
	{
		$minchaGedola30 = $this->getMinchaGedola30Minutes();
		if ($minchaGedola30 == null || $minchaGedola == null) {
			return null;
		}

		return $minchaGedola30->gt($minchaGedola) ? $minchaGedola30 : $minchaGedola;
	}

	public function getMinchaGedolaGRAGreaterThan30(): Carbon|null
	{
		return $this->getMinchaGedolaGreaterThan30($this->getMinchaGedolaGRA());
	}

	public function getMinchaGedolaGRAFixedLocalChatzos30Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getFixedLocalChatzosHayom(), 30 * Zmanim::MINUTE_MILLIS);
	}
}