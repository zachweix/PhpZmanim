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

/**
 * @property int $gregorianYear;
 * @property int $gregorianMonth;
 * @property int $gregorianDayOfMonth;
 * @property int $dayOfWeek;
 * @property int $gregorianAbsDate;
 */
trait Gregorian
{
	// The following are from JewishDate

	public function toCarbon(DateTimeZone|string|int|null $timezone = null): Carbon
	{
		return Carbon::createMidnightDate($this->gregorianYear, $this->gregorianMonth, $this->gregorianDayOfMonth, $timezone);
	}

	public function formatGregorian(): string
	{
		return sprintf('%d-%02d-%02d', $this->gregorianYear, $this->gregorianMonth, $this->gregorianDayOfMonth);
	}

	private function absDateToGregorian(): void
	{
		$year = intdiv($this->gregorianAbsDate, 366);
		while ($this->gregorianAbsDate >= self::gregorianDateToAbsDate($year + 1, 1, 1)) {
			$year++;
		}

		$month = 1;
		while ($this->gregorianAbsDate > self::gregorianDateToAbsDate($year, $month, self::getLastDayOfGregorianMonth($year, $month))) {
			$month++;
		}

		$this->gregorianYear = $year;
		$this->gregorianMonth = $month;
		$this->gregorianDayOfMonth = $this->gregorianAbsDate - self::gregorianDateToAbsDate($year, $month, 1) + 1;
	}

	private static function gregorianDateToAbsDate(int $year, int $month, int $dayOfMonth): int
	{
		$absDate = $dayOfMonth;
		for ($m = $month - 1; $m > 0; $m--) {
			$absDate += self::getLastDayOfGregorianMonth($year, $m);
		}

		$lastYear = $year - 1;
		return $absDate
			+ 365 * $lastYear
			+ intdiv($lastYear, 4)
			- intdiv($lastYear, 100)
			+ intdiv($lastYear, 400);
	}

	private static function getLastDayOfGregorianMonth(int $year, int $month): int
	{
		switch ($month) {
			case 2:
				return self::isGregorianLeapYear($year) ? 29 : 28;
			case 4:
			case 6:
			case 9:
			case 11:
				return 30;
			default:
				return 31;
		}
	}

	private static function isGregorianLeapYear(int $year): bool
	{
		return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
	}

	private static function validateGregorianDate(int $year, int $month, int $dayOfMonth): void
	{
		self::validateGregorianMonth($month);
		self::validateGregorianDayOfMonth($dayOfMonth);
		self::validateGregorianYear($year);
	}

	private static function validateGregorianMonth(int $month): void
	{
		if ($month > 12 || $month < 1) {
			throw new \InvalidArgumentException("The Gregorian month has to be between 1 - 12. " . $month . " is invalid.");
		}
	}

	private static function validateGregorianDayOfMonth(int $dayOfMonth): void
	{
		if ($dayOfMonth <= 0) {
			throw new \InvalidArgumentException("The day of month can't be less than 1. " . $dayOfMonth . " is invalid.");
		}
	}

	private static function validateGregorianYear(int $year): void
	{
		if ($year < 1) {
			throw new \InvalidArgumentException("Years < 1 can't be calculated. " . $year . " is invalid.");
		}
	}
}
