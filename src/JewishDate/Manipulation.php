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
 * @property int $gregorianYear;
 * @property int $gregorianMonth;
 * @property int $gregorianDayOfMonth;
 * @property int $gregorianAbsDate;
 */
trait Manipulation
{
	// The following are from JewishDate

	public function resetDate(): self
	{
		return $this->setDate(Carbon::now());
	}

	public function addDays(int $amount): self
	{
		if ($amount < 0) {
			return $this->subDays(-$amount);
		}

		return $this->setGregorianAbsDate($this->gregorianAbsDate + $amount);
	}

	public function subDays(int $amount): self
	{
		if ($amount < 0) {
			return $this->addDays(-$amount);
		}

		return $this->setGregorianAbsDate($this->gregorianAbsDate - $amount);
	}

	public function addMonthsJewish(int $amount): self
	{
		if ($amount < 0) {
			return $this->subMonthsJewish(-$amount);
		}

		$year = $this->jewishYear;
		$month = $this->jewishMonth;
		for ($i = 0; $i < $amount; $i++) {
			if ($month == JewishDate::ELUL) {
				$month = JewishDate::TISHREI;
				$year++;
			} elseif ((!self::isJewishLeapYearForYear($year) && $month == JewishDate::ADAR)
				|| (self::isJewishLeapYearForYear($year) && $month == JewishDate::ADAR_II)) {
				$month = JewishDate::NISSAN;
			} else {
				$month++;
			}
		}

		$day = min($this->jewishDay, self::getDaysInJewishMonthForYear($year, $month));

		return $this->setJewishDate($year, $month, $day);
	}

	public function subMonthsJewish(int $amount): self
	{
		if ($amount < 0) {
			return $this->addMonthsJewish(-$amount);
		}

		$year = $this->jewishYear;
		$month = $this->jewishMonth;
		for ($i = 0; $i < $amount; $i++) {
			if ($month == JewishDate::TISHREI) {
				$month = JewishDate::ELUL;
				$year--;
			} elseif ($month == JewishDate::NISSAN) {
				$month = self::getLastMonthOfJewishYear($year);
			} elseif (!self::isJewishLeapYearForYear($year) && $month == JewishDate::ADAR) {
				$month = JewishDate::SHEVAT;
			} else {
				$month--;
			}
		}

		$day = min($this->jewishDay, self::getDaysInJewishMonthForYear($year, $month));

		return $this->setJewishDate($year, $month, $day);
	}

	public function addMonthsGregorian(int $amount): self
	{
		$monthIndex = ($this->gregorianMonth - 1) + $amount;
		$yearOffset = intdiv($monthIndex, 12);
		$month = $monthIndex % 12;
		if ($month < 0) {
			$month += 12;
			$yearOffset -= 1;
		}

		return $this->setGregorianDate($this->gregorianYear + $yearOffset, $month + 1, $this->gregorianDayOfMonth);
	}

	public function subMonthsGregorian(int $amount): self
	{
		return $this->addMonthsGregorian(-$amount);
	}

	public function addYearsJewish(int $amount, bool $useAdarAlephForLeapYear = false): self
	{
		if ($amount < 0) {
			return $this->subYearsJewish(-$amount, $useAdarAlephForLeapYear);
		}

		$targetYear = $this->jewishYear + $amount;

		if ($this->jewishMonth == JewishDate::ADAR && !self::isJewishLeapYearForYear($this->jewishYear) && self::isJewishLeapYearForYear($targetYear)) {
			$month = $useAdarAlephForLeapYear ? JewishDate::ADAR : JewishDate::ADAR_II;
		} else {
			$month = min($this->jewishMonth, self::getLastMonthOfJewishYear($targetYear));
		}

		$day = min($this->jewishDay, self::getDaysInJewishMonthForYear($targetYear, $month));

		return $this->setJewishDate($targetYear, $month, $day);
	}

	public function subYearsJewish(int $amount, bool $useAdarAlephForLeapYear = false): self
	{
		if ($amount < 0) {
			return $this->addYearsJewish(-$amount, $useAdarAlephForLeapYear);
		}

		$targetYear = $this->jewishYear - $amount;

		if ($this->jewishMonth == JewishDate::ADAR && !self::isJewishLeapYearForYear($this->jewishYear) && self::isJewishLeapYearForYear($targetYear)) {
			$month = $useAdarAlephForLeapYear ? JewishDate::ADAR : JewishDate::ADAR_II;
		} else {
			$month = min($this->jewishMonth, self::getLastMonthOfJewishYear($targetYear));
		}

		$day = min($this->jewishDay, self::getDaysInJewishMonthForYear($targetYear, $month));

		return $this->setJewishDate($targetYear, $month, $day);
	}

	public function addYearsGregorian(int $amount): self
	{
		return $this->setGregorianYear($this->gregorianYear + $amount);
	}

	public function subYearsGregorian(int $amount): self
	{
		return $this->addYearsGregorian(-$amount);
	}
}
