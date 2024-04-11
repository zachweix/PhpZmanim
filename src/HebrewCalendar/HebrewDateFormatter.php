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

class HebrewDateFormatter {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private $hebrewFormat = false;
	private $useLonghebrewYears = false;
	private $useGershGershayim = true;
	private $longWeekFormat = true;
	private $useFinalFormLetters = false;
	private $hebrewOmerPrefix = "\327\221";
	private $transliteratedShabbosDayOfweek = "Shabbos";

	private $transliteratedParshaMap;
	private $transliteratedMonths;
	private $transliteratedHolidays;

	const GERESH = "\327\263";
	const GERSHAYIM = "\327\264";

	const HEBREW_HOLIDAYS = ["\327\242\327\250\327\221 \327\244\327\241\327\227", "\327\244\327\241\327\227",
			"\327\227\327\225\327\234 \327\224\327\236\327\225\327\242\327\223 \327\244\327\241\327\227",
			"\327\244\327\241\327\227 \327\251\327\240\327\231", "\327\242\327\250\327\221 \327\251\327\221\327\225\327\242\327\225\327\252",
			"\327\251\327\221\327\225\327\242\327\225\327\252",
			"\327\251\327\221\327\242\327\224 \327\242\327\251\327\250 \327\221\327\252\327\236\327\225\327\226",
			"\327\252\327\251\327\242\327\224 \327\221\327\220\327\221", "\327\230\327\264\327\225 \327\221\327\220\327\221",
			"\327\242\327\250\327\221 \327\250\327\220\327\251 \327\224\327\251\327\240\327\224",
			"\327\250\327\220\327\251 \327\224\327\251\327\240\327\224", "\327\246\327\225\327\235 \327\222\327\223\327\234\327\231\327\224",
			"\327\242\327\250\327\221 \327\231\327\225\327\235 \327\233\327\231\327\244\327\225\327\250",
			"\327\231\327\225\327\235 \327\233\327\231\327\244\327\225\327\250", "\327\242\327\250\327\221 \327\241\327\225\327\233\327\225\327\252",
			"\327\241\327\225\327\233\327\225\327\252",
			"\327\227\327\225\327\234 \327\224\327\236\327\225\327\242\327\223 \327\241\327\225\327\233\327\225\327\252",
			"\327\224\327\225\327\251\327\242\327\240\327\220 \327\250\327\221\327\224",
			"\327\251\327\236\327\231\327\240\327\231 \327\242\327\246\327\250\327\252",
			"\327\251\327\236\327\227\327\252 \327\252\327\225\327\250\327\224", "\327\242\327\250\327\221 \327\227\327\240\327\225\327\233\327\224",
			"\327\227\327\240\327\225\327\233\327\224", "\327\242\327\251\327\250\327\224 \327\221\327\230\327\221\327\252",
			"\327\230\327\264\327\225 \327\221\327\251\327\221\327\230", "\327\252\327\242\327\240\327\231\327\252 \327\220\327\241\327\252\327\250",
			"\327\244\327\225\327\250\327\231\327\235", "\327\244\327\225\327\250\327\231\327\235 \327\251\327\225\327\251\327\237",
			"\327\244\327\225\327\250\327\231\327\235 \327\247\327\230\327\237", "\327\250\327\220\327\251 \327\227\327\225\327\223\327\251",
			"\327\231\327\225\327\235 \327\224\327\251\327\225\327\220\327\224",
			"\327\231\327\225\327\235 \327\224\327\226\327\231\327\233\327\250\327\225\327\237",
			"\327\231\327\225\327\235 \327\224\327\242\327\246\327\236\327\220\327\225\327\252",
			"\327\231\327\225\327\235 \327\231\327\250\327\225\327\251\327\234\327\231\327\235",
			"\327\234\327\264\327\222 \327\221\327\242\327\225\327\236\327\250",
			"\327\244\327\225\327\250\327\231\327\235 \327\251\327\225\327\251\327\237 \327\247\327\230\327\237",
			"\327\220\327\241\327\250\327\225 \327\227\327\222"];

	const HEBREW_MONTHS = ["\327\240\327\231\327\241\327\237", "\327\220\327\231\327\231\327\250",
			"\327\241\327\231\327\225\327\237", "\327\252\327\236\327\225\327\226", "\327\220\327\221", "\327\220\327\234\327\225\327\234",
			"\327\252\327\251\327\250\327\231", "\327\227\327\251\327\225\327\237", "\327\233\327\241\327\234\327\225",
			"\327\230\327\221\327\252", "\327\251\327\221\327\230", "\327\220\327\223\327\250", "\327\220\327\223\327\250 \327\221",
			"\327\220\327\223\327\250 \327\220"];

	const HEBREW_DAYS_OF_WEEK = ["\327\250\327\220\327\251\327\225\327\237", "\327\251\327\240\327\231",
			"\327\251\327\234\327\231\327\251\327\231", "\327\250\327\221\327\231\327\242\327\231", "\327\227\327\236\327\231\327\251\327\231",
			"\327\251\327\251\327\231", "\327\251\327\221\327\252"];

	const HEBREW_PARSHA_MAP = [
		Parsha::NONE => "",
		Parsha::BERESHIS => "\327\221\327\250\327\220\327\251\327\231\327\252",
		Parsha::NOACH => "\327\240\327\227",
		Parsha::LECH_LECHA => "\327\234\327\232 \327\234\327\232",
		Parsha::VAYERA => "\327\225\327\231\327\250\327\220",
		Parsha::CHAYEI_SARA => "\327\227\327\231\327\231 \327\251\327\250\327\224",
		Parsha::TOLDOS => "\327\252\327\225\327\234\327\223\327\225\327\252",
		Parsha::VAYETZEI => "\327\225\327\231\327\246\327\220",
		Parsha::VAYISHLACH => "\327\225\327\231\327\251\327\234\327\227",
		Parsha::VAYESHEV => "\327\225\327\231\327\251\327\221",
		Parsha::MIKETZ => "\327\236\327\247\327\245",
		Parsha::VAYIGASH => "\327\225\327\231\327\222\327\251",
		Parsha::VAYECHI => "\327\225\327\231\327\227\327\231",
		Parsha::SHEMOS => "\327\251\327\236\327\225\327\252",
		Parsha::VAERA => "\327\225\327\220\327\250\327\220",
		Parsha::BO => "\327\221\327\220",
		Parsha::BESHALACH => "\327\221\327\251\327\234\327\227",
		Parsha::YISRO => "\327\231\327\252\327\250\327\225",
		Parsha::MISHPATIM => "\327\236\327\251\327\244\327\230\327\231\327\235",
		Parsha::TERUMAH => "\327\252\327\250\327\225\327\236\327\224",
		Parsha::TETZAVEH => "\327\252\327\246\327\225\327\224",
		Parsha::KI_SISA => "\327\233\327\231 \327\252\327\251\327\220",
		Parsha::VAYAKHEL => "\327\225\327\231\327\247\327\224\327\234",
		Parsha::PEKUDEI => "\327\244\327\247\327\225\327\223\327\231",
		Parsha::VAYIKRA => "\327\225\327\231\327\247\327\250\327\220",
		Parsha::TZAV => "\327\246\327\225",
		Parsha::SHMINI => "\327\251\327\236\327\231\327\240\327\231",
		Parsha::TAZRIA => "\327\252\327\226\327\250\327\231\327\242",
		Parsha::METZORA => "\327\236\327\246\327\250\327\242",
		Parsha::ACHREI_MOS => "\327\220\327\227\327\250\327\231 \327\236\327\225\327\252",
		Parsha::KEDOSHIM => "\327\247\327\223\327\225\327\251\327\231\327\235",
		Parsha::EMOR => "\327\220\327\236\327\225\327\250",
		Parsha::BEHAR => "\327\221\327\224\327\250",
		Parsha::BECHUKOSAI => "\327\221\327\227\327\247\327\252\327\231",
		Parsha::BAMIDBAR => "\327\221\327\236\327\223\327\221\327\250",
		Parsha::NASSO => "\327\240\327\251\327\220",
		Parsha::BEHAALOSCHA => "\327\221\327\224\327\242\327\234\327\252\327\232",
		Parsha::SHLACH => "\327\251\327\234\327\227 \327\234\327\232",
		Parsha::KORACH => "\327\247\327\250\327\227",
		Parsha::CHUKAS => "\327\227\327\225\327\247\327\252",
		Parsha::BALAK => "\327\221\327\234\327\247",
		Parsha::PINCHAS => "\327\244\327\231\327\240\327\227\327\241",
		Parsha::MATOS => "\327\236\327\230\327\225\327\252",
		Parsha::MASEI => "\327\236\327\241\327\242\327\231",
		Parsha::DEVARIM => "\327\223\327\221\327\250\327\231\327\235",
		Parsha::VAESCHANAN => "\327\225\327\220\327\252\327\227\327\240\327\237",
		Parsha::EIKEV => "\327\242\327\247\327\221",
		Parsha::REEH => "\327\250\327\220\327\224",
		Parsha::SHOFTIM => "\327\251\327\225\327\244\327\230\327\231\327\235",
		Parsha::KI_SEITZEI => "\327\233\327\231 \327\252\327\246\327\220",
		Parsha::KI_SAVO => "\327\233\327\231 \327\252\327\221\327\225\327\220",
		Parsha::NITZAVIM => "\327\240\327\246\327\221\327\231\327\235",
		Parsha::VAYEILECH => "\327\225\327\231\327\234\327\232",
		Parsha::HAAZINU => "\327\224\327\220\327\226\327\231\327\240\327\225",
		Parsha::VZOS_HABERACHA => "\327\225\327\226\327\220\327\252 \327\224\327\221\327\250\327\233\327\224 ",
		Parsha::VAYAKHEL_PEKUDEI => "\327\225\327\231\327\247\327\224\327\234 \327\244\327\247\327\225\327\223\327\231",
		Parsha::TAZRIA_METZORA => "\327\252\327\226\327\250\327\231\327\242 \327\236\327\246\327\250\327\242",
		Parsha::ACHREI_MOS_KEDOSHIM => "\327\220\327\227\327\250\327\231 \327\236\327\225\327\252 \327\247\327\223\327\225\327\251\327\231\327\235",
		Parsha::BEHAR_BECHUKOSAI => "\327\221\327\224\327\250 \327\221\327\227\327\247\327\252\327\231",
		Parsha::CHUKAS_BALAK => "\327\227\327\225\327\247\327\252 \327\221\327\234\327\247",
		Parsha::MATOS_MASEI => "\327\236\327\230\327\225\327\252 \327\236\327\241\327\242\327\231",
		Parsha::NITZAVIM_VAYEILECH => "\327\240\327\246\327\221\327\231\327\235 \327\225\327\231\327\234\327\232",
		Parsha::SHKALIM => "\327\251\327\247\327\234\327\231\327\235",
		Parsha::ZACHOR => "\327\226\327\233\327\225\327\250",
		Parsha::PARA => "\327\244\327\250\327\224",
		Parsha::HACHODESH => "\327\224\327\227\327\223\327\251",
		Parsha::SHUVA => "\327\251\327\225\327\221\327\224",
		Parsha::SHIRA => "\327\251\327\231\327\250\327\224",
		Parsha::HAGADOL => "\327\224\327\222\327\223\327\225\327\234",
		Parsha::CHAZON => "\327\227\327\226\327\225\327\237",
		Parsha::NACHAMU => "\327\240\327\227\327\236\327\225",
	];

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTOR
	|--------------------------------------------------------------------------
	*/

	public static function create() {
		return new static();
	}

	public function __construct() {
		$this->transliteratedParshaMap = [
			Parsha::NONE => "",
			Parsha::BERESHIS => "Bereshis",
			Parsha::NOACH => "Noach",
			Parsha::LECH_LECHA => "Lech Lecha",
			Parsha::VAYERA => "Vayera",
			Parsha::CHAYEI_SARA => "Chayei Sara",
			Parsha::TOLDOS => "Toldos",
			Parsha::VAYETZEI => "Vayetzei",
			Parsha::VAYISHLACH => "Vayishlach",
			Parsha::VAYESHEV => "Vayeshev",
			Parsha::MIKETZ => "Miketz",
			Parsha::VAYIGASH => "Vayigash",
			Parsha::VAYECHI => "Vayechi",
			Parsha::SHEMOS => "Shemos",
			Parsha::VAERA => "Vaera",
			Parsha::BO => "Bo",
			Parsha::BESHALACH => "Beshalach",
			Parsha::YISRO => "Yisro",
			Parsha::MISHPATIM => "Mishpatim",
			Parsha::TERUMAH => "Terumah",
			Parsha::TETZAVEH => "Tetzaveh",
			Parsha::KI_SISA => "Ki Sisa",
			Parsha::VAYAKHEL => "Vayakhel",
			Parsha::PEKUDEI => "Pekudei",
			Parsha::VAYIKRA => "Vayikra",
			Parsha::TZAV => "Tzav",
			Parsha::SHMINI => "Shmini",
			Parsha::TAZRIA => "Tazria",
			Parsha::METZORA => "Metzora",
			Parsha::ACHREI_MOS => "Achrei Mos",
			Parsha::KEDOSHIM => "Kedoshim",
			Parsha::EMOR => "Emor",
			Parsha::BEHAR => "Behar",
			Parsha::BECHUKOSAI => "Bechukosai",
			Parsha::BAMIDBAR => "Bamidbar",
			Parsha::NASSO => "Nasso",
			Parsha::BEHAALOSCHA => "Beha'aloscha",
			Parsha::SHLACH => "Sh'lach",
			Parsha::KORACH => "Korach",
			Parsha::CHUKAS => "Chukas",
			Parsha::BALAK => "Balak",
			Parsha::PINCHAS => "Pinchas",
			Parsha::MATOS => "Matos",
			Parsha::MASEI => "Masei",
			Parsha::DEVARIM => "Devarim",
			Parsha::VAESCHANAN => "Vaeschanan",
			Parsha::EIKEV => "Eikev",
			Parsha::REEH => "Re'eh",
			Parsha::SHOFTIM => "Shoftim",
			Parsha::KI_SEITZEI => "Ki Seitzei",
			Parsha::KI_SAVO => "Ki Savo",
			Parsha::NITZAVIM => "Nitzavim",
			Parsha::VAYEILECH => "Vayeilech",
			Parsha::HAAZINU => "Ha'Azinu",
			Parsha::VZOS_HABERACHA => "Vezos Habracha",
			Parsha::VAYAKHEL_PEKUDEI => "Vayakhel Pekudei",
			Parsha::TAZRIA_METZORA => "Tazria Metzora",
			Parsha::ACHREI_MOS_KEDOSHIM => "Achrei Mos Kedoshim",
			Parsha::BEHAR_BECHUKOSAI => "Behar Bechukosai",
			Parsha::CHUKAS_BALAK => "Chukas Balak",
			Parsha::MATOS_MASEI => "Matos Masei",
			Parsha::NITZAVIM_VAYEILECH => "Nitzavim Vayeilech",
			Parsha::SHKALIM => "Shekalim",
			Parsha::ZACHOR => "Zachor",
			Parsha::PARA => "Parah",
			Parsha::HACHODESH => "Hachodesh",
			Parsha::SHUVA => "Shuva",
			Parsha::SHIRA => "Shira",
			Parsha::HAGADOL => "Hagadol",
			Parsha::CHAZON => "Chazon",
			Parsha::NACHAMU => "Nachamu",
		];

		$this->transliteratedMonths = [
			"Nissan", "Iyar", "Sivan", "Tammuz", "Av", "Elul", "Tishrei", "Cheshvan",
			"Kislev", "Teves", "Shevat", "Adar", "Adar II", "Adar I"
		];

		$this->transliteratedHolidays = ["Erev Pesach", "Pesach", "Chol Hamoed Pesach", "Pesach Sheni",
			"Erev Shavuos", "Shavuos", "Seventeenth of Tammuz", "Tishah B'Av", "Tu B'Av", "Erev Rosh Hashana",
			"Rosh Hashana", "Fast of Gedalyah", "Erev Yom Kippur", "Yom Kippur", "Erev Succos", "Succos",
			"Chol Hamoed Succos", "Hoshana Rabbah", "Shemini Atzeres", "Simchas Torah", "Erev Chanukah", "Chanukah",
			"Tenth of Teves", "Tu B'Shvat", "Fast of Esther", "Purim", "Shushan Purim", "Purim Katan", "Rosh Chodesh",
			"Yom HaShoah", "Yom Hazikaron", "Yom Ha'atzmaut", "Yom Yerushalayim", "Lag B'Omer","Shushan Purim Katan",
			"Isru Chag"];
	}

	/*
	|--------------------------------------------------------------------------
	| SETTERS AND GETTERS
	|--------------------------------------------------------------------------
	*/

	public function isLongWeekFormat() {
		return $this->longWeekFormat;
	}

	public function setLongWeekFormat($longWeekFormat) {
		$this->longWeekFormat = $longWeekFormat;

		return $this;
	}

	public function getTransliteratedShabbosDayOfWeek() {
		return $this->transliteratedShabbosDayOfweek;
	}

	public function setTransliteratedShabbosDayOfWeek($transliteratedShabbos) {
		$this->transliteratedShabbosDayOfweek = $transliteratedShabbos;

		return $this;
	}

	public function getTransliteratedHolidayList() {
		return $transliteratedHolidays;
	}

	public function setTransliteratedHolidayList($transliteratedHolidays) {
		$this->transliteratedHolidays = $transliteratedHolidays;

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| FORMATTERS
	|--------------------------------------------------------------------------
	*/

	public function formatYomTov(JewishCalendar $jewishCalendar) {
		$index = $jewishCalendar->getYomTovIndex();
		if ($index == JewishCalendar::CHANUKAH) {
			$dayOfChanukah = $jewishCalendar->getDayOfChanukah();
			return $this->hebrewFormat ? ($this->formatHebrewNumber($dayOfChanukah) . " " . self::HEBREW_HOLIDAYS[$index])
					: ($this->transliteratedHolidays[$index] . " " . $dayOfChanukah);
		}
		return $index == -1 ? "" : ($this->hebrewFormat ? self::HEBREW_HOLIDAYS[$index] : $this->transliteratedHolidays[$index]);
	}

	public function formatRoshChodesh(JewishCalendar $jewishCalendar) {
		if (!$jewishCalendar->isRoshChodesh()) {
			return "";
		}

		$year = $jewishCalendar->getJewishYear();
		$month = $jewishCalendar->getJewishMonth();
		if ($jewishCalendar->getJewishDayOfMonth() == 30) {
			if ($month < JewishCalendar::ADAR || ($month == JewishCalendar::ADAR && JewishDate::isJewishLeapYear($year))) {
				$month++;
			} else { // roll to Nissan
				$month = JewishCalendar::NISSAN;
			}
		}

		$formattedRoshChodesh = $hebrewFormat ? self::HEBREW_HOLIDAYS[JewishCalendar::ROSH_CHODESH]
				: $this->transliteratedHolidays[JewishCalendar::ROSH_CHODESH];
		$formattedRoshChodesh .= " " . $this->formatMonth($jewishCalendar->clone()->setJewishMonth($month));
		return $formattedRoshChodesh;
	}

	/*
	|--------------------------------------------------------------------------
	| SETTERS AND GETTERS CONTINUED
	|--------------------------------------------------------------------------
	*/

	public function isHebrewFormat() {
		return $this->hebrewFormat;
	}

	public function setHebrewFormat($hebrewFormat) {
		$this->hebrewFormat = $hebrewFormat;

		return $this;
	}

	public function getHebrewOmerPrefix() {
		return $this->hebrewOmerPrefix;
	}

	public function setHebrewOmerPrefix($hebrewOmerPrefix) {
		$this->hebrewOmerPrefix = $hebrewOmerPrefix;

		return $this;
	}

	public function getTransliteratedMonthList() {
		return $this->transliteratedMonths;
	}

	public function setTransliteratedMonthList($transliteratedMonths) {
		$this->transliteratedMonths = $transliteratedMonths;

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| FORMATTERS
	|--------------------------------------------------------------------------
	*/

	public function formatDayOfWeek(JewishDate $jewishDate) {
		if ($this->hebrewFormat) {
			if($this->isLongWeekFormat()) {
				return self::HEBREW_DAYS_OF_WEEK[$jewishDate->getDayOfWeek() - 1];
			} else {
				if($jewishDate->getDayOfWeek() == 7) {
					return $this->formatHebrewNumber(300);
				} else {
					return $this->formatHebrewNumber($jewishDate->getDayOfWeek());
				}
			}
		} else {
			if($jewishDate->getDayOfWeek() == 7) {
				if($this->isLongWeekFormat()) {
					return $this->getTransliteratedShabbosDayOfWeek();
				} else {
					return substr($this->getTransliteratedShabbosDayOfWeek(), 0, 3);
				}
			} else {
				$format = $this->isLongWeekFormat() ? "l" : "D";
				return $jewishDate->getGregorianCalendar()->format($format);
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| SETTERS AND GETTERS CONTINUED
	|--------------------------------------------------------------------------
	*/

	public function isUseGershGershayim() {
		return $this->useGershGershayim;
	}

	public function setUseGershGershayim($useGershGershayim) {
		$this->useGershGershayim = $useGershGershayim;

		return $this;
	}

	public function isUseFinalFormLetters() {
		return $this->useFinalFormLetters;
	}

	public function setUseFinalFormLetters($useFinalFormLetters) {
		$this->useFinalFormLetters = $useFinalFormLetters;

		return $this;
	}

	public function isUseLongHebrewYears() {
		return $this->useLonghebrewYears;
	}

	public function setUseLongHebrewYears($useLongHebrewYears) {
		$this->useLonghebrewYears = $useLongHebrewYears;

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| FORMATTERS CONTINUED
	|--------------------------------------------------------------------------
	*/

	public function format(JewishDate $jewishDate) {
		if ($this->isHebrewFormat()) {
			return $this->formatHebrewNumber($jewishDate->getJewishDayOfMonth()) . " " . $this->formatMonth($jewishDate) . " "
					. $this->formatHebrewNumber($jewishDate->getJewishYear());
		} else {
			return $jewishDate->getJewishDayOfMonth() . " " . $this->formatMonth($jewishDate) . ", " . $jewishDate->getJewishYear();
		}
	}

	public function formatMonth(JewishDate $jewishDate) {
		$year = $jewishDate->getJewishYear();
		$month = $jewishDate->getJewishMonth();
		if ($this->isHebrewFormat()) {
			if (JewishDate::isJewishLeapYear($year) && $month == JewishDate::ADAR) {
				return self::HEBREW_MONTHS[13] . ($this->useGershGershayim ? self::GERESH : ""); // return Adar I, not Adar in a leap year
			} else if (JewishDate::isJewishLeapYear($year) && $month == JewishDate::ADAR_II) {
				return self::HEBREW_MONTHS[12] . ($this->useGershGershayim ? self::GERESH : "");
			} else {
				return self::HEBREW_MONTHS[$month - 1];
			}
		} else {
			if (JewishDate::isJewishLeapYear($year) && $month == JewishDate::ADAR) {
				return $this->transliteratedMonths[13]; // return Adar I, not Adar in a leap year
			} else {
				return $this->transliteratedMonths[$month - 1];
			}
		}
	}

	public function formatOmer(JewishCalendar $jewishCalendar) {
		$omer = $jewishCalendar->getDayOfOmer();
		if ($omer == -1) {
			return "";
		}
		if ($this->hebrewFormat) {
			return $this->formatHebrewNumber($omer) . " " . $this->hebrewOmerPrefix . "\327\242\327\225\327\236\327\250";
		} else {
			if ($omer == 33) { // if Lag B'Omer
				return $this->transliteratedHolidays[33];
			} else {
				return "Omer " . $omer;
			}
		}
	}

	private static function formatMolad($moladChalakim) {
		$adjustedChalakim = $moladChalakim;
		$minute_chalakim = 18;
		$hour_chalakim = 1080;
		$day_chalakim = 24 * $hour_chalakim;

		$days = $adjustedChalakim / $day_chalakim;
		$adjustedChalakim = $adjustedChalakim - ($days * $day_chalakim);
		$hours = (int) (($adjustedChalakim / $hour_chalakim));
		if ($hours >= 6) {
			$days += 1;
		}
		$adjustedChalakim = $adjustedChalakim - ($hours * $hour_chalakim);
		$minutes = (int) ($adjustedChalakim / $minute_chalakim);
		$adjustedChalakim = $adjustedChalakim - $minutes * $minute_chalakim;
		return "Day: " . ($days % 7) . " hours: " . $hours . ", minutes " . $minutes . ", chalakim: " . $adjustedChalakim;
	}

	public function getFormattedKviah($jewishYear) {
		$jewishDate = new JewishDate($jewishYear, JewishDate::TISHREI, 1); // set date to Rosh Hashana
		$kviah = $jewishDate->getCheshvanKislevKviah();
		$roshHashanaDayOfweek = $jewishDate->getDayOfWeek();

		$pesachDayOfweek = $roshHashanaDayOfweek + 1;
		if ($kviah = JewishDate::KESIDRAN) {
			$pesachDayOfweek++;
		} else if ($kviah == JewishDate::SHELAIMIM) {
			$pesachDayOfweek += 2;
		}
		if (JewishDate::isJewishLeapYear($jewishYear)) {
			$pesachDayOfweek += 2;
		}
		if ($pesachDayOfweek > 7) {
			$pesachDayOfweek -= 7;
		}

		$returnValue = $this->formatHebrewNumber($roshHashanaDayOfweek);
		$returnValue .= ($kviah == JewishDate::CHASERIM ? "\327\227" : ($kviah == JewishDate::SHELAIMIM ? "\327\251" : "\327\233"));
		$returnValue .= $this->formatHebrewNumber($pesachDayOfweek);

		return str_replace(self::GERESH, "", $returnValue);
	}

	/*
	|--------------------------------------------------------------------------
	| DAF YOMI
	|--------------------------------------------------------------------------
	*/

	public function formatDafYomiBavli($daf) {
		if ($this->hebrewFormat) {
			return $daf->getMasechta() . " " . $this->formatHebrewNumber($daf->getDaf());
		} else {
			return $daf->getMasechtaTransliterated() . " " . $daf->getDaf();
		}
	}

	public function formatDafYomiYerushalmi($daf) {
		if($daf == null) {
			if ($this->hebrewFormat) {
				return Daf::getYerushalmiMasechtos()[39];
			} else {
				return Daf::getYerushalmiMasechtosTransliterated()[39];
			}
		}
		if ($this->hebrewFormat) {			
			return $daf->getYerushalmiMasechta() . " " . $this->formatHebrewNumber($daf->getDaf());
		} else {
			return $daf->getYerushalmiMasechtaTransliterated() . " " . $daf->getDaf();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| FORMAT HEBREW NUMBER
	|--------------------------------------------------------------------------
	*/

	public function formatHebrewNumber($number) {
		if ($number < 0) {
			throw new \Exception("negative numbers can't be formatted");
		} else if ($number > 9999) {
			throw new \Exception("numbers > 9999 can't be formatted");
		}

		$alafim = "\327\220\327\234\327\244\327\231\327\235";
		$efes = "\327\220\327\244\327\241";

		$jHundreds = ["", "\327\247", "\327\250", "\327\251", "\327\252", "\327\252\327\247", "\327\252\327\250",
				"\327\252\327\251", "\327\252\327\252", "\327\252\327\252\327\247"];
		$jTens = ["", "\327\231", "\327\233", "\327\234", "\327\236", "\327\240", "\327\241", "\327\242",
				"\327\244", "\327\246"];
		$jTenEnds = ["", "\327\231", "\327\232", "\327\234", "\327\235", "\327\237", "\327\241", "\327\242",
				"\327\243", "\327\245"];
		$tavTaz = ["\327\230\327\225", "\327\230\327\226"];
		$jOnes = ["", "\327\220", "\327\221", "\327\222", "\327\223", "\327\224", "\327\225", "\327\226",
				"\327\227", "\327\230"];

		if ($number == 0) {
			return $efes;
		}

		$shortNumber = $number % 1000;
		$singleDigitNumber = ($shortNumber < 11 || ($shortNumber < 100 && $shortNumber % 10 == 0) || ($shortNumber <= 400 && $shortNumber % 100 == 0));
		$thousands = (int) ($number / 1000);

		$year = "";
		if ($number % 1000 == 0) {
			return $jOnes[$thousands] . ($this->isUseGershGershayim() ? self::GERESH : "") . " " . $alafim;
		} else if ($this->isUseLongHebrewYears() && $number >= 1000) {
			$year = $jOnes[$thousands] . ($this->isUseGershGershayim() ? self::GERESH : "") . " ";
		}

		$number = $number % 1000;
		$hundreds = (int) ($number / 100);
		$year .= $jHundreds[$hundreds];

		$number = $number % 100;
		if ($number == 15) {
			$year .= $tavTaz[0];
		} else if ($number == 16) {
			$year .= $tavTaz[1];
		} else {
			$tens = (int) ($number / 10);
			if ($number % 10 == 0) {
				if (!$singleDigitNumber) {
					if($this->isUseFinalFormLetters()) {
						$year .= $jTenEnds[$tens];
					} else {
						$year .= $jTens[$tens];
					}
				} else {
					$year .= $jTens[$tens];
				}
			} else {
				$year .= $jTens[$tens];

				$number = $number % 10;
				$year .= $jOnes[$number];
			}
		}
		if ($this->isUseGershGershayim()) {
			if ($singleDigitNumber) {
				$year .= self::GERESH;
			} else {
				$year = substr_replace($year, self::GERSHAYIM, strlen($year) - 2, 0);
			}
		}

		return $year;
	}

	/*
	|--------------------------------------------------------------------------
	| PARSHA
	|--------------------------------------------------------------------------
	*/

	public function getTransliteratedParshiosList() {
		return $this->transliteratedParshaMap;
	}

	public function setTransliteratedParshiosList($transliteratedParshaMap) {
		$this->transliteratedParshaMap = $transliteratedParshaMap;

		return $this;
	}

	public function formatParsha(JewishCalendar $jewishCalendar) {
		$parsha =  $jewishCalendar->getParshah();
		return $this->hebrewFormat ? self::HEBREW_PARSHA_MAP[$parsha] : $this->transliteratedParshaMap[$parsha];
	}

	public function formatSpecialParsha(JewishCalendar $jewishCalendar) {
		$specialParsha =  $jewishCalendar->getSpecialShabbos();
		return $this->hebrewFormat ? self::HEBREW_PARSHA_MAP[$specialParsha] : $this->transliteratedParshaMap[$specialParsha];
	}
}