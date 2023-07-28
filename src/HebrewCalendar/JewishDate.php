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

namespace PhpZmanim\HebrewCalendar;

use Carbon\Carbon;

class JewishDate {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/
	private $jewishMonth;
	private $jewishDay;
	private $jewishYear;
	private $moladHours;
	private $moladMinutes;
	private $moladChalakim;

	private $gregorianMonth;
	private $gregorianDayOfMonth;
	private $gregorianYear;
	private $dayOfWeek;
	private $gregorianAbsDate;

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
	| CONSTRUCTOR
	|--------------------------------------------------------------------------
	*/

	public function __construct($jewishYear = null, $jewishMonth = null, $jewishDayOfMonth = null) {
		if (is_null($jewishYear)) {
			$this->resetDate();
			return;
		}

		// $jewishYear refers to a given English date
		if ($jewishYear instanceof Carbon) {
			$this->setDate($jewishYear);
			return;
		}

		if (!is_null($jewishYear) && !is_null($jewishMonth) && !is_null($jewishDayOfMonth)) {
			$this->setJewishDate($jewishYear, $jewishMonth, $jewishDayOfMonth);
			return;
		}

		// At this point we're going to assume that $jewishYear refers to a given molad
		$molad = $jewishYear;
		$this->absDateToDate($this->moladToAbsDate($molad));
		$conjunctionDay = (int) ($molad / self::CHALAKIM_PER_DAY);
		$conjunctionParts = (int) ($molad - $conjunctionDay * self::CHALAKIM_PER_DAY);
		$this->setMoladTime($conjunctionParts);
	}

	/*
	|--------------------------------------------------------------------------
	| MOLAD HOURS, MINUTES, CHALAKIM
	|--------------------------------------------------------------------------
	*/

	public function getMoladHours() {
		return $this->moladHours;
	}

	public function setMoladHours($moladHours) {
		$this->moladHours = $moladHours;
	}

	public function getMoladMinutes() {
		return $this->moladMinutes;
	}

	public function setMoladMinutes($moladMinutes) {
		$this->moladMinutes = $moladMinutes;
	}

	public function setMoladChalakim($moladChalakim) {
		$this->moladChalakim = $moladChalakim;
	}

	public function getMoladChalakim() {
		return $this->moladChalakim;
	}

	/*
	|--------------------------------------------------------------------------
	| GREGORIAN CALCULATIONS
	|--------------------------------------------------------------------------
	*/

	public static function getLastDayOfGregorianMonth($month, $year) {
		switch ($month) {
			case 2:
				if (self::isGregorianLeapYear($year)) {
					return 29;
				} else {
					return 28;
				}
			case 4:
			case 6:
			case 9:
			case 11:
				return 30;
			default:
				return 31;
		}
	}

	public static function isGregorianLeapYear($year) {
		return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
	}

	private function absDateToDate($absDate) {
		$year = $absDate / 366;
		while ($absDate >= $this->gregorianDateToAbsDate($year + 1, 1, 1)) {
			$year++;
		}

		$$month = 1;
		while ($absDate > $this->gregorianDateToAbsDate($year, $month, self::getLastDayOfGregorianMonth($month, $year))) {
			$month++;
		}

		$dayOfMonth = $absDate - $this->gregorianDateToAbsDate($year, $month, 1) + 1;
		$this->setInternalGregorianDate($year, $month, $dayOfMonth);
	}

	public function getAbsDate() {
		return $this->gregorianAbsDate;
	}

	private static function gregorianDateToAbsDate($year, $month, $dayOfMonth) {
		$absDate = $dayOfMonth;
		for ($m = $month - 1; $m > 0; $m--) {
			$absDate += self::getLastDayOfGregorianMonth($m, $year);
		}

		$lastYear = $year - 1;
		return ($absDate
				+ 365 * $lastYear
				+ (int) ($lastYear / 4)
				- (int) ($lastYear / 100)
		+ (int) ($lastYear / 400));
	}

	public static function isJewishLeapYear($year) {
		return ((7 * $year) + 1) % 19 < 7;
	}

	private static function getLastMonthOfJewishYear($year) {
		return self::isJewishLeapYear($year) ? self::ADAR_II : self::ADAR;
	}

	/*
	|--------------------------------------------------------------------------
	| MOLAD CALCULATIONS
	|--------------------------------------------------------------------------
	*/

	public static function getJewishCalendarElapsedDays($year) {
		$chalakimSince = self::getChalakimSinceMoladTohu($year, self::TISHREI);
		$moladDay = (int) ($chalakimSince / self::CHALAKIM_PER_DAY);
		$moladParts = (int) ($chalakimSince - $moladDay * self::CHALAKIM_PER_DAY);

		return self::addDechiyos($year, $moladDay, $moladParts);
	}

	private static function addDechiyos($year, $moladDay, $moladParts) {
		$roshHashanaDay = $moladDay; 

		if (($moladParts >= 19440)
				|| ((($moladDay % 7) == 2)
						&& ($moladParts >= 9924)
				&& !self::isJewishLeapYear($year))
				|| ((($moladDay % 7) == 1)
						&& ($moladParts >= 16789)
				&& (self::isJewishLeapYear($year - 1)))) {
			$roshHashanaDay += 1;
		}
	
		if ((($roshHashanaDay % 7) == 0)
				|| (($roshHashanaDay % 7) == 3)
				|| (($roshHashanaDay % 7) == 5)) {
			$roshHashanaDay = $roshHashanaDay + 1;
		}
		return $roshHashanaDay;
	}

	public static function getChalakimSinceMoladTohu($year, $month) {
		$monthOfYear = self::getJewishMonthOfYear($year, $month);

		$lastYear = $year - 1;
		$monthsElapsed = (235 * (int) ($lastYear / 19))
				+ (12 * ($lastYear % 19))
				+ (int) ((7 * ($lastYear % 19) + 1) / 19)
				+ ($monthOfYear - 1);

		return self::CHALAKIM_MOLAD_TOHU + (self::CHALAKIM_PER_MONTH * $monthsElapsed);
	}

	/*
	|--------------------------------------------------------------------------
	| JEWISH DATE
	|--------------------------------------------------------------------------
	*/

	private static function getJewishMonthOfYear($year, $month) {
		$isLeapYear = self::isJewishLeapYear($year);
		return ($month + ($isLeapYear ? 6 : 5)) % ($isLeapYear ? 13 : 12) + 1;
	}

	private static function validateJewishDate($year, $month, $dayOfMonth, $hours, $minutes, $chalakim) {
		if ($month < self::NISSAN || $month > self::getLastMonthOfJewishYear($year)) {
			throw new \Exception("The Jewish month has to be between 1 and 12 (or 13 on a leap year). "
					. $month . " is invalid for the year " . $year . ".");
		}
		if ($dayOfMonth < 1 || $dayOfMonth > 30) {
			throw new \Exception("The Jewish day of month can't be < 1 or > 30.  " . $dayOfMonth
					. " is invalid.");
		}

		if (($year < 3761) || ($year == 3761 && ($month >= self::TISHREI && $month < self::TEVES))
				|| ($year == 3761 && $month == self::TEVES && $dayOfMonth < 18)) {
			throw new \Exception(
					"A Jewish date earlier than 18 Teves, 3761 (1/1/1 Gregorian) can't be set. " . $year . ", " . $month
							. ", " . $dayOfMonth . " is invalid.");
		}
		if ($hours < 0 || $hours > 23) {
			throw new \Exception("Hours < 0 or > 23 can't be set. " . $hours . " is invalid.");
		}

		if ($minutes < 0 || $minutes > 59) {
			throw new \Exception("Minutes < 0 or > 59 can't be set. " . $minutes . " is invalid.");
		}

		if ($chalakim < 0 || $chalakim > 17) {
			throw new \Exception(
					"Chalakim/parts < 0 or > 17 can't be set. "
							. $chalakim
							. " is invalid. For larger numbers such as 793 (TaShTzaG) break the chalakim into minutes (18 chalakim per minutes, so it would be 44 minutes and 1 chelek in the case of 793 (TaShTzaG)");
		}
	}

	private static function validateGregorianDate($year, $month, $dayOfMonth) {
		self::validateGregorianMonth($month);
		self::validateGregorianDayOfMonth($dayOfMonth);
		self::validateGregorianYear($year);
	}

	private static function validateGregorianMonth($month) {
		if ($month > 12 || $month < 1) {
			throw new \Exception("The Gregorian month has to be between 1 - 12. " . $month
					. " is invalid.");
		}
	}

	private static function validateGregorianDayOfMonth($dayOfMonth) {
		if ($dayOfMonth <= 0) {
			throw new \Exception("The day of month can't be less than 1. " . $dayOfMonth . " is invalid.");
		}
	}

	private static function validateGregorianYear($year) {
		if ($year < 1) {
			throw new \Exception("Years < 1 can't be claculated. " . $year . " is invalid.");
		}
	}

	public static function getDaysInJewishYear($year) {
		return self::getJewishCalendarElapsedDays($year + 1) - self::getJewishCalendarElapsedDays($year);
	}

	public static function isCheshvanLong($year) {
		return self::getDaysInJewishYear($year) % 10 == 5;
	}

	public static function isKislevShort($year) {
		return self::getDaysInJewishYear($year) % 10 == 3;
	}

	public function getCheshvanKislevKviah() {
		$is_cheshvan_long = self::isCheshvanLong($this->getJewishYear());
		$is_kislev_short = self::isKislevShort($this->getJewishYear());
		if ($is_cheshvan_long && !$is_kislev_short) {
			return self::SHELAIMIM;
		} else if (!$is_cheshvan_long && $is_kislev_short) {
			return self::CHASERIM;
		} else {
			return self::KESIDRAN;
		}
	}

	public static function getDaysInJewishMonth($month, $year) {
		if (($month == self::IYAR) || ($month == self::TAMMUZ) || ($month == self::ELUL) || (($month == self::CHESHVAN) && !(self::isCheshvanLong($year)))
				|| (($month == self::KISLEV) && self::isKislevShort($year)) || ($month == self::TEVES)
				|| (($month == self::ADAR) && !(self::isJewishLeapYear($year))) || ($month == self::ADAR_II)) {
			return 29;
		} else {
			return 30;
		}
	}

	private function absDateToJewishDate() {
		$this->jewishYear = (int) (($this->gregorianAbsDate - self::JEWISH_EPOCH) / 366);

		while ($this->gregorianAbsDate >= self::jewishDateToAbsDate($this->jewishYear + 1, self::TISHREI, 1)) {
			$this->jewishYear++;
		}

		if ($this->gregorianAbsDate < self::jewishDateToAbsDate($this->jewishYear, self::NISSAN, 1)) {
			$this->jewishMonth = self::TISHREI;
		} else {
			$this->jewishMonth = self::NISSAN;
		}

		while ($this->gregorianAbsDate > self::jewishDateToAbsDate($this->jewishYear, $this->jewishMonth, self::getDaysInJewishMonth($this->getJewishMonth(), $this->getJewishYear()))) {
			$this->jewishMonth++;
		}
		// Calculate the day by subtraction
		$this->jewishDay = $this->gregorianAbsDate - self::jewishDateToAbsDate($this->jewishYear, $this->jewishMonth, 1) + 1;
	}

	private static function jewishDateToAbsDate($year, $month, $dayOfMonth) {
		$elapsed = self::getDaysSinceStartOfJewishYear($year, $month, $dayOfMonth);

		return $elapsed + self::getJewishCalendarElapsedDays($year) + self::JEWISH_EPOCH;
	}

	/*
	|--------------------------------------------------------------------------
	| MOLAD
	|--------------------------------------------------------------------------
	*/

	public function getMolad() {
		$moladDate = new JewishDate(self::getChalakimSinceMoladTohu($this->jewishYear, $this->jewishMonth));
		if ($moladDate->getMoladHours() >= 6) {
			$moladDate->addDay();
		}
		$moladDate->setMoladHours(($moladDate->getMoladHours() + 18) % 24);
		return $moladDate;
	}

	private static function moladToAbsDate($chalakim) {
		return (int) ($chalakim / self::CHALAKIM_PER_DAY) + self::JEWISH_EPOCH;
	}

	private function setMoladTime($chalakim) {
		$adjustedChalakim = $chalakim;
		$this->setMoladHours($adjustedChalakim / self::CHALAKIM_PER_HOUR);
		$adjustedChalakim = $adjustedChalakim - ($this->getMoladHours() * self::CHALAKIM_PER_HOUR);
		$this->setMoladMinutes($adjustedChalakim / self::CHALAKIM_PER_MINUTE);
		$this->setMoladChalakim($adjustedChalakim - $this->moladMinutes * self::CHALAKIM_PER_MINUTE);
	}

	/*
	|--------------------------------------------------------------------------
	| SETTING DATE
	|--------------------------------------------------------------------------
	*/

	public static function getDaysSinceStartOfJewishYear($year, $month, $dayOfMonth) {
		$elapsedDays = $dayOfMonth;

		if ($month < self::TISHREI) {
			for ($m = self::TISHREI; $m <= self::getLastMonthOfJewishYear($year); $m++) {
				$elapsedDays += self::getDaysInJewishMonth($m, $year);
			}
			for ($m = self::NISSAN; $m < $month; $m++) {
				$elapsedDays += self::getDaysInJewishMonth($m, $year);
			}
		} else {
			for ($m = self::TISHREI; $m < $month; $m++) {
				$elapsedDays += self::getDaysInJewishMonth($m, $year);
			}
		}
		return $elapsedDays;
	}

	public function setDate(Carbon $calendar) {
		$this->validateGregorianDate($calendar->year, $calendar->month, $calendar->day);
		$this->setInternalGregorianDate($calendar->year, $calendar->month, $calendar->day);

		return $this;
	}

	public function setGregorianDate($year, $month, $dayOfMonth) {
		$this->validateGregorianDate($year, $month, $dayOfMonth);
		$this->setInternalGregorianDate($year, $month, $dayOfMonth);
	}

	private function setInternalGregorianDate($year, $month, $dayOfMonth) {
		if ($dayOfMonth > self::getLastDayOfGregorianMonth($month, $year)) {
			$dayOfMonth = self::getLastDayOfGregorianMonth($month, $year);
		}
		// init month, date, year
		$this->gregorianMonth = $month;
		$this->gregorianDayOfMonth = $dayOfMonth;
		$this->gregorianYear = $year;

		$this->gregorianAbsDate = self::gregorianDateToAbsDate($this->gregorianYear, $this->gregorianMonth, $this->gregorianDayOfMonth);
		$this->absDateToJewishDate();

		$this->dayOfWeek = abs($this->gregorianAbsDate % 7) + 1;
	}

	public function setJewishDate($year, $month, $dayOfMonth, $hours = 0, $minutes = 0, $chalakim = 0) {
		self::validateJewishDate($year, $month, $dayOfMonth, $hours, $minutes, $chalakim);

		// if 30 is passed for a month that only has 29 days (for example by rolling the month from a month that had 30
		// days to a month that only has 29) set the date to 29th
		if ($dayOfMonth > self::getDaysInJewishMonth($month, $year)) {
			$dayOfMonth = self::getDaysInJewishMonth($month, $year);
		}

		$this->jewishMonth = $month;
		$this->jewishDay = $dayOfMonth;
		$this->jewishYear = $year;
		$this->moladHours = $hours;
		$this->moladMinutes = $minutes;
		$this->moladChalakim = $chalakim;

		$this->gregorianAbsDate = self::jewishDateToAbsDate($this->jewishYear, $this->jewishMonth, $this->jewishDay); // reset Gregorian date
		$this->absDateToDate($this->gregorianAbsDate);
	}

	public function getGregorianCalendar() {
		$calendar = Carbon::createMidnightDate($this->gregorianYear, $this->gregorianMonth, $this->gregorianDayOfMonth);
		return $calendar;
	}

	public function resetDate() {
		return $this->setDate(Carbon::now());
	}

	public function addDays($amount) {
		if ($amount < 1) {
			throw new \Exception("The amount cannot be a negative number");
		}

		// Change Gregorian date
		for ($i = 0; $i < $amount; $i++) {
			if ($this->gregorianDayOfMonth == self::getLastDayOfGregorianMonth($this->gregorianMonth, $this->gregorianYear)) {
				$this->gregorianDayOfMonth = 1;
				// if last day of year
				if ($this->gregorianMonth == 12) {
					$this->gregorianYear++;
					$this->gregorianMonth = 1;
				} else {
					$this->gregorianMonth++;
				}
			} else { // if not last day of month
				$this->gregorianDayOfMonth++;
			}
	
			// Change the Jewish Date
			if ($this->jewishDay == self::getDaysInJewishMonth($this->getJewishMonth(), $this->getJewishYear())) {
				// if it last day of elul (i.e. last day of Jewish year)
				if ($this->jewishMonth == self::ELUL) {
					$this->jewishYear++;
					$this->jewishMonth++;
					$this->jewishDay = 1;
				} else if ($this->jewishMonth == self::getLastMonthOfJewishYear($this->jewishYear)) {
					// if it is the last day of Adar, or Adar II as case may be
					$this->jewishMonth = self::NISSAN;
					$this->jewishDay = 1;
				} else {
					$this->jewishMonth++;
					$this->jewishDay = 1;
				}
			} else { // if not last date of month
				$this->jewishDay++;
			}
	
			if ($this->dayOfWeek == 7) {
				$this->dayOfWeek = 1;
			} else {
				$this->dayOfWeek++;
			}
	
			$this->gregorianAbsDate++; // increment the absolute date
		}

		return $this;
	}

	public function addDay() {
		return $this->addDays(1);
	}

	public function addMonthsJewish($amount) {
		if ($amount < 1) {
			throw new \Exception("The amount of months to add has to be greater than zero.");
		}
		for ($i = 0; $i < $amount; $i++) {
			if($this->getJewishMonth() == self::ELUL) {
				$this->setJewishMonth(self::TISHREI);
				$this->setJewishYear($this->getJewishYear() + 1);
			} else if ((! self::isJewishLeapYear($this->getJewishYear()) && $this->getJewishMonth() == self::ADAR)
						|| (self::isJewishLeapYear($this->getJewishYear()) && $this->getJewishMonth() == self::ADAR_II)){
				$this->setJewishMonth(self::NISSAN);
			} else {
				$this->setJewishMonth($this->getJewishMonth() + 1);
			}
		}

		return $this;
	}

	public function addMonthJewish() {
		return $this->addMonthsJewish(1);
	}

	public function addMonthsGregorian($amount) {
		$months = $amount + $this->gregorianMonth;

		$years = (int) ($months / 12) + $this->gregorianYear;
		$months = $months % 12;

		$this->setInternalGregorianDate($years, $months, $this->gregorianDayOfMonth);

		return $this;
	}

	public function addMonthGregorian() {
		return $this->addMonthsGregorian(1);
	}

	public function addYearsJewish($amount) {
		$this->setJewishYear($this->getJewishYear() + $amount);

		return $this;
	}

	public function addYearJewish() {
		return $this->addYearsJewish(1);
	}

	public function addYearsGregorian($amount) {
		$this->setGregorianYear($this->getGregorianYear() + $amount);

		return $this;
	}

	public function addYearGregorian() {
		return $this->addYearsGregorian(1);
	}

	public function subDays($amount) {
		for ($i = 0; $i < $amount; $i++) {
			if ($this->gregorianDayOfMonth == 1) {
				if ($this->gregorianMonth == 1) {
					$this->gregorianMonth = 12;
					$this->gregorianYear--;
				} else {
					$this->gregorianMonth--;
				}
				$this->gregorianDayOfMonth = self::getLastDayOfGregorianMonth($this->gregorianMonth, $this->gregorianYear);
			} else {
				$this->gregorianDayOfMonth--;
			}

			if ($this->jewishDay == 1) {
				if ($this->jewishMonth == self::NISSAN) {
					$this->jewishMonth = self::getLastMonthOfJewishYear($this->jewishYear);
				} else if ($this->jewishMonth == self::TISHREI) {
					$this->jewishYear--;
					$this->jewishMonth--;
				} else {
					$this->jewishMonth--;
				}
				$this->jewishDay = self::getDaysInJewishMonth($this->getJewishMonth(), $this->getJewishYear());
			} else {
				$this->jewishDay--;
			}

			if ($this->dayOfWeek == 1) {
				$this->dayOfWeek = 7;
			} else {
				$this->dayOfWeek--;
			}
			$this->gregorianAbsDate--;
		}

		return $this;
	}

	public function subDay() {
		return $this->subDays(1);
	}

	public function subYearsJewish($amount) {
		return $this->addYearsJewish(-1 * $amount);
	}

	public function subYearJewish() {
		return $this->addYearsJewish(-1);
	}

	/*
	|--------------------------------------------------------------------------
	| COMPARISONS
	|--------------------------------------------------------------------------
	*/

	public function equals($jewishDate) {
		if ($this == $jewishDate) {
			return true;
		}
		if (!($jewishDate instanceof JewishDate)) {
			return false;
		}

		return $this->gregorianAbsDate == $jewishDate->getAbsDate();
	}

	/*
	|--------------------------------------------------------------------------
	| DATE GETTERS
	|--------------------------------------------------------------------------
	*/

	public function getGregorianMonth() {
		return $this->gregorianMonth;
	}

	public function getGregorianDayOfMonth() {
		return $this->gregorianDayOfMonth;
	}

	public function getGregorianYear() {
		return $this->gregorianYear;
	}

	public function getJewishMonth() {
		return $this->jewishMonth;
	}

	public function getJewishDayOfMonth() {
		return $this->jewishDay;
	}

	public function getJewishYear() {
		return $this->jewishYear;
	}

	public function getDayOfWeek() {
		return $this->dayOfWeek;
	}

	/*
	|--------------------------------------------------------------------------
	| DATE SETTERS
	|--------------------------------------------------------------------------
	*/

	public function setGregorianMonth($month) {
		self::validateGregorianMonth($month);
		$this->setInternalGregorianDate($this->gregorianYear, $month + 1, $this->gregorianDayOfMonth);
	}

	public function setGregorianYear($year) {
		self::validateGregorianYear($year);
		$this->setInternalGregorianDate($year, $this->gregorianMonth, $this->gregorianDayOfMonth);
	}

	public function setGregorianDayOfMonth($dayOfMonth) {
		self::validateGregorianDayOfMonth($dayOfMonth);
		$this->setInternalGregorianDate($this->gregorianYear, $this->gregorianMonth, $dayOfMonth);
	}

	public function setJewishMonth($month) {
		$this->setJewishDate($this->jewishYear, $month, $this->jewishDay);
	}

	public function setJewishYear($year) {
		$this->setJewishDate($year, $this->jewishMonth, $this->jewishDay);
	}

	public function setJewishDayOfMonth($dayOfMonth) {
		$this->setJewishDate($this->jewishYear, $this->jewishMonth, $dayOfMonth);
	}

	public function copy() {
		return clone $this;
	}

}