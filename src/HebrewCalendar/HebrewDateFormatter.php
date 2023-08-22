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
	private $hebrewOmerPrefix = "\u05D1";
	private $transliteratedShabbosDayOfweek = "Shabbos";

	private $transliteratedParshaMap;
	private $transliteratedMonths;
	private $transliteratedHolidays;

	const GERESH = "\u05F3";
	const GERSHAYIM = "\u05F4";

	const HEBREW_HOLIDAYS = ["\u05E2\u05E8\u05D1 \u05E4\u05E1\u05D7", "\u05E4\u05E1\u05D7",
			"\u05D7\u05D5\u05DC \u05D4\u05DE\u05D5\u05E2\u05D3 \u05E4\u05E1\u05D7",
			"\u05E4\u05E1\u05D7 \u05E9\u05E0\u05D9", "\u05E2\u05E8\u05D1 \u05E9\u05D1\u05D5\u05E2\u05D5\u05EA",
			"\u05E9\u05D1\u05D5\u05E2\u05D5\u05EA",
			"\u05E9\u05D1\u05E2\u05D4 \u05E2\u05E9\u05E8 \u05D1\u05EA\u05DE\u05D5\u05D6",
			"\u05EA\u05E9\u05E2\u05D4 \u05D1\u05D0\u05D1", "\u05D8\u05F4\u05D5 \u05D1\u05D0\u05D1",
			"\u05E2\u05E8\u05D1 \u05E8\u05D0\u05E9 \u05D4\u05E9\u05E0\u05D4",
			"\u05E8\u05D0\u05E9 \u05D4\u05E9\u05E0\u05D4", "\u05E6\u05D5\u05DD \u05D2\u05D3\u05DC\u05D9\u05D4",
			"\u05E2\u05E8\u05D1 \u05D9\u05D5\u05DD \u05DB\u05D9\u05E4\u05D5\u05E8",
			"\u05D9\u05D5\u05DD \u05DB\u05D9\u05E4\u05D5\u05E8", "\u05E2\u05E8\u05D1 \u05E1\u05D5\u05DB\u05D5\u05EA",
			"\u05E1\u05D5\u05DB\u05D5\u05EA",
			"\u05D7\u05D5\u05DC \u05D4\u05DE\u05D5\u05E2\u05D3 \u05E1\u05D5\u05DB\u05D5\u05EA",
			"\u05D4\u05D5\u05E9\u05E2\u05E0\u05D0 \u05E8\u05D1\u05D4",
			"\u05E9\u05DE\u05D9\u05E0\u05D9 \u05E2\u05E6\u05E8\u05EA",
			"\u05E9\u05DE\u05D7\u05EA \u05EA\u05D5\u05E8\u05D4", "\u05E2\u05E8\u05D1 \u05D7\u05E0\u05D5\u05DB\u05D4",
			"\u05D7\u05E0\u05D5\u05DB\u05D4", "\u05E2\u05E9\u05E8\u05D4 \u05D1\u05D8\u05D1\u05EA",
			"\u05D8\u05F4\u05D5 \u05D1\u05E9\u05D1\u05D8", "\u05EA\u05E2\u05E0\u05D9\u05EA \u05D0\u05E1\u05EA\u05E8",
			"\u05E4\u05D5\u05E8\u05D9\u05DD", "\u05E4\u05D5\u05E8\u05D9\u05DD \u05E9\u05D5\u05E9\u05DF",
			"\u05E4\u05D5\u05E8\u05D9\u05DD \u05E7\u05D8\u05DF", "\u05E8\u05D0\u05E9 \u05D7\u05D5\u05D3\u05E9",
			"\u05D9\u05D5\u05DD \u05D4\u05E9\u05D5\u05D0\u05D4",
			"\u05D9\u05D5\u05DD \u05D4\u05D6\u05D9\u05DB\u05E8\u05D5\u05DF",
			"\u05D9\u05D5\u05DD \u05D4\u05E2\u05E6\u05DE\u05D0\u05D5\u05EA",
			"\u05D9\u05D5\u05DD \u05D9\u05E8\u05D5\u05E9\u05DC\u05D9\u05DD",
			"\u05DC\u05F4\u05D2 \u05D1\u05E2\u05D5\u05DE\u05E8",
			"\u05E4\u05D5\u05E8\u05D9\u05DD \u05E9\u05D5\u05E9\u05DF \u05E7\u05D8\u05DF",
			"\u05D0\u05E1\u05E8\u05D5 \u05D7\u05D2"];

	const HEBREW_MONTHS = ["\u05E0\u05D9\u05E1\u05DF", "\u05D0\u05D9\u05D9\u05E8",
			"\u05E1\u05D9\u05D5\u05DF", "\u05EA\u05DE\u05D5\u05D6", "\u05D0\u05D1", "\u05D0\u05DC\u05D5\u05DC",
			"\u05EA\u05E9\u05E8\u05D9", "\u05D7\u05E9\u05D5\u05DF", "\u05DB\u05E1\u05DC\u05D5",
			"\u05D8\u05D1\u05EA", "\u05E9\u05D1\u05D8", "\u05D0\u05D3\u05E8", "\u05D0\u05D3\u05E8 \u05D1",
			"\u05D0\u05D3\u05E8 \u05D0"];

	const HEBREW_DAYS_OF_WEEK = ["\u05E8\u05D0\u05E9\u05D5\u05DF", "\u05E9\u05E0\u05D9",
			"\u05E9\u05DC\u05D9\u05E9\u05D9", "\u05E8\u05D1\u05D9\u05E2\u05D9", "\u05D7\u05DE\u05D9\u05E9\u05D9",
			"\u05E9\u05E9\u05D9", "\u05E9\u05D1\u05EA"];

	const HEBREW_PARSHA_MAP = [
		Parsha::NONE => "",
		Parsha::BERESHIS => "\u05D1\u05E8\u05D0\u05E9\u05D9\u05EA",
		Parsha::NOACH => "\u05E0\u05D7",
		Parsha::LECH_LECHA => "\u05DC\u05DA \u05DC\u05DA",
		Parsha::VAYERA => "\u05D5\u05D9\u05E8\u05D0",
		Parsha::CHAYEI_SARA => "\u05D7\u05D9\u05D9 \u05E9\u05E8\u05D4",
		Parsha::TOLDOS => "\u05EA\u05D5\u05DC\u05D3\u05D5\u05EA",
		Parsha::VAYETZEI => "\u05D5\u05D9\u05E6\u05D0",
		Parsha::VAYISHLACH => "\u05D5\u05D9\u05E9\u05DC\u05D7",
		Parsha::VAYESHEV => "\u05D5\u05D9\u05E9\u05D1",
		Parsha::MIKETZ => "\u05DE\u05E7\u05E5",
		Parsha::VAYIGASH => "\u05D5\u05D9\u05D2\u05E9",
		Parsha::VAYECHI => "\u05D5\u05D9\u05D7\u05D9",
		Parsha::SHEMOS => "\u05E9\u05DE\u05D5\u05EA",
		Parsha::VAERA => "\u05D5\u05D0\u05E8\u05D0",
		Parsha::BO => "\u05D1\u05D0",
		Parsha::BESHALACH => "\u05D1\u05E9\u05DC\u05D7",
		Parsha::YISRO => "\u05D9\u05EA\u05E8\u05D5",
		Parsha::MISHPATIM => "\u05DE\u05E9\u05E4\u05D8\u05D9\u05DD",
		Parsha::TERUMAH => "\u05EA\u05E8\u05D5\u05DE\u05D4",
		Parsha::TETZAVEH => "\u05EA\u05E6\u05D5\u05D4",
		Parsha::KI_SISA => "\u05DB\u05D9 \u05EA\u05E9\u05D0",
		Parsha::VAYAKHEL => "\u05D5\u05D9\u05E7\u05D4\u05DC",
		Parsha::PEKUDEI => "\u05E4\u05E7\u05D5\u05D3\u05D9",
		Parsha::VAYIKRA => "\u05D5\u05D9\u05E7\u05E8\u05D0",
		Parsha::TZAV => "\u05E6\u05D5",
		Parsha::SHMINI => "\u05E9\u05DE\u05D9\u05E0\u05D9",
		Parsha::TAZRIA => "\u05EA\u05D6\u05E8\u05D9\u05E2",
		Parsha::METZORA => "\u05DE\u05E6\u05E8\u05E2",
		Parsha::ACHREI_MOS => "\u05D0\u05D7\u05E8\u05D9 \u05DE\u05D5\u05EA",
		Parsha::KEDOSHIM => "\u05E7\u05D3\u05D5\u05E9\u05D9\u05DD",
		Parsha::EMOR => "\u05D0\u05DE\u05D5\u05E8",
		Parsha::BEHAR => "\u05D1\u05D4\u05E8",
		Parsha::BECHUKOSAI => "\u05D1\u05D7\u05E7\u05EA\u05D9",
		Parsha::BAMIDBAR => "\u05D1\u05DE\u05D3\u05D1\u05E8",
		Parsha::NASSO => "\u05E0\u05E9\u05D0",
		Parsha::BEHAALOSCHA => "\u05D1\u05D4\u05E2\u05DC\u05EA\u05DA",
		Parsha::SHLACH => "\u05E9\u05DC\u05D7 \u05DC\u05DA",
		Parsha::KORACH => "\u05E7\u05E8\u05D7",
		Parsha::CHUKAS => "\u05D7\u05D5\u05E7\u05EA",
		Parsha::BALAK => "\u05D1\u05DC\u05E7",
		Parsha::PINCHAS => "\u05E4\u05D9\u05E0\u05D7\u05E1",
		Parsha::MATOS => "\u05DE\u05D8\u05D5\u05EA",
		Parsha::MASEI => "\u05DE\u05E1\u05E2\u05D9",
		Parsha::DEVARIM => "\u05D3\u05D1\u05E8\u05D9\u05DD",
		Parsha::VAESCHANAN => "\u05D5\u05D0\u05EA\u05D7\u05E0\u05DF",
		Parsha::EIKEV => "\u05E2\u05E7\u05D1",
		Parsha::REEH => "\u05E8\u05D0\u05D4",
		Parsha::SHOFTIM => "\u05E9\u05D5\u05E4\u05D8\u05D9\u05DD",
		Parsha::KI_SEITZEI => "\u05DB\u05D9 \u05EA\u05E6\u05D0",
		Parsha::KI_SAVO => "\u05DB\u05D9 \u05EA\u05D1\u05D5\u05D0",
		Parsha::NITZAVIM => "\u05E0\u05E6\u05D1\u05D9\u05DD",
		Parsha::VAYEILECH => "\u05D5\u05D9\u05DC\u05DA",
		Parsha::HAAZINU => "\u05D4\u05D0\u05D6\u05D9\u05E0\u05D5",
		Parsha::VZOS_HABERACHA => "\u05D5\u05D6\u05D0\u05EA \u05D4\u05D1\u05E8\u05DB\u05D4 ",
		Parsha::VAYAKHEL_PEKUDEI => "\u05D5\u05D9\u05E7\u05D4\u05DC \u05E4\u05E7\u05D5\u05D3\u05D9",
		Parsha::TAZRIA_METZORA => "\u05EA\u05D6\u05E8\u05D9\u05E2 \u05DE\u05E6\u05E8\u05E2",
		Parsha::ACHREI_MOS_KEDOSHIM => "\u05D0\u05D7\u05E8\u05D9 \u05DE\u05D5\u05EA \u05E7\u05D3\u05D5\u05E9\u05D9\u05DD",
		Parsha::BEHAR_BECHUKOSAI => "\u05D1\u05D4\u05E8 \u05D1\u05D7\u05E7\u05EA\u05D9",
		Parsha::CHUKAS_BALAK => "\u05D7\u05D5\u05E7\u05EA \u05D1\u05DC\u05E7",
		Parsha::MATOS_MASEI => "\u05DE\u05D8\u05D5\u05EA \u05DE\u05E1\u05E2\u05D9",
		Parsha::NITZAVIM_VAYEILECH => "\u05E0\u05E6\u05D1\u05D9\u05DD \u05D5\u05D9\u05DC\u05DA",
		Parsha::SHKALIM => "\u05E9\u05E7\u05DC\u05D9\u05DD",
		Parsha::ZACHOR => "\u05D6\u05DB\u05D5\u05E8",
		Parsha::PARA => "\u05E4\u05E8\u05D4",
		Parsha::HACHODESH => "\u05D4\u05D7\u05D3\u05E9",
		Parsha::SHUVA => "\u05E9\u05D5\u05D1\u05D4",
		Parsha::SHIRA => "\u05E9\u05D9\u05E8\u05D4",
		Parsha::HAGADOL => "\u05D4\u05D2\u05D3\u05D5\u05DC",
		Parsha::CHAZON => "\u05D7\u05D6\u05D5\u05DF",
		Parsha::NACHAMU => "\u05E0\u05D7\u05DE\u05D5",
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
					: ($tihs->transliteratedHolidays[$index] . " " . $dayOfChanukah);
		}
		return $index == -1 ? "" : ($this->hebrewFormat ? self::HEBREW_HOLIDAYS[$index] : $tihs->transliteratedHolidays[$index]);
	}

	public function formatRoshChodesh(JewishCalendar $jewishCalendar) {
		if (!$jewishCalendar->isRoshChodesh()) {
			return "";
		}

		$month = $jewishCalendar->getJewishMonth();
		if ($jewishCalendar->getJewishDayOfMonth() == 30) {
			if ($month < JewishCalendar::ADAR || ($month == JewishCalendar::ADAR && $jewishCalendar->isJewishLeapYear())) {
				$month++;
			} else { // roll to Nissan
				$month = JewishCalendar::NISSAN;
			}
		}

		$formattedRoshChodesh = $hebrewFormat ? self::HEBREW_HOLIDAYS[JewishCalendar::ROSH_CHODESH]
				: $tihs->transliteratedHolidays[JewishCalendar::ROSH_CHODESH];
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
		$month = $jewishDate->getJewishMonth();
		if ($this->isHebrewFormat()) {
			if ($jewishDate->isJewishLeapYear() && $month == JewishDate::ADAR) {
				return self::HEBREW_MONTHS[13] . ($this->useGershGershayim ? self::GERESH : ""); // return Adar I, not Adar in a leap year
			} else if ($jewishDate->isJewishLeapYear() && $month == JewishDate::ADAR_II) {
				return self::HEBREW_MONTHS[12] . ($this->useGershGershayim ? self::GERESH : "");
			} else {
				return self::HEBREW_MONTHS[$month - 1];
			}
		} else {
			if ($jewishDate->isJewishLeapYear() && $month == JewishDate::ADAR) {
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
			return $this->formatHebrewNumber($omer) . " " . $this->hebrewOmerPrefix . "\u05E2\u05D5\u05DE\u05E8";
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
		$returnValue .= ($kviah == JewishDate::CHASERIM ? "\u05D7" : ($kviah == JewishDate::SHELAIMIM ? "\u05E9" : "\u05DB"));
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

		$alafim = "\u05D0\u05DC\u05E4\u05D9\u05DD";
		$efes = "\u05D0\u05E4\u05E1";

		$jHundreds = ["", "\u05E7", "\u05E8", "\u05E9", "\u05EA", "\u05EA\u05E7", "\u05EA\u05E8",
				"\u05EA\u05E9", "\u05EA\u05EA", "\u05EA\u05EA\u05E7"];
		$jTens = ["", "\u05D9", "\u05DB", "\u05DC", "\u05DE", "\u05E0", "\u05E1", "\u05E2",
				"\u05E4", "\u05E6"];
		$jTenEnds = ["", "\u05D9", "\u05DA", "\u05DC", "\u05DD", "\u05DF", "\u05E1", "\u05E2",
				"\u05E3", "\u05E5"];
		$tavTaz = ["\u05D8\u05D5", "\u05D8\u05D6"];
		$jOnes = ["", "\u05D0", "\u05D1", "\u05D2", "\u05D3", "\u05D4", "\u05D5", "\u05D6",
				"\u05D7", "\u05D8"];

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
				$year = substr_replace($year, self::GERSHAYIM, strlen($year) - 6, 0);
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