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
use PhpZmanim\HebrewCalendar\JewishCalendar;

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

trait Molad
{
	public function getSofZmanKidushLevanaBetweenMoldos(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = $this->jewishCalendar();
		if ($jewishCalendar->getJewishDayOfMonth() < 11 || $jewishCalendar->getJewishDayOfMonth() > 16) {
			return null;
		}

		return $this->getMoladBasedTime($jewishCalendar->getSofZmanKidushLevanaBetweenMoldos(), $alos, $tzais, false);
	}

	public function getSofZmanKidushLevana15Days(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = $this->jewishCalendar();
		if ($jewishCalendar->getJewishDayOfMonth() < 11 || $jewishCalendar->getJewishDayOfMonth() > 17) {
			return null;
		}

		return $this->getMoladBasedTime($jewishCalendar->getSofZmanKidushLevana15Days(), $alos, $tzais, false);
	}

	public function getTchilasZmanKidushLevana3Days(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = $this->jewishCalendar();
		if ($jewishCalendar->getJewishDayOfMonth() > 5 && $jewishCalendar->getJewishDayOfMonth() < 30) {
			return null;
		}

		$zman = $this->getMoladBasedTime($jewishCalendar->getTchilasZmanKidushLevana3Days(), $alos, $tzais, true);
		if ($zman == null && $jewishCalendar->getJewishDayOfMonth() == 30) {
			$jewishCalendar->addMonthsJewish(1);
			$zman = $this->getMoladBasedTime($jewishCalendar->getTchilasZmanKidushLevana3Days(), null, null, true);
		}

		return $zman;
	}

	public function getTchilasZmanKidushLevana7Days(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = $this->jewishCalendar();
		if ($jewishCalendar->getJewishDayOfMonth() < 4 || $jewishCalendar->getJewishDayOfMonth() > 9) {
			return null;
		}

		return $this->getMoladBasedTime($jewishCalendar->getTchilasZmanKidushLevana7Days(), $alos, $tzais, true);
	}

	public function getZmanMolad(): Carbon|null
	{
		$jewishCalendar = $this->jewishCalendar();
		if ($jewishCalendar->getJewishDayOfMonth() > 2 && $jewishCalendar->getJewishDayOfMonth() < 27) {
			return null;
		}

		$molad = $this->getMoladBasedTime($jewishCalendar->getMoladAsDate(), null, null, true);
		if ($molad == null && $jewishCalendar->getJewishDayOfMonth() > 26) {
			$jewishCalendar->addMonthsJewish(1);
			$molad = $this->getMoladBasedTime($jewishCalendar->getMoladAsDate(), null, null, true);
		}

		return $molad;
	}

	private function jewishCalendar(): JewishCalendar
	{
		$jewishCalendar = new JewishCalendar();
		$jewishCalendar->setGregorianDate($this->date->year, $this->date->month, $this->date->day);

		return $jewishCalendar;
	}

	private function getMoladBasedTime(?Carbon $moladBasedTime, ?Carbon $alos, ?Carbon $tzais, bool $techila): Carbon|null
	{
		if ($moladBasedTime == null) {
			return null;
		}

		$lastMidnight = $this->getMidnightLastNight();
		$midnightTonight = $this->getMidnightTonight();
		if ($moladBasedTime->lt($lastMidnight) || $moladBasedTime->gt($midnightTonight)) {
			return null;
		}

		if ($alos == null || $tzais == null) {
			return $moladBasedTime;
		}

		if ($moladBasedTime->gt($alos) && $moladBasedTime->lt($tzais)) {
			return $techila ? $tzais : $alos;
		}

		return $moladBasedTime;
	}
}