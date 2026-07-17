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
use PhpZmanim\JewishDate;

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
trait Chametz
{
	// The following are from ZmanimCalendar

	// Chametz zmanim only apply on Erev Pesach (Nissan 14); null is returned on any other day.
	public function getSofZmanBiurChametz(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if (!$this->isErevPesach()) {
			return null;
		}

		if ($this->useAstronomicalChatzosForOtherZmanim && $synchronous) {
			return $this->getHalfDayBasedZman($startOfDay, $this->getChatzos(), 5);
		}

		return $this->getShaahZmanisBasedZman($startOfDay, $endOfDay, 5);
	}

	public function getSofZmanAchilasChametz(?Carbon $startOfDay = null, ?Carbon $endOfDay = null, bool $synchronous = false): Carbon|null
	{
		if (!$this->isErevPesach()) {
			return null;
		}

		return $this->getSofZmanTfila($startOfDay, $endOfDay, $synchronous);
	}

	private function isErevPesach(): bool
	{
		$jewishDate = new JewishDate();
		$jewishDate->setGregorianDate($this->date->year, $this->date->month, $this->date->day);

		return $jewishDate->getJewishMonth() == JewishDate::NISSAN && $jewishDate->getJewishDayOfMonth() == 14;
	}

	// The following are from ZmanimCalendar

	public function getSofZmanAchilasChametzGRA(): Carbon|null
	{
		return $this->getSofZmanAchilasChametz($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getSofZmanAchilasChametzMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanAchilasChametz($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	public function getSofZmanAchilasChametzMGA72MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanAchilasChametz($this->getAlos72Zmanis(), $this->getTzais72Zmanis(), true);
	}

	public function getSofZmanAchilasChametzMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanAchilasChametz($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getSofZmanBiurChametzGRA(): Carbon|null
	{
		return $this->getSofZmanBiurChametz($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getSofZmanBiurChametzMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanBiurChametz($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	public function getSofZmanBiurChametzMGA72MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanBiurChametz($this->getAlos72Zmanis(), $this->getTzais72Zmanis(), true);
	}

	public function getSofZmanBiurChametzMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanBiurChametz($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}
}