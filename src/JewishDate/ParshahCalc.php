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

namespace PhpZmanim\JewishDate;

use PhpZmanim\Torah\Parshah;
use PhpZmanim\JewishDate;

/**
 * @property int $jewishMonth;
 * @property int $jewishDay;
 * @property int $jewishYear;
 * @property int $dayOfWeek;
 * @property bool $inIsrael;
 */
trait ParshahCalc
{
	// The following are from JewishCalendar

	public function getParshah(): Parshah
	{
		if ($this->dayOfWeek != 7) {
			return Parshah::NONE;
		}

		$yearType = $this->getParshaYearType();
		$roshHashanaDayOfWeek = self::getJewishCalendarElapsedDays($this->jewishYear) % 7;
		$day = $roshHashanaDayOfWeek + self::getDaysSinceStartOfJewishYearForDate($this->jewishYear, $this->jewishMonth, $this->jewishDay);

		if ($yearType >= 0) {
			$week = intdiv($day, 7);
			return Parshah::PARSHA_LIST[$yearType][$week];
		}

		return Parshah::NONE;
	}

	public function getUpcomingParshah(): Parshah
	{
		$clone = $this->copy();
		$daysToShabbos = 7 - ($this->dayOfWeek % 7);

		$clone->addDays($daysToShabbos);
		while ($clone->getParshah() === Parshah::NONE) {
			$clone->addDays(7);
		}

		return $clone->getParshah();
	}

	public function getSpecialShabbos(): Parshah
	{
		if ($this->dayOfWeek != 7) {
			return Parshah::NONE;
		}

		if (($this->jewishMonth == JewishDate::SHEVAT && !self::isJewishLeapYearForYear($this->jewishYear))
			|| ($this->jewishMonth == JewishDate::ADAR && self::isJewishLeapYearForYear($this->jewishYear))) {
			if ($this->jewishDay == 25 || $this->jewishDay == 27 || $this->jewishDay == 29) {
				return Parshah::SHKALIM;
			}
		}

		if (($this->jewishMonth == JewishDate::ADAR && !self::isJewishLeapYearForYear($this->jewishYear)) || $this->jewishMonth == JewishDate::ADAR_II) {
			if ($this->jewishDay == 1) {
				return Parshah::SHKALIM;
			}
			if ($this->jewishDay == 8 || $this->jewishDay == 9 || $this->jewishDay == 11 || $this->jewishDay == 13) {
				return Parshah::ZACHOR;
			}
			if ($this->jewishDay == 18 || $this->jewishDay == 20 || $this->jewishDay == 22 || $this->jewishDay == 23) {
				return Parshah::PARA;
			}
			if ($this->jewishDay == 25 || $this->jewishDay == 27 || $this->jewishDay == 29) {
				return Parshah::HACHODESH;
			}
		}

		if ($this->jewishMonth == JewishDate::NISSAN) {
			if ($this->jewishDay == 1) {
				return Parshah::HACHODESH;
			}
			if ($this->jewishDay >= 8 && $this->jewishDay <= 14) {
				return Parshah::HAGADOL;
			}
		}

		if ($this->jewishMonth == JewishDate::AV) {
			if ($this->jewishDay >= 4 && $this->jewishDay <= 9) {
				return Parshah::CHAZON;
			}
			if ($this->jewishDay >= 10 && $this->jewishDay <= 16) {
				return Parshah::NACHAMU;
			}
		}

		if ($this->jewishMonth == JewishDate::TISHREI) {
			if ($this->jewishDay >= 3 && $this->jewishDay <= 8) {
				return Parshah::SHUVA;
			}
		}

		if ($this->getParshah() === Parshah::BESHALACH) {
			return Parshah::SHIRA;
		}

		return Parshah::NONE;
	}

	private function getParshaYearType(): int
	{
		$roshHashanaDayOfWeek = (self::getJewishCalendarElapsedDays($this->jewishYear) + 1) % 7;
		if ($roshHashanaDayOfWeek == 0) {
			$roshHashanaDayOfWeek = 7;
		}

		if (self::isJewishLeapYearForYear($this->jewishYear)) {
			switch ($roshHashanaDayOfWeek) {
				case 2:
					if (self::isKislevShortForYear($this->jewishYear)) { // BaCh
						return $this->inIsrael ? 14 : 6;
					}
					if (self::isCheshvanLongForYear($this->jewishYear)) { // BaSh
						return $this->inIsrael ? 15 : 7;
					}
					break;
				case 3: // GaK
					return $this->inIsrael ? 15 : 7;
				case 5:
					if (self::isKislevShortForYear($this->jewishYear)) { // HaCh
						return 8;
					}
					if (self::isCheshvanLongForYear($this->jewishYear)) { // HaSh
						return 9;
					}
					break;
				case 7:
					if (self::isKislevShortForYear($this->jewishYear)) { // ZaCh
						return 10;
					}
					if (self::isCheshvanLongForYear($this->jewishYear)) { // ZaSh
						return $this->inIsrael ? 16 : 11;
					}
					break;
			}
		} else {
			switch ($roshHashanaDayOfWeek) {
				case 2:
					if (self::isKislevShortForYear($this->jewishYear)) { // BaCh
						return 0;
					}
					if (self::isCheshvanLongForYear($this->jewishYear)) { // BaSh
						return $this->inIsrael ? 12 : 1;
					}
					break;
				case 3: // GaK
					return $this->inIsrael ? 12 : 1;
				case 5:
					if (self::isCheshvanLongForYear($this->jewishYear)) { // HaSh
						return 3;
					}
					if (!self::isKislevShortForYear($this->jewishYear)) { // Hak
						return $this->inIsrael ? 13 : 2;
					}
					break;
				case 7:
					if (self::isKislevShortForYear($this->jewishYear)) { // ZaCh
						return 4;
					}
					if (self::isCheshvanLongForYear($this->jewishYear)) { // ZaSh
						return 5;
					}
					break;
			}
		}

		return -1;
	}
}
