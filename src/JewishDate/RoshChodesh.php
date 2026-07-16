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

use PhpZmanim\JewishDate;

/**
 * @property int $jewishMonth;
 * @property int $jewishDay;
 * @property int $dayOfWeek;
 */
trait RoshChodesh
{
	// The following are from JewishCalendar

	public function isRoshChodesh(): bool
	{
		return ($this->jewishDay == 1 && $this->jewishMonth != JewishDate::TISHREI) || $this->jewishDay == 30;
	}

	public function isErevRoshChodesh(): bool
	{
		return $this->jewishDay == 29 && $this->jewishMonth != JewishDate::ELUL;
	}

	public function isMacharChodesh(): bool
	{
		return $this->dayOfWeek == 7 && ($this->jewishDay == 30 || $this->jewishDay == 29);
	}

	public function isShabbosMevorchim(): bool
	{
		return $this->dayOfWeek == 7 && $this->jewishDay >= 23 && $this->jewishDay <= 29 && $this->jewishMonth != JewishDate::ELUL;
	}

	public function isYomKippurKatan(): bool
	{
		$dayOfWeek = $this->dayOfWeek;
		$month = $this->jewishMonth;
		$day = $this->jewishDay;

		if ($month == JewishDate::ELUL || $month == JewishDate::TISHREI || $month == JewishDate::KISLEV || $month == JewishDate::NISSAN) {
			return false;
		}

		if ($day == 29 && $dayOfWeek != 6 && $dayOfWeek != 7) {
			return true;
		}

		if (($day == 27 || $day == 28) && $dayOfWeek == 5) {
			return true;
		}

		return false;
	}

	public function isBeHaB(): bool
	{
		$dayOfWeek = $this->dayOfWeek;
		$month = $this->jewishMonth;
		$day = $this->jewishDay;

		if ($month == JewishDate::CHESHVAN || $month == JewishDate::IYAR) {
			if (($dayOfWeek == 2 && $day > 4 && $day < 18) || ($dayOfWeek == 5 && $day > 7 && $day < 14)) {
				return true;
			}
		}

		return false;
	}
}
