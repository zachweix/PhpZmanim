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
use PhpZmanim\Geo\GeoLocation;

class JewishCalendar extends JewishDate {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/
	private $inIsrael = false;
	private $isMukafChoma = false;
	private $useModernHolidays = false;

	const EREV_PESACH = 0;
	const PESACH = 1;
	const CHOL_HAMOED_PESACH = 2;
	const PESACH_SHENI = 3;
	const EREV_SHAVUOS = 4;
	const SHAVUOS = 5;
	const SEVENTEEN_OF_TAMMUZ = 6;
	const TISHA_BEAV = 7;
	const TU_BEAV = 8;
	const EREV_ROSH_HASHANA = 9;
	const ROSH_HASHANA = 10;
	const FAST_OF_GEDALYAH = 11;
	const EREV_YOM_KIPPUR = 12;
	const YOM_KIPPUR = 13;
	const EREV_SUCCOS = 14;
	const SUCCOS = 15;
	const CHOL_HAMOED_SUCCOS = 16;
	const HOSHANA_RABBA = 17;
	const SHEMINI_ATZERES = 18;
	const SIMCHAS_TORAH = 19;
	const CHANUKAH = 21;
	const TENTH_OF_TEVES = 22;
	const TU_BESHVAT = 23;
	const FAST_OF_ESTHER = 24;
	const PURIM = 25;
	const SHUSHAN_PURIM = 26;
	const PURIM_KATAN = 27;
	const ROSH_CHODESH = 28;
	const YOM_HASHOAH = 29;
	const YOM_HAZIKARON = 30;
	const YOM_HAATZMAUT = 31;
	const YOM_YERUSHALAYIM = 32;
	const LAG_BAOMER = 33;
	const SHUSHAN_PURIM_KATAN = 34;
	const ISRU_CHAG = 35;
	const YOM_KIPPUR_KATAN = 36;
	const BEHAB = 37;

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTOR
	|--------------------------------------------------------------------------
	*/

	public function __construct($jewishYear = null, $jewishMonth = null, $jewishDayOfMonth = null, $inIsrael = false) {
		parent::__construct($jewishYear, $jewishMonth, $jewishDayOfMonth);
		$this->setInIsrael($inIsrael);
	}

	/*
	|--------------------------------------------------------------------------
	| SETTERS AND GETTERS
	|--------------------------------------------------------------------------
	*/

	public function isUseModernHolidays() {
		return $this->useModernHolidays;
	}

	public function setUseModernHolidays($useModernHolidays) {
		$this->useModernHolidays = $useModernHolidays;
	}

	public function setInIsrael($inIsrael) {
		$this->inIsrael = $inIsrael;
	}

	public function getInIsrael() {
		return $this->inIsrael;
	}

	public function getIsMukafChoma() {
		return $isMukafChoma;
	}

	public function setIsMukafChoma($isMukafChoma) {
		$this->isMukafChoma = $isMukafChoma;
	}

	/*
	|--------------------------------------------------------------------------
	| CALENDAR RULES AND HOLIDAYS
	|--------------------------------------------------------------------------
	*/

	public function isBirkasHachamah() {
		$elapsedDays = $this->getJewishCalendarElapsedDays($this->getJewishYear());
		$elapsedDays += self::getDaysSinceStartOfJewishYear($this->getJewishYear(), $this->getJewishMonth(), $this->getJewishDayOfMonth());

		return ($elapsedDays % (28 * 365.25) == 172);
	}

	private function getParshaYearType() {
		$roshHashanaDayOfWeek = ($this->getJewishCalendarElapsedDays($this->getJewishYear()) + 1) % 7;
		if ($roshHashanaDayOfWeek == 0) {
			$roshHashanaDayOfWeek = 7;
		}
		if (self::isJewishLeapYear($this->getJewishYear())) {
			switch ($roshHashanaDayOfWeek) {
				case 2:
					if (self::isKislevShort($this->getJewishYear())) { //BaCh
						if ($this->getInIsrael()) {
							return 14;
						}
						return 6;
					}
					if (self::isCheshvanLong($this->getJewishYear())) { //BaSh
						if ($this->getInIsrael()) {
							return 15;
						}
						return 7;
					}
					break;
				case 3: //Gak
					if ($this->getInIsrael()) {
						return 15;
					}
					return 7;
				case 5:
					if (self::isKislevShort($this->getJewishYear())) { //HaCh
						return 8;
					}
					if (self::isCheshvanLong($this->getJewishYear())) { //HaSh
						return 9;
					}
					break;
				case 7:
					if (self::isKislevShort($this->getJewishYear())) { //ZaCh
						return 10;
					}
					if (self::isCheshvanLong($this->getJewishYear())) { //ZaSh
						if ($this->getInIsrael()) {
							return 16;
						}
						return 11;
					}
					break;
			}
		} else {
			switch ($roshHashanaDayOfWeek) {
				case 2:
					if (self::isKislevShort($this->getJewishYear())) { //BaCh
						return 0;
					}
					if (self::isCheshvanLong($this->getJewishYear())) { //BaSh
						if ($this->getInIsrael()) {
							return 12;
						}
						return 1;
					}
					break;
				case 3: //GaK
					if ($this->getInIsrael()) {
						return 12;
					}
					return 1;
				case 5:
					if (self::isCheshvanLong($this->getJewishYear())) { //HaSh
						return 3;
					}
					if (!self::isKislevShort($this->getJewishYear())) { //Hak
						if ($this->getInIsrael()) {
							return 13;
						}
						return 2;
					}
					break;
				case 7:
					if (self::isKislevShort($this->getJewishYear())) { //ZaCh
						return 4;
					}
					if (self::isCheshvanLong($this->getJewishYear())) { //ZaSh
						return 5;
					}
					break;
			}
		}
		return -1;
	}

	public function getParshah() {
		if ($this->getDayOfWeek() != 7) {
			return Parsha::NONE;
		}
		
		$yearType = $this->getParshaYearType();
		$roshHashanaDayOfWeek = self::getJewishCalendarElapsedDays($this->getJewishYear()) % 7;
		$day = $roshHashanaDayOfWeek + self::getDaysSinceStartOfJewishYear($this->getJewishYear(), $this->getJewishMonth(), $this->getJewishDayOfMonth());
		
		if ($yearType >= 0) {
			$week = (int) ($day / 7);
			return Parsha::PARSHA_LIST[$yearType][$week];
		}
		return Parsha::NONE;
	}

	public function getUpcomingParshah() {
		$clone = $this->clone();
		$daysToShabbos = 7 - ($this->getDayOfWeek() % 7);

		$clone->addDays($daysToShabbos);
		while($clone->getParshah() == Parsha::NONE) {
			$clone->addDays(7);
		}

		return $clone->getParshah();
	}

	public function getSpecialShabbos() {
		if ($this->getDayOfWeek() != 7) {
			return Parsha::NONE;
		}

		if (($this->getJewishMonth() == JewishDate::SHEVAT && !self::isJewishLeapYear($this->getJewishYear())) || ($this->getJewishMonth() == JewishDate::ADAR && self::isJewishLeapYear($this->getJewishYear()))) {
			if ($this->getJewishDayOfMonth() == 25 || $this->getJewishDayOfMonth() == 27 || $this->getJewishDayOfMonth() == 29) {
				return Parsha::SHKALIM;
			}
		}

		if (($this->getJewishMonth() == JewishDate::ADAR && !self::isJewishLeapYear($this->getJewishYear())) || $this->getJewishMonth() == JewishDate::ADAR_II) {
			if ($this->getJewishDayOfMonth() == 1) {
				return Parsha::SHKALIM;
			}
			if ($this->getJewishDayOfMonth() == 8 || $this->getJewishDayOfMonth() == 9 || $this->getJewishDayOfMonth() == 11 || $this->getJewishDayOfMonth() == 13) {
				return Parsha::ZACHOR;
			}
			if ($this->getJewishDayOfMonth() == 18 || $this->getJewishDayOfMonth() == 20 || $this->getJewishDayOfMonth() == 22 || $this->getJewishDayOfMonth() == 23) {
				return Parsha::PARA;
			}
			if ($this->getJewishDayOfMonth() == 25 || $this->getJewishDayOfMonth() == 27 || $this->getJewishDayOfMonth() == 29) {
				return Parsha::HACHODESH;
			}
		}

		if ($this->getJewishMonth() == JewishDate::NISSAN) {
			if($this->getJewishDayOfMonth() == 1) {
				return Parsha::HACHODESH;
			}
			if($this->getJewishDayOfMonth() >= 8 && $this->getJewishDayOfMonth() <= 14) {
				return Parsha::HAGADOL;
			}
		}

		if ($this->getJewishMonth() == JewishDate::AV) {
			if($this->getJewishDayOfMonth() >= 4 && $this->getJewishDayOfMonth() <= 9) {
				return Parsha::CHAZON;
			}
			if($this->getJewishDayOfMonth() >= 10 && $this->getJewishDayOfMonth() <= 16) {
				return Parsha::NACHAMU;
			}
		}

		if ($this->getJewishMonth() == JewishDate::TISHREI) {
			if($this->getJewishDayOfMonth() >= 3 && $this->getJewishDayOfMonth() <= 8) {
				return Parsha::SHUVA;
			}
			
		}

		if($this->getParshah() == Parsha::BESHALACH) {
			return Parsha::SHIRA;
		}

		return Parsha::NONE;
	}

	public function getYomTovIndex() {
		$day = $this->getJewishDayOfMonth();
		$dayOfWeek = $this->getDayOfWeek();

		switch ($this->getJewishMonth()) {
			case JewishDate::NISSAN:
				if ($day == 14) {
					return JewishCalendar::EREV_PESACH;
				}
				if ($day == 15 || $day == 21
						|| (!$this->inIsrael && ($day == 16 || $day == 22))) {
					return JewishCalendar::PESACH;
				}
				if ($day >= 17 && $day <= 20
						|| ($day == 16 && $this->inIsrael)) {
					return JewishCalendar::CHOL_HAMOED_PESACH;
				}
				if (($day == 22 && $this->inIsrael) || ($day == 23 && !$this->inIsrael)) {
					return JewishCalendar::ISRU_CHAG;
				}
				if ($this->isUseModernHolidays()
						&& (($day == 26 && $dayOfWeek == 5)
								|| ($day == 28 && $dayOfWeek == 2)
								|| ($day == 27 && $dayOfWeek != 1 && $dayOfWeek != 6))) {
					return JewishCalendar::YOM_HASHOAH;
				}
				break;
			case JewishDate::IYAR:
				if ($this->isUseModernHolidays()
						&& (($day == 4 && $dayOfWeek == 3)
								|| (($day == 3 || $day == 2) && $dayOfWeek == 4) || ($day == 5 && $dayOfWeek == 2))) {
					return JewishCalendar::YOM_HAZIKARON;
				}
				if ($this->isUseModernHolidays()
						&& (($day == 5 && $dayOfWeek == 4)
								|| (($day == 4 || $day == 3) && $dayOfWeek == 5) || ($day == 6 && $dayOfWeek == 3))) {
					return JewishCalendar::YOM_HAATZMAUT;
				}
				if ($day == 14) {
					return JewishCalendar::PESACH_SHENI;
				}
				if ($day == 18) {
					return JewishCalendar::LAG_BAOMER;
				}
				if ($this->isUseModernHolidays() && $day == 28) {
					return JewishCalendar::YOM_YERUSHALAYIM;
				}
				break;
			case JewishDate::SIVAN:
				if ($day == 5) {
					return JewishCalendar::EREV_SHAVUOS;
				}
				if ($day == 6 || ($day == 7 && !$this->inIsrael)) {
					return JewishCalendar::SHAVUOS;
				}
				if (($day == 7 && $this->inIsrael) || ($day == 8 && !$this->inIsrael)) {
					return JewishCalendar::ISRU_CHAG;
				}
				break;
			case JewishDate::TAMMUZ:
				if (($day == 17 && $dayOfWeek != 7)
						|| ($day == 18 && $dayOfWeek == 1)) {
					return JewishCalendar::SEVENTEEN_OF_TAMMUZ;
				}
				break;
			case JewishDate::AV:
				if (($dayOfWeek == 1 && $day == 10)
						|| ($dayOfWeek != 7 && $day == 9)) {
					return JewishCalendar::TISHA_BEAV;
				}
				if ($day == 15) {
					return JewishCalendar::TU_BEAV;
				}
				break;
			case JewishDate::ELUL:
				if ($day == 29) {
					return JewishCalendar::EREV_ROSH_HASHANA;
				}
				break;
			case JewishDate::TISHREI:
				if ($day == 1 || $day == 2) {
					return JewishCalendar::ROSH_HASHANA;
				}
				if (($day == 3 && $dayOfWeek != 7) || ($day == 4 && $dayOfWeek == 1)) {
					return JewishCalendar::FAST_OF_GEDALYAH;
				}
				if ($day == 9) {
					return JewishCalendar::EREV_YOM_KIPPUR;
				}
				if ($day == 10) {
					return JewishCalendar::YOM_KIPPUR;
				}
				if ($day == 14) {
					return JewishCalendar::EREV_SUCCOS;
				}
				if ($day == 15 || ($day == 16 && !$this->inIsrael)) {
					return JewishCalendar::SUCCOS;
				}
				if ($day >= 17 && $day <= 20 || ($day == 16 && $this->inIsrael)) {
					return JewishCalendar::CHOL_HAMOED_SUCCOS;
				}
				if ($day == 21) {
					return JewishCalendar::HOSHANA_RABBA;
				}
				if ($day == 22) {
					return JewishCalendar::SHEMINI_ATZERES;
				}
				if ($day == 23 && !$this->inIsrael) {
					return JewishCalendar::SIMCHAS_TORAH;
				}
				if (($day == 23 && $this->inIsrael) || ($day == 24 && !$this->inIsrael)) {
					return JewishCalendar::ISRU_CHAG;
				}
				break;
			case JewishDate::KISLEV:
				if ($day >= 25) {
					return JewishCalendar::CHANUKAH;
				}
				break;
			case JewishDate::TEVES:
				if ($day == 1 || $day == 2
						|| ($day == 3 && self::isKislevShort($this->getJewishYear()))) {
					return JewishCalendar::CHANUKAH;
				}
				if ($day == 10) {
					return JewishCalendar::TENTH_OF_TEVES;
				}
				break;
			case JewishDate::SHEVAT:
				if ($day == 15) {
					return JewishCalendar::TU_BESHVAT;
				}
				break;
			case JewishDate::ADAR:
				if (!self::isJewishLeapYear($this->getJewishYear())) {
					if ((($day == 11 || $day == 12) && $dayOfWeek == 5)
							|| ($day == 13 && !($dayOfWeek == 6 || $dayOfWeek == 7))) {
						return JewishCalendar::FAST_OF_ESTHER;
					}
					if ($day == 14) {
						return JewishCalendar::PURIM;
					}
					if ($day == 15) {
						return JewishCalendar::SHUSHAN_PURIM;
					}
				} else {
					if ($day == 14) {
						return JewishCalendar::PURIM_KATAN;
					}
					if ($day == 15) {
						return JewishCalendar::SHUSHAN_PURIM_KATAN;
					}
				}
				break;
			case JewishDate::ADAR_II:
				if ((($day == 11 || $day == 12) && $dayOfWeek == 5)
						|| ($day == 13 && !($dayOfWeek == 6 || $dayOfWeek == 7))) {
					return JewishCalendar::FAST_OF_ESTHER;
				}
				if ($day == 14) {
					return JewishCalendar::PURIM;
				}
				if ($day == 15) {
					return JewishCalendar::SHUSHAN_PURIM;
				}
				break;
		}

		return -1;
	}

	public function isYomTov($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		if (($this->isErevYomTov($holidayIndex) && $holidayIndex != JewishCalendar::HOSHANA_RABBA && $holidayIndex != JewishCalendar::CHOL_HAMOED_PESACH)
				|| ($this->isTaanis($holidayIndex) && $holidayIndex != JewishCalendar::YOM_KIPPUR) || $holidayIndex == JewishCalendar::ISRU_CHAG) {
			return false;
		}
		return $holidayIndex != -1;
	}

	public function isYomTovAssurBemelacha($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::PESACH || $holidayIndex == JewishCalendar::SHAVUOS || $holidayIndex == JewishCalendar::SUCCOS || $holidayIndex == JewishCalendar::SHEMINI_ATZERES ||
				$holidayIndex == JewishCalendar::SIMCHAS_TORAH || $holidayIndex == JewishCalendar::ROSH_HASHANA  || $holidayIndex == JewishCalendar::YOM_KIPPUR;
	}

	public function isAssurBemelacha($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $this->getDayOfWeek() == 7 || $this->isYomTovAssurBemelacha($holidayIndex);
	}

	public function hasCandleLighting($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $this->isTomorrowShabbosOrYomTov($holidayIndex);
	}

	public function isTomorrowShabbosOrYomTov($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $this->getDayOfWeek() == 6 || $this->isErevYomTov($holidayIndex) || $this->isErevYomTovSheni();
	}

	public function isErevYomTovSheni() {
		return ($this->getJewishMonth() == JewishCalendar::TISHREI && ($this->getJewishDayOfMonth() == 1))
		|| (! $this->getInIsrael()
				&& (($this->getJewishMonth() == JewishCalendar::NISSAN && ($this->getJewishDayOfMonth() == 15 || $this->getJewishDayOfMonth() == 21))
				|| ($this->getJewishMonth() == JewishCalendar::TISHREI && ($this->getJewishDayOfMonth() == 15 || $this->getJewishDayOfMonth() == 22))
				|| ($this->getJewishMonth() == JewishCalendar::SIVAN && $this->getJewishDayOfMonth() == 6 )));
	}

	public function isAseresYemeiTeshuva() {
		return $this->getJewishMonth() == JewishCalendar::TISHREI && $this->getJewishDayOfMonth() <= 10;
	}

	public function isPesach($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::PESACH || $holidayIndex == JewishCalendar::CHOL_HAMOED_PESACH;
	}

	public function isCholHamoedPesach($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::CHOL_HAMOED_PESACH;
	}

	public function isShavuos($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::SHAVUOS;
	}

	public function isRoshHashana($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::ROSH_HASHANA;
	}

	public function isYomKippur($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::YOM_KIPPUR;
	}

	public function isSuccos($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::SUCCOS || $holidayIndex == JewishCalendar::CHOL_HAMOED_SUCCOS || $holidayIndex == JewishCalendar::HOSHANA_RABBA;
	}

	public function isHoshanaRabba($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::HOSHANA_RABBA;
	}

	public function isShminiAtzeres($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::SHEMINI_ATZERES;
	}

	public function isSimchasTorah($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::SIMCHAS_TORAH;
	}

	public function isCholHamoedSuccos($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::CHOL_HAMOED_SUCCOS || $holidayIndex == JewishCalendar::HOSHANA_RABBA;
	}

	public function isCholHamoed($holidayIndex = null) {
		return $this->isCholHamoedPesach() || $this->isCholHamoedSuccos();
	}

	public function isErevYomTov($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::EREV_PESACH || $holidayIndex == JewishCalendar::EREV_SHAVUOS || $holidayIndex == JewishCalendar::EREV_ROSH_HASHANA
				|| $holidayIndex == JewishCalendar::EREV_YOM_KIPPUR || $holidayIndex == JewishCalendar::EREV_SUCCOS || $holidayIndex == JewishCalendar::HOSHANA_RABBA
				|| ($holidayIndex == JewishCalendar::CHOL_HAMOED_PESACH && $this->getJewishDayOfMonth() == 20);
	}

	public function isErevRoshChodesh() {
		return ($this->getJewishDayOfMonth() == 29 && $this->getJewishMonth() != JewishDate::ELUL);
	}

	public function isYomKippurKatan() {
		$dayOfWeek = $this->getDayOfWeek();
		$month = $this->getJewishMonth();
		$day = $this->getJewishDayOfMonth();
		if($month == JewishDate::ELUL || $month == JewishDate::TISHREI || $month == JewishDate::KISLEV || $month == JewishDate::NISSAN) {
			return false;
		}

		if($day == 29 && $dayOfWeek != 6 && $dayOfWeek != 7) {
			return true;
		}
		
		if(($day == 27 || $day == 28) && $dayOfWeek == 5 ) {
			return true;
		}
		return false;
	}

	public function isBeHaB() {
		$dayOfWeek = $this->getDayOfWeek();
		$month = $this->getJewishMonth();
		$day = $this->getJewishDayOfMonth();
		
		if ($month == JewishDate::CHESHVAN || $month == JewishDate::IYAR) {
			if(($dayOfWeek == 2 && $day > 4 && $day < 18)
					|| ($dayOfWeek == 5 && $day > 7 && $day < 14)) {
				return true;
			}
		}
		return false;
	}

	public function isTaanis($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::SEVENTEEN_OF_TAMMUZ || $holidayIndex == JewishCalendar::TISHA_BEAV || $holidayIndex == JewishCalendar::YOM_KIPPUR
				|| $holidayIndex == JewishCalendar::FAST_OF_GEDALYAH || $holidayIndex == JewishCalendar::TENTH_OF_TEVES || $holidayIndex == JewishCalendar::FAST_OF_ESTHER;
	}

	public function isTaanisBechoros() {
	    $day = $this->getJewishDayOfMonth();
	    $dayOfWeek = $this->getDayOfWeek();

	    return $this->getJewishMonth() == JewishDate::NISSAN && (($day == 14 && $dayOfWeek != 7) ||
	    		($day == 12 && $dayOfWeek == 5 ));
	}

	public function getDayOfChanukah() {
		$day = $this->getJewishDayOfMonth();
		if ($this->isChanukah()) {
			if ($this->getJewishMonth() == JewishDate::KISLEV) {
				return $day - 24;
			} else {
				return self::isKislevShort($this->getJewishYear()) ? $day + 5 : $day + 6;
			}
		} else {
			return -1;
		}
	}

	public function isChanukah($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::CHANUKAH;
	}

	public function isPurim($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		if($this->isMukafChoma) {
			return $holidayIndex == JewishCalendar::SHUSHAN_PURIM;
		} else {
			return $holidayIndex == JewishCalendar::PURIM;
		}
	}

	public function isRoshChodesh() {
		return ($this->getJewishDayOfMonth() == 1 && $this->getJewishMonth() != JewishDate::TISHREI) || $this->getJewishDayOfMonth() == 30;
	}

	public function isMacharChodesh() {
		return ($this->getDayOfWeek() == 7 && ($this->getJewishDayOfMonth() == 30 || $this->getJewishDayOfMonth() == 29));
	}

	public function isShabbosMevorchim() {
		return ($this->getDayOfWeek() == 7 && $this->getJewishDayOfMonth() >= 23 && $this->getJewishDayOfMonth() <= 29 && $this->getJewishMonth() != JewishDate::ELUL);
	}

	public function getDayOfOmer() {
		$omer = -1;
		$month = $this->getJewishMonth();
		$day = $this->getJewishDayOfMonth();

		if ($month == JewishDate::NISSAN && $day >= 16) {
			$omer = $day - 15;
		} else if ($month == JewishDate::IYAR) {
			$omer = $day + 15;
		} else if ($month == JewishDate::SIVAN && $day < 6) {
			$omer = $day + 44;
		}
		return $omer;
	}

	public function isTishaBav($holidayIndex = null) {
	    $holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
	    return $holidayIndex == JewishCalendar::TISHA_BEAV;
	}

	public function getMoladAsDate() {
		$molad = $this->getMolad();

		$locationName = "Jerusalem, Israel";
		$latitude = 31.778;
		$longitude = 35.2354;
		$yerushalayimStandardTZ = "GMT+2";

		$geo = new GeoLocation($locationName, $latitude, $longitude, 0.0, $yerushalayimStandardTZ);

		$moladSeconds = $molad->getMoladChalakim() * 10 / 3;

		$cal = Carbon::create(
			$molad->getGregorianYear(), $molad->getGregorianMonth(), $molad->getGregorianDayOfMonth(),
			$molad->getMoladHours(), $molad->getMoladMinutes(), (int) $moladSeconds,
			$yerushalayimStandardTZ
		);
		$cal->milliseconds = (int) (($moladSeconds - (int) $moladSeconds) * 1000);

		$cal->add(-1 * (int) $geo->getLocalMeanTimeOffset(), "milliseconds");
		return $cal;
	}

	public function getTchilasZmanKidushLevana3Days() {
		return $this->getMoladAsDate()->addHours(72);
	}

	public function getTchilasZmanKidushLevana7Days() {
		return $this->getMoladAsDate()->addHours(168);
	}

	public function getSofZmanKidushLevanaBetweenMoldos() {
		return $this->getMoladAsDate()
			->addHours((24 * 14) + 18)
			->addMinutes(22)
			->addSeconds(1)
			->add(666, "milliseconds");
	}

	public function getSofZmanKidushLevana15Days() {
		return $this->getMoladAsDate()->addHours(24 * 15);
	}

	/*
	|--------------------------------------------------------------------------
	| DAF YOMI
	|--------------------------------------------------------------------------
	*/

	public function getDafYomiBavli() {
		return YomiCalculator::getDafYomiBavli($this);
	}

	public function getDafYomiYerushalmi() {
		return YerushalmiYomiCalculator::getDafYomiYerushalmi($this);
	}

	/*
	|--------------------------------------------------------------------------
	| CALENDAR RULES AND HOLIDAYS CONTINUED
	|--------------------------------------------------------------------------
	*/

	public function getTekufasTishreiElapsedDays() {
		$days = $this->getJewishCalendarElapsedDays($this->getJewishYear()) + (self::getDaysSinceStartOfJewishYear($this->getJewishYear(), $this->getJewishMonth(), $this->getJewishDayOfMonth())-1) + 0.5;

		$solar = ($this->getJewishYear() - 1) * 365.25;
		return floor($days - $solar);
	}

	public function isIsruChag($holidayIndex = null) {
		$holidayIndex = $holidayIndex ?? $this->getYomTovIndex();
		return $holidayIndex == JewishCalendar::ISRU_CHAG;
	}

	/*
	|--------------------------------------------------------------------------
	| HELPER METHODS
	|--------------------------------------------------------------------------
	*/

	public function equals($jewishCalendar) {
		if ($this == $jewishCalendar) {
			return true;
		}
		if (!($jewishCalendar instanceof JewishCalendar)) {
			return false;
		}

		return $this->gregorianAbsDate == $jewishDate->getAbsDate() && $this->getInIsrael() == $jewishCalendar->getInIsrael();
	}
}