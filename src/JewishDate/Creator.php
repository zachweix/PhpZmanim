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

use Carbon\Carbon;
use PhpZmanim\JewishDate;

/**
 * @property int $jewishYear;
 * @property int $jewishMonth;
 * @property int $jewishDay;
 * @property int $moladHours;
 * @property int $moladMinutes;
 * @property int $moladChalakim;
 * @property int $gregorianYear;
 * @property int $gregorianMonth;
 * @property int $gregorianDayOfMonth;
 * @property int $dayOfWeek;
 * @property int $gregorianAbsDate;
 * @property bool $inIsrael;
 * @property bool $isMukafChoma;
 * @property bool $useModernHolidays;
 */
trait Creator
{
	// The following are from JewishDate and JewishCalendar

	public function __construct(Carbon|int|null $jewishYear = null, int|null $jewishMonth = null, int|null $jewishDayOfMonth = null, bool $inIsrael = false)
	{
		$this->setInIsrael($inIsrael);

		// $jewishYear refers to a given English date
		if ($jewishYear instanceof Carbon) {
			$this->setDate($jewishYear);
			return;
		}

		if (is_null($jewishYear)) {
			$this->resetDate();
			return;
		}

		if (!is_null($jewishMonth) && !is_null($jewishDayOfMonth)) {
			$this->setJewishDate($jewishYear, $jewishMonth, $jewishDayOfMonth);
			return;
		}

		// At this point we're going to assume that $jewishYear refers to a given molad
		$molad = $jewishYear;
		$this->setGregorianAbsDate(self::moladToAbsDate($molad));
		$conjunctionDay = intdiv($molad, JewishDate::CHALAKIM_PER_DAY);
		$this->setMoladTime($molad - $conjunctionDay * JewishDate::CHALAKIM_PER_DAY);
	}

	public static function create(Carbon|int|null $jewishYear = null, int|null $jewishMonth = null, int|null $jewishDayOfMonth = null, bool $inIsrael = false): JewishDate
	{
		return new static($jewishYear, $jewishMonth, $jewishDayOfMonth, $inIsrael);
	}

	public static function createFromDate(Carbon|int $year, int|null $month = null, int|null $day = null): JewishDate
	{
		if (! ($year instanceof Carbon)) {
			$year = Carbon::createFromDate($year, $month, $day);
		}

		return new static($year);
	}

	public function copy(): JewishDate
	{
		return clone $this;
	}

	public function equals(JewishDate $jewishDate): bool
	{
		if ($this === $jewishDate) {
			return true;
		}

		return $this->gregorianAbsDate === $jewishDate->getAbsDate()
			&& $this->inIsrael === $jewishDate->getInIsrael();
	}

	public function compareTo(JewishDate $jewishDate): int
	{
		return $this->gregorianAbsDate <=> $jewishDate->getAbsDate();
	}
}
