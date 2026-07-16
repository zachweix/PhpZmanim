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
 * @property int $jewishYear;
 * @property int $jewishMonth;
 * @property int $jewishDay;
 * @property int $gregorianAbsDate;
 */
trait JewishCalc
{
	// The following are from JewishDate

	public function isJewishLeapYear(): bool
	{
		return self::isJewishLeapYearForYear($this->jewishYear);
	}

	public function getDaysInJewishYear(): int
	{
		return self::getDaysInJewishYearForYear($this->jewishYear);
	}

	public function getDaysInJewishMonth(): int
	{
		return self::getDaysInJewishMonthForYear($this->jewishYear, $this->jewishMonth);
	}

	public function isCheshvanLong(): bool
	{
		return self::isCheshvanLongForYear($this->jewishYear);
	}

	public function isKislevShort(): bool
	{
		return self::isKislevShortForYear($this->jewishYear);
	}

	public function getCheshvanKislevKviah(): int
	{
		$isCheshvanLong = self::isCheshvanLongForYear($this->jewishYear);
		$isKislevShort = self::isKislevShortForYear($this->jewishYear);

		if ($isCheshvanLong && !$isKislevShort) {
			return JewishDate::SHELAIMIM;
		} elseif (!$isCheshvanLong && $isKislevShort) {
			return JewishDate::CHASERIM;
		} else {
			return JewishDate::KESIDRAN;
		}
	}

	public function getDaysSinceStartOfJewishYear(): int
	{
		return self::getDaysSinceStartOfJewishYearForDate($this->jewishYear, $this->jewishMonth, $this->jewishDay);
	}

	public function formatJewish(): string
	{
		return sprintf('%d-%02d-%02d', $this->jewishYear, $this->jewishMonth, $this->jewishDay);
	}

	private function absDateToJewish(): void
	{
		$this->jewishYear = intdiv($this->gregorianAbsDate - JewishDate::JEWISH_EPOCH, 366);

		while ($this->gregorianAbsDate >= self::jewishDateToAbsDate($this->jewishYear + 1, JewishDate::TISHREI, 1)) {
			$this->jewishYear++;
		}

		$this->jewishMonth = $this->gregorianAbsDate < self::jewishDateToAbsDate($this->jewishYear, JewishDate::NISSAN, 1)
			? JewishDate::TISHREI
			: JewishDate::NISSAN;

		while ($this->gregorianAbsDate > self::jewishDateToAbsDate($this->jewishYear, $this->jewishMonth, self::getDaysInJewishMonthForYear($this->jewishYear, $this->jewishMonth))) {
			$this->jewishMonth++;
		}

		$this->jewishDay = $this->gregorianAbsDate - self::jewishDateToAbsDate($this->jewishYear, $this->jewishMonth, 1) + 1;
	}

	private static function jewishDateToAbsDate(int $year, int $month, int $dayOfMonth): int
	{
		$elapsed = self::getDaysSinceStartOfJewishYearForDate($year, $month, $dayOfMonth);

		return $elapsed + self::getJewishCalendarElapsedDays($year) + JewishDate::JEWISH_EPOCH;
	}

	private static function getDaysSinceStartOfJewishYearForDate(int $year, int $month, int $dayOfMonth): int
	{
		$elapsedDays = $dayOfMonth;

		if ($month < JewishDate::TISHREI) {
			for ($m = JewishDate::TISHREI; $m <= self::getLastMonthOfJewishYear($year); $m++) {
				$elapsedDays += self::getDaysInJewishMonthForYear($year, $m);
			}
			for ($m = JewishDate::NISSAN; $m < $month; $m++) {
				$elapsedDays += self::getDaysInJewishMonthForYear($year, $m);
			}
		} else {
			for ($m = JewishDate::TISHREI; $m < $month; $m++) {
				$elapsedDays += self::getDaysInJewishMonthForYear($year, $m);
			}
		}

		return $elapsedDays;
	}

	private static function isJewishLeapYearForYear(int $year): bool
	{
		return ((7 * $year) + 1) % 19 < 7;
	}

	private static function getLastMonthOfJewishYear(int $year): int
	{
		return self::isJewishLeapYearForYear($year) ? JewishDate::ADAR_II : JewishDate::ADAR;
	}

	private static function getJewishMonthOfYear(int $year, int $month): int
	{
		$isLeapYear = self::isJewishLeapYearForYear($year);
		return ($month + ($isLeapYear ? 6 : 5)) % ($isLeapYear ? 13 : 12) + 1;
	}

	private static function getDaysInJewishYearForYear(int $year): int
	{
		return self::getJewishCalendarElapsedDays($year + 1) - self::getJewishCalendarElapsedDays($year);
	}

	private static function isCheshvanLongForYear(int $year): bool
	{
		return self::getDaysInJewishYearForYear($year) % 10 == 5;
	}

	private static function isKislevShortForYear(int $year): bool
	{
		return self::getDaysInJewishYearForYear($year) % 10 == 3;
	}

	private static function getDaysInJewishMonthForYear(int $year, int $month): int
	{
		if (($month == JewishDate::IYAR) || ($month == JewishDate::TAMMUZ) || ($month == JewishDate::ELUL)
			|| (($month == JewishDate::CHESHVAN) && !self::isCheshvanLongForYear($year))
			|| (($month == JewishDate::KISLEV) && self::isKislevShortForYear($year))
			|| ($month == JewishDate::TEVES)
			|| (($month == JewishDate::ADAR) && !self::isJewishLeapYearForYear($year))
			|| ($month == JewishDate::ADAR_II)) {
			return 29;
		}

		return 30;
	}

	private static function validateJewishDate(int $year, int $month, int $dayOfMonth, int $hours, int $minutes, int $chalakim): void
	{
		if ($month < JewishDate::NISSAN || $month > self::getLastMonthOfJewishYear($year)) {
			throw new \InvalidArgumentException("The Jewish month has to be between 1 and 12 (or 13 on a leap year). "
				. $month . " is invalid for the year " . $year . ".");
		}
		if ($dayOfMonth < 1 || $dayOfMonth > 30) {
			throw new \InvalidArgumentException("The Jewish day of month can't be < 1 or > 30.  " . $dayOfMonth . " is invalid.");
		}
		if (($year < 3761) || ($year == 3761 && ($month >= JewishDate::TISHREI && $month < JewishDate::TEVES))
			|| ($year == 3761 && $month == JewishDate::TEVES && $dayOfMonth < 18)) {
			throw new \InvalidArgumentException(
				"A Jewish date earlier than 18 Teves, 3761 (1/1/1 Gregorian) can't be set. " . $year . ", " . $month
					. ", " . $dayOfMonth . " is invalid.");
		}
		if ($hours < 0 || $hours > 23) {
			throw new \InvalidArgumentException("Hours < 0 or > 23 can't be set. " . $hours . " is invalid.");
		}
		if ($minutes < 0 || $minutes > 59) {
			throw new \InvalidArgumentException("Minutes < 0 or > 59 can't be set. " . $minutes . " is invalid.");
		}
		if ($chalakim < 0 || $chalakim > 17) {
			throw new \InvalidArgumentException(
				"Chalakim/parts < 0 or > 17 can't be set. " . $chalakim
					. " is invalid. For larger numbers such as 793 (TaShTzaG) break the chalakim into minutes (18 chalakim per minutes, so it would be 44 minutes and 1 chelek in the case of 793 (TaShTzaG)");
		}
	}
}
