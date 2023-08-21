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

class TefilaRules {
	private static $tachanunRecitedEndOfTishrei = true;
	private static $tachanunRecitedWeekAfterShavuos = false;
	private static $tachanunRecited13SivanOutOfIsrael = true;
	private static $tachanunRecitedPesachSheni = false;
	private static $tachanunRecited15IyarOutOfIsrael = true;
	private static $tachanunRecitedMinchaErevLagBaomer = false;
	private static $tachanunRecitedShivasYemeiHamiluim = true;
	private static $tachanunRecitedWeekOfHod = true;
	private static $tachanunRecitedWeekOfPurim = true;
	private static $tachanunRecitedFridays = true;
	private static $tachanunRecitedSundays = true;
	private static $tachanunRecitedMinchaAllYear = true;
	private static $mizmorLesodaRecitedErevYomKippurAndPesach = true;

	/*
	|--------------------------------------------------------------------------
	| TACHANUN
	|--------------------------------------------------------------------------
	*/

	public static function isTachanunRecitedShacharis(JewishCalendar $jewishCalendar) {
		$holidayIndex = $jewishCalendar->getYomTovIndex();
		$day = $jewishCalendar->getJewishDayOfMonth();
		$month = $jewishCalendar->getJewishMonth();

		if ($jewishCalendar->getDayOfWeek() == 7
				|| (!self::$tachanunRecitedSundays && $jewishCalendar->getDayOfWeek() == 1)
				|| (!self::$tachanunRecitedFridays && $jewishCalendar->getDayOfWeek() == 6)
				|| $month == JewishDate::NISSAN
				|| ($month == JewishDate::TISHREI && ((!self::$tachanunRecitedEndOfTishrei && $day > 8)
				|| (self::$tachanunRecitedEndOfTishrei && ($day > 8 && $day < 22))))
				|| ($month == JewishDate::SIVAN && (self::$tachanunRecitedWeekAfterShavuos && $day < 7
						|| !self::$tachanunRecitedWeekAfterShavuos && $day < (!$jewishCalendar->getInIsrael()
								&& !self::$tachanunRecited13SivanOutOfIsrael ? 14: 13)))
				|| ($jewishCalendar->isYomTov($holidayIndex) && (! $jewishCalendar->isTaanis($holidayIndex)
						|| (!self::$tachanunRecitedPesachSheni && $holidayIndex == JewishCalendar::PESACH_SHENI))) // Erev YT is included in isYomTov()
				|| (!$jewishCalendar->getInIsrael() && !self::$tachanunRecitedPesachSheni && !self::$tachanunRecited15IyarOutOfIsrael
						&& $jewishCalendar->getJewishMonth() == JewishDate::IYAR && $day == 15)
				|| $holidayIndex == JewishCalendar::TISHA_BEAV || $jewishCalendar->isIsruChag($holidayIndex)
				|| $jewishCalendar->isRoshChodesh()
				|| (!self::$tachanunRecitedShivasYemeiHamiluim &&
						((!$jewishCalendar->isJewishLeapYear() && $month == JewishDate::ADAR)
								|| ($jewishCalendar->isJewishLeapYear() && $month == JewishDate::ADAR_II)) && $day > 22)
				|| (!self::$tachanunRecitedWeekOfPurim &&
						((!$jewishCalendar->isJewishLeapYear() && $month == JewishDate::ADAR)
								|| ($jewishCalendar->isJewishLeapYear() && $month == JewishDate::ADAR_II)) && $day > 10 && $day < 18)
				|| ($jewishCalendar->isUseModernHolidays()
						&& ($holidayIndex == JewishCalendar::YOM_HAATZMAUT || $holidayIndex == JewishCalendar::YOM_YERUSHALAYIM))
				|| (!self::$tachanunRecitedWeekOfHod && $month == JewishDate::IYAR && $day > 13 && $day < 21)) {
			return false;
		}
		return true;
	}

	public static function isTachanunRecitedMincha(JewishCalendar $jewishCalendar) {
		$tomorrow = $jewishCalendar->copy()->addDays(1);
		$tomorrowHolidayIndex = $jewishCalendar->getYomTovIndex();
		
		if (!self::$tachanunRecitedMinchaAllYear
					|| $jewishCalendar->getDayOfWeek() == 6
					|| ! self::isTachanunRecitedShacharis($jewishCalendar) 
					|| (! self::isTachanunRecitedShacharis($tomorrow) && 
							!($tomorrowHolidayIndex == JewishCalendar::EREV_ROSH_HASHANA) &&
							!($tomorrowHolidayIndex == JewishCalendar::EREV_YOM_KIPPUR) &&
							!($tomorrowHolidayIndex == JewishCalendar::PESACH_SHENI))
					|| ! self::$tachanunRecitedMinchaErevLagBaomer && $tomorrowHolidayIndex == JewishCalendar::LAG_BAOMER) {
			return false;
		}
		return true;
	}

	/*
	|--------------------------------------------------------------------------
	| VESEIN TAL UMATAR/BRACHA
	|--------------------------------------------------------------------------
	*/

	public static function isVeseinTalUmatarStartDate(JewishCalendar $jewishCalendar) {
		if ($jewishCalendar->getInIsrael()) {
			 // The 7th Cheshvan can't occur on Shabbos, so always return true for 7 Cheshvan
			if ($jewishCalendar->getJewishMonth() == JewishDate::CHESHVAN && $jewishCalendar->getJewishDayOfMonth() == 7) {
				return true;
			}
		} else {
			if ($jewishCalendar->getDayOfWeek() == 7) { //Not recited on Friday night
				return false;
			}
			if($jewishCalendar->getDayOfWeek() == 1) { // When starting on Sunday, it can be the start date or delayed from Shabbos
				return $jewishCalendar->getTekufasTishreiElapsedDays() == 48 || $jewishCalendar->getTekufasTishreiElapsedDays() == 47;
			} else {
				return $jewishCalendar->getTekufasTishreiElapsedDays() == 47;
			}
		}
		return false; // keep the compiler happy
	}

	public static function isVeseinTalUmatarStartingTonight(JewishCalendar $jewishCalendar) {
		if ($jewishCalendar->getInIsrael()) {
			// The 7th Cheshvan can't occur on Shabbos, so always return true for 6 Cheshvan
			if ($jewishCalendar->getJewishMonth() == JewishDate::CHESHVAN && $jewishCalendar->getJewishDayOfMonth() == 6) {
					return true;
			}
		} else {
			if ($jewishCalendar->getDayOfWeek() == 6) { //Not recited on Friday night
				return false;
			}
			if($jewishCalendar->getDayOfWeek() == 7) { // When starting on motzai Shabbos, it can be the start date or delayed from Friday night
				return $jewishCalendar->getTekufasTishreiElapsedDays() == 47 || $jewishCalendar->getTekufasTishreiElapsedDays() == 46;
			} else {
				return $jewishCalendar->getTekufasTishreiElapsedDays() == 46;
			}
		}
		return false;
	}

	public static function isVeseinTalUmatarRecited(JewishCalendar $jewishCalendar) {
		if ($jewishCalendar->getJewishMonth() == JewishDate::NISSAN && $jewishCalendar->getJewishDayOfMonth() < 15) {
			return true;
		}
		if ($jewishCalendar->getJewishMonth() < JewishDate::CHESHVAN) {
			return false;
		}
		if ($jewishCalendar->getInIsrael()) {
			return $jewishCalendar->getJewishMonth() != JewishDate::CHESHVAN || $jewishCalendar->getJewishDayOfMonth() >= 7;
		} else {
			return $jewishCalendar->getTekufasTishreiElapsedDays() >= 47;
		}
	}

	public static function isVeseinBerachaRecited(JewishCalendar $jewishCalendar) {
		return !self::isVeseinTalUmatarRecited($jewishCalendar);
	}

	/*
	|--------------------------------------------------------------------------
	| MASHIV HARUACH/MORID HATAL
	|--------------------------------------------------------------------------
	*/

	public static function isMashivHaruachStartDate(JewishCalendar $jewishCalendar) {
		return $jewishCalendar->getJewishMonth() == JewishDate::TISHREI && $jewishCalendar->getJewishDayOfMonth() == 22;
	}

	public static function isMashivHaruachEndDate(JewishCalendar $jewishCalendar) {
		return $jewishCalendar->getJewishMonth() == JewishDate::NISSAN && $jewishCalendar->getJewishDayOfMonth() == 15;
	}

	public static function isMashivHaruachRecited(JewishCalendar $jewishCalendar) {
		$startDate = new JewishDate($jewishCalendar->getJewishYear(), JewishDate::TISHREI, 22);
		$endDate = new JewishDate($jewishCalendar->getJewishYear(), JewishDate::NISSAN, 15);
		return $jewishCalendar->compareTo($startDate) > 0 && $jewishCalendar->compareTo($endDate) < 0;
	}

	public static function isMoridHatalRecited(JewishCalendar $jewishCalendar) {
		return !self::isMashivHaruachRecited($jewishCalendar) || self::isMashivHaruachStartDate($jewishCalendar) || self::isMashivHaruachEndDate($jewishCalendar);
	}

	/*
	|--------------------------------------------------------------------------
	| HALLEL
	|--------------------------------------------------------------------------
	*/

	public static function isHallelRecited(JewishCalendar $jewishCalendar) {
		$day = $jewishCalendar->getJewishDayOfMonth();
		$month = $jewishCalendar->getJewishMonth();
		$holidayIndex = $jewishCalendar->getYomTovIndex();
		$inIsrael = $jewishCalendar->getInIsrael();
		
		if($jewishCalendar->isRoshChodesh()) { //RH returns false for RC
			return true;
		}
		if($jewishCalendar->isChanukah($holidayIndex)) {
			return true;
		}
		switch ($month) {
			case JewishDate::NISSAN:
				if($day >= 15 && (($inIsrael && day <= 21) || (!$inIsrael && $day <= 22))){
					return true;
				}
				break;
			case JewishDate::IYAR: // modern holidays
				if($jewishCalendar->isUseModernHolidays()  && ($holidayIndex == JewishCalendar::YOM_HAATZMAUT
						|| $holidayIndex == JewishCalendar::YOM_YERUSHALAYIM)){
					return true;
				}
				break;
			case JewishDate::SIVAN:
				if ($day == 6 || (!$inIsrael && ($day == 7))){
					return true;
				}
				break;
			case JewishDate::TISHREI:
				if ($day >= 15 && ($day <= 22 || (!$inIsrael && ($day <= 23)))){
					return true;
				}
		}
		return false;
	}

	public static function isHallelShalemRecited(JewishCalendar $jewishCalendar) {
		$day = $jewishCalendar->getJewishDayOfMonth();
		$month = $jewishCalendar->getJewishMonth();
		$inIsrael = $jewishCalendar->getInIsrael();
		if(self::isHallelRecited($jewishCalendar)) {
			if(($jewishCalendar->isRoshChodesh() && ! $jewishCalendar->isChanukah())
					|| ($month == JewishDate::NISSAN && (($inIsrael && $day > 15) || (!$inIsrael && $day > 16)))) {
				return false;
			} else {
				return true;
			}
		} 
		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| AL HANISIM
	|--------------------------------------------------------------------------
	*/

	public static function isAlHanissimRecited(JewishCalendar $jewishCalendar) {
	    return $jewishCalendar->isPurim() || $jewishCalendar->isChanukah();
	}

	/*
	|--------------------------------------------------------------------------
	| YAALEH VEYAVO
	|--------------------------------------------------------------------------
	*/

	public static function isYaalehVeyavoRecited(JewishCalendar $jewishCalendar) {
		$holidayIndex = $holidayIndex ?? $jewishCalendar->getYomTovIndex();
	    return $jewishCalendar->isPesach($holidayIndex) || $jewishCalendar->isShavuos($holidayIndex) ||$jewishCalendar->isRoshHashana($holidayIndex) || $jewishCalendar->isYomKippur($holidayIndex)
	    		|| $jewishCalendar->isSuccos($holidayIndex) || $jewishCalendar->isShminiAtzeres($holidayIndex) || $jewishCalendar->isSimchasTorah($holidayIndex)
	    		|| $jewishCalendar->isRoshChodesh();
	}

	/*
	|--------------------------------------------------------------------------
	| EXTRA STUFF
	|--------------------------------------------------------------------------
	*/

	public static function isMizmorLesodaRecited(JewishCalendar $jewishCalendar) {
		if($jewishCalendar->isAssurBemelacha()) {
			return false;
		}

		$holidayIndex = $jewishCalendar->getYomTovIndex();
		if(!self::isMizmorLesodaRecitedErevYomKippurAndPesach()
				&& ($holidayIndex == JewishCalendar::EREV_YOM_KIPPUR
						|| $holidayIndex == JewishCalendar::EREV_PESACH
						|| $jewishCalendar->isCholHamoedPesach())) {
			return false;
		}
	    return true;
	}

	/*
	|--------------------------------------------------------------------------
	| SETTERS AND GETTERS
	|--------------------------------------------------------------------------
	*/

	public static function isTachanunRecitedWeekOfPurim() {
		return self::$tachanunRecitedWeekOfPurim;
	}

	public static function setTachanunRecitedWeekOfPurim($tachanunRecitedWeekOfPurim) {
		self::$tachanunRecitedWeekOfPurim = $tachanunRecitedWeekOfPurim;
	}

	public static function isTachanunRecitedWeekOfHod() {
		return self::$tachanunRecitedWeekOfHod;
	}

	public static function setTachanunRecitedWeekOfHod($tachanunRecitedWeekOfHod) {
		self::$tachanunRecitedWeekOfHod = $tachanunRecitedWeekOfHod;
	}

	public static function isTachanunRecitedEndOfTishrei() {
		return self::$tachanunRecitedEndOfTishrei;
	}

	public static function setTachanunRecitedEndOfTishrei($tachanunRecitedEndOfTishrei) {
		self::$tachanunRecitedEndOfTishrei = $tachanunRecitedEndOfTishrei;
	}

	public static function isTachanunRecitedWeekAfterShavuos() {
		return self::$tachanunRecitedWeekAfterShavuos;
	}

	public static function setTachanunRecitedWeekAfterShavuos($tachanunRecitedWeekAfterShavuos) {
		self::$tachanunRecitedWeekAfterShavuos = $tachanunRecitedWeekAfterShavuos;
	}

	public static function isTachanunRecited13SivanOutOfIsrael() {
		return self::$tachanunRecited13SivanOutOfIsrael;
	}

	public static function setTachanunRecited13SivanOutOfIsrael($tachanunRecitedThirteenSivanOutOfIsrael) {
		self::$tachanunRecited13SivanOutOfIsrael = $tachanunRecitedThirteenSivanOutOfIsrael;
	}

	public static function isTachanunRecitedPesachSheni() {
		return self::$tachanunRecitedPesachSheni;
	}

	public static function setTachanunRecitedPesachSheni($tachanunRecitedPesachSheni) {
		self::$tachanunRecitedPesachSheni = $tachanunRecitedPesachSheni;
	}

	public static function isTachanunRecited15IyarOutOfIsrael() {
		return self::$tachanunRecited15IyarOutOfIsrael;
	}

	public static function setTachanunRecited15IyarOutOfIsrael($tachanunRecited15IyarOutOfIsrael) {
		self::$tachanunRecited15IyarOutOfIsrael = $tachanunRecited15IyarOutOfIsrael;
	}

	public static function isTachanunRecitedMinchaErevLagBaomer() {
		return self::$tachanunRecitedMinchaErevLagBaomer;
	}

	public static function setTachanunRecitedMinchaErevLagBaomer($tachanunRecitedMinchaErevLagBaomer) {
		self::$tachanunRecitedMinchaErevLagBaomer = $tachanunRecitedMinchaErevLagBaomer;
	}

	public static function isTachanunRecitedShivasYemeiHamiluim() {
		return self::$tachanunRecitedShivasYemeiHamiluim;
	}

	public static function setTachanunRecitedShivasYemeiHamiluim($tachanunRecitedShivasYemeiHamiluim) {
		self::$tachanunRecitedShivasYemeiHamiluim = $tachanunRecitedShivasYemeiHamiluim;
	}

	public static function isTachanunRecitedFridays() {
		return self::$tachanunRecitedFridays;
	}

	public static function setTachanunRecitedFridays($tachanunRecitedFridays) {
		self::$tachanunRecitedFridays = $tachanunRecitedFridays;
	}

	public static function isTachanunRecitedSundays() {
		return self::$tachanunRecitedSundays;
	}

	public static function setTachanunRecitedSundays($tachanunRecitedSundays) {
		self::$tachanunRecitedSundays = $tachanunRecitedSundays;
	}

	public static function isTachanunRecitedMinchaAllYear() {
		return self::$tachanunRecitedMinchaAllYear;
	}

	public static function setTachanunRecitedMinchaAllYear($tachanunRecitedMinchaAllYear) {
		self::$tachanunRecitedMinchaAllYear = $tachanunRecitedMinchaAllYear;
	}

	public static function setMizmorLesodaRecitedErevYomKippurAndPesach($mizmorLesodaRecitedErevYomKippurAndPesach) {
		self::$mizmorLesodaRecitedErevYomKippurAndPesach = $mizmorLesodaRecitedErevYomKippurAndPesach;
	}

	public static function isMizmorLesodaRecitedErevYomKippurAndPesach() {
		return self::$mizmorLesodaRecitedErevYomKippurAndPesach;
	}
}
