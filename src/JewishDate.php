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

namespace PhpZmanim;

use Carbon\Carbon;

class JewishDate
{
	use JewishDate\Creator;
	use JewishDate\Gregorian;
	use JewishDate\JewishCalc;
	use JewishDate\Molad;
	use JewishDate\Manipulation;
	use JewishDate\Holidays;
	use JewishDate\Melacha;
	use JewishDate\ParshahCalc;
	use JewishDate\RoshChodesh;
	use JewishDate\MoladZmanim;
	use JewishDate\Tekufa;
	use JewishDate\DafYomi;
	use JewishDate\TefilaRules;
	use JewishDate\Formatting;

	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private int $jewishYear;
	private int $jewishMonth;
	private int $jewishDay;
	private int $moladHours = 0;
	private int $moladMinutes = 0;
	private int $moladChalakim = 0;

	private int $gregorianYear;
	private int $gregorianMonth;
	private int $gregorianDayOfMonth;
	private int $dayOfWeek;
	private int $gregorianAbsDate;

	private bool $inIsrael = false;
	private bool $isMukafChoma = false;
	private bool $useModernHolidays = false;

	const NISSAN = 1;
	const IYAR = 2;
	const SIVAN = 3;
	const TAMMUZ = 4;
	const AV = 5;
	const ELUL = 6;
	const TISHREI = 7;
	const CHESHVAN = 8;
	const KISLEV = 9;
	const TEVES = 10;
	const SHEVAT = 11;
	const ADAR = 12;
	const ADAR_II = 13;

	const JEWISH_EPOCH = -1373429;
	const CHALAKIM_PER_MINUTE = 18;
	const CHALAKIM_PER_HOUR = 1080;
	const CHALAKIM_PER_DAY = 25920;
	const CHALAKIM_PER_MONTH = 765433;
	const CHALAKIM_MOLAD_TOHU = 31524;

	const CHASERIM = 0;
	const KESIDRAN = 1;
	const SHELAIMIM = 2;

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function getJewishYear(): int
	{
		return $this->jewishYear;
	}

	public function setJewishYear(int $year): self
	{
		$month = min($this->jewishMonth, self::getLastMonthOfJewishYear($year));
		$day = min($this->jewishDay, self::getDaysInJewishMonthForYear($year, $month));

		return $this->setJewishDate($year, $month, $day);
	}

	public function getJewishMonth(): int
	{
		return $this->jewishMonth;
	}

	public function setJewishMonth(int $month): self
	{
		$day = min($this->jewishDay, self::getDaysInJewishMonthForYear($this->jewishYear, $month));

		return $this->setJewishDate($this->jewishYear, $month, $day);
	}

	public function getJewishDayOfMonth(): int
	{
		return $this->jewishDay;
	}

	public function setJewishDayOfMonth(int $dayOfMonth): self
	{
		return $this->setJewishDate($this->jewishYear, $this->jewishMonth, $dayOfMonth);
	}

	public function getGregorianYear(): int
	{
		return $this->gregorianYear;
	}

	public function setGregorianYear(int $year): self
	{
		self::validateGregorianYear($year);

		return $this->setGregorianDate($year, $this->gregorianMonth, $this->gregorianDayOfMonth);
	}

	public function getGregorianMonth(): int
	{
		return $this->gregorianMonth;
	}

	public function setGregorianMonth(int $month): self
	{
		self::validateGregorianMonth($month);

		return $this->setGregorianDate($this->gregorianYear, $month, $this->gregorianDayOfMonth);
	}

	public function getGregorianDayOfMonth(): int
	{
		return $this->gregorianDayOfMonth;
	}

	public function setGregorianDayOfMonth(int $dayOfMonth): self
	{
		self::validateGregorianDayOfMonth($dayOfMonth);

		return $this->setGregorianDate($this->gregorianYear, $this->gregorianMonth, $dayOfMonth);
	}

	public function getDayOfWeek(): int
	{
		return $this->dayOfWeek;
	}

	public function getAbsDate(): int
	{
		return $this->gregorianAbsDate;
	}

	public function getMoladHours(): int
	{
		return $this->moladHours;
	}

	public function setMoladHours(int $moladHours): self
	{
		$this->moladHours = $moladHours;

		return $this;
	}

	public function getMoladMinutes(): int
	{
		return $this->moladMinutes;
	}

	public function setMoladMinutes(int $moladMinutes): self
	{
		$this->moladMinutes = $moladMinutes;

		return $this;
	}

	public function getMoladChalakim(): int
	{
		return $this->moladChalakim;
	}

	public function setMoladChalakim(int $moladChalakim): self
	{
		$this->moladChalakim = $moladChalakim;

		return $this;
	}

	public function getInIsrael(): bool
	{
		return $this->inIsrael;
	}

	public function setInIsrael(bool $inIsrael): self
	{
		$this->inIsrael = $inIsrael;

		return $this;
	}

	public function getIsMukafChoma(): bool
	{
		return $this->isMukafChoma;
	}

	public function setIsMukafChoma(bool $isMukafChoma): self
	{
		$this->isMukafChoma = $isMukafChoma;

		return $this;
	}

	public function getUseModernHolidays(): bool
	{
		return $this->useModernHolidays;
	}

	public function setUseModernHolidays(bool $useModernHolidays): self
	{
		$this->useModernHolidays = $useModernHolidays;

		return $this;
	}

	public function setDate(Carbon $date): self
	{
		return $this->setGregorianDate($date->year, $date->month, $date->day);
	}

	public function setGregorianDate(int $year, int $month, int $dayOfMonth): self
	{
		self::validateGregorianDate($year, $month, $dayOfMonth);

		$lastDay = self::getLastDayOfGregorianMonth($year, $month);
		if ($dayOfMonth > $lastDay) {
			$dayOfMonth = $lastDay;
		}

		return $this->setGregorianAbsDate(self::gregorianDateToAbsDate($year, $month, $dayOfMonth));
	}

	public function setJewishDate(int $year, int $month, int $dayOfMonth, int $hours = 0, int $minutes = 0, int $chalakim = 0): self
	{
		self::validateJewishDate($year, $month, $dayOfMonth, $hours, $minutes, $chalakim);

		$daysInMonth = self::getDaysInJewishMonthForYear($year, $month);
		if ($dayOfMonth > $daysInMonth) {
			$dayOfMonth = $daysInMonth;
		}

		$this->moladHours = $hours;
		$this->moladMinutes = $minutes;
		$this->moladChalakim = $chalakim;

		return $this->setGregorianAbsDate(self::jewishDateToAbsDate($year, $month, $dayOfMonth));
	}

	private function setGregorianAbsDate(int $absDate): self
	{
		$this->gregorianAbsDate = $absDate;
		$this->absDateToGregorian();
		$this->absDateToJewish();
		$this->dayOfWeek = abs($absDate % 7) + 1;

		return $this;
	}
}
