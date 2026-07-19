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

namespace PhpZmanim\Torah;

enum YomTov: int implements Nameable
{
	case NONE = 0;
	case EREV_PESACH = 1;
	case PESACH = 2;
	case CHOL_HAMOED_PESACH = 3;
	case PESACH_SHENI = 4;
	case EREV_SHAVUOS = 5;
	case SHAVUOS = 6;
	case SEVENTEEN_OF_TAMMUZ = 7;
	case TISHA_BEAV = 8;
	case TU_BEAV = 9;
	case EREV_ROSH_HASHANA = 10;
	case ROSH_HASHANA = 11;
	case FAST_OF_GEDALYAH = 12;
	case EREV_YOM_KIPPUR = 13;
	case YOM_KIPPUR = 14;
	case EREV_SUCCOS = 15;
	case SUCCOS = 16;
	case CHOL_HAMOED_SUCCOS = 17;
	case HOSHANA_RABBA = 18;
	case SHEMINI_ATZERES = 19;
	case SIMCHAS_TORAH = 20;
	case CHANUKAH = 21;
	case TENTH_OF_TEVES = 22;
	case TU_BESHVAT = 23;
	case FAST_OF_ESTHER = 24;
	case PURIM = 25;
	case SHUSHAN_PURIM = 26;
	case PURIM_KATAN = 27;
	case ROSH_CHODESH = 28;
	case YOM_HASHOAH = 29;
	case YOM_HAZIKARON = 30;
	case YOM_HAATZMAUT = 31;
	case YOM_YERUSHALAYIM = 32;
	case LAG_BAOMER = 33;
	case SHUSHAN_PURIM_KATAN = 34;
	case ISRU_CHAG = 35;
	case YOM_KIPPUR_KATAN = 36;
	case BEHAB = 37;

	/**
	 * The Hebrew name of the holiday.
	 */
	public function hebrew(): string
	{
		return match ($this) {
			self::NONE => "",
			self::EREV_PESACH => "ערב פסח",
			self::PESACH => "פסח",
			self::CHOL_HAMOED_PESACH => "חול המועד פסח",
			self::PESACH_SHENI => "פסח שני",
			self::EREV_SHAVUOS => "ערב שבועות",
			self::SHAVUOS => "שבועות",
			self::SEVENTEEN_OF_TAMMUZ => "שבעה עשר בתמוז",
			self::TISHA_BEAV => "תשעה באב",
			self::TU_BEAV => "ט״ו באב",
			self::EREV_ROSH_HASHANA => "ערב ראש השנה",
			self::ROSH_HASHANA => "ראש השנה",
			self::FAST_OF_GEDALYAH => "צום גדליה",
			self::EREV_YOM_KIPPUR => "ערב יום כיפור",
			self::YOM_KIPPUR => "יום כיפור",
			self::EREV_SUCCOS => "ערב סוכות",
			self::SUCCOS => "סוכות",
			self::CHOL_HAMOED_SUCCOS => "חול המועד סוכות",
			self::HOSHANA_RABBA => "הושענא רבה",
			self::SHEMINI_ATZERES => "שמיני עצרת",
			self::SIMCHAS_TORAH => "שמחת תורה",
			self::CHANUKAH => "חנוכה",
			self::TENTH_OF_TEVES => "עשרה בטבת",
			self::TU_BESHVAT => "ט״ו בשבט",
			self::FAST_OF_ESTHER => "תענית אסתר",
			self::PURIM => "פורים",
			self::SHUSHAN_PURIM => "פורים שושן",
			self::PURIM_KATAN => "פורים קטן",
			self::ROSH_CHODESH => "ראש חודש",
			self::YOM_HASHOAH => "יום השואה",
			self::YOM_HAZIKARON => "יום הזיכרון",
			self::YOM_HAATZMAUT => "יום העצמאות",
			self::YOM_YERUSHALAYIM => "יום ירושלים",
			self::LAG_BAOMER => "ל״ג בעומר",
			self::SHUSHAN_PURIM_KATAN => "פורים שושן קטן",
			self::ISRU_CHAG => "אסרו חג",
			self::YOM_KIPPUR_KATAN => "יום כיפור קטן",
			self::BEHAB => "בה״ב",
		};
	}

	/**
	 * The Ashkenazi transliterated English name of the holiday.
	 */
	public function english(): string
	{
		return match ($this) {
			self::NONE => "",
			self::EREV_PESACH => "Erev Pesach",
			self::PESACH => "Pesach",
			self::CHOL_HAMOED_PESACH => "Chol Hamoed Pesach",
			self::PESACH_SHENI => "Pesach Sheni",
			self::EREV_SHAVUOS => "Erev Shavuos",
			self::SHAVUOS => "Shavuos",
			self::SEVENTEEN_OF_TAMMUZ => "Seventeenth of Tammuz",
			self::TISHA_BEAV => "Tishah B'Av",
			self::TU_BEAV => "Tu B'Av",
			self::EREV_ROSH_HASHANA => "Erev Rosh Hashana",
			self::ROSH_HASHANA => "Rosh Hashana",
			self::FAST_OF_GEDALYAH => "Fast of Gedalyah",
			self::EREV_YOM_KIPPUR => "Erev Yom Kippur",
			self::YOM_KIPPUR => "Yom Kippur",
			self::EREV_SUCCOS => "Erev Succos",
			self::SUCCOS => "Succos",
			self::CHOL_HAMOED_SUCCOS => "Chol Hamoed Succos",
			self::HOSHANA_RABBA => "Hoshana Rabbah",
			self::SHEMINI_ATZERES => "Shemini Atzeres",
			self::SIMCHAS_TORAH => "Simchas Torah",
			self::CHANUKAH => "Chanukah",
			self::TENTH_OF_TEVES => "Tenth of Teves",
			self::TU_BESHVAT => "Tu B'Shvat",
			self::FAST_OF_ESTHER => "Fast of Esther",
			self::PURIM => "Purim",
			self::SHUSHAN_PURIM => "Shushan Purim",
			self::PURIM_KATAN => "Purim Katan",
			self::ROSH_CHODESH => "Rosh Chodesh",
			self::YOM_HASHOAH => "Yom HaShoah",
			self::YOM_HAZIKARON => "Yom Hazikaron",
			self::YOM_HAATZMAUT => "Yom Ha'atzmaut",
			self::YOM_YERUSHALAYIM => "Yom Yerushalayim",
			self::LAG_BAOMER => "Lag B'Omer",
			self::SHUSHAN_PURIM_KATAN => "Shushan Purim Katan",
			self::ISRU_CHAG => "Isru Chag",
			self::YOM_KIPPUR_KATAN => "Yom Kippur Katan",
			self::BEHAB => "BeHaB",
		};
	}
}
