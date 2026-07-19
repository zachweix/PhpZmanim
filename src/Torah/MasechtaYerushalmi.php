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

enum MasechtaYerushalmi: int implements Nameable
{
	case BERACHOS = 0;
	case PEAH = 1;
	case DEMAI = 2;
	case KILAYIM = 3;
	case SHEVIIS = 4;
	case TERUMOS = 5;
	case MAASROS = 6;
	case MAASER_SHENI = 7;
	case CHALAH = 8;
	case ORLAH = 9;
	case BIKURIM = 10;
	case SHABBOS = 11;
	case EIRUVIN = 12;
	case PESACHIM = 13;
	case BEITZAH = 14;
	case ROSH_HASHANAH = 15;
	case YOMA = 16;
	case SUKAH = 17;
	case TAANIS = 18;
	case SHEKALIM = 19;
	case MEGILAH = 20;
	case CHAGIGAH = 21;
	case MOED_KATAN = 22;
	case YEVAMOS = 23;
	case KESUVOS = 24;
	case SOTAH = 25;
	case NEDARIM = 26;
	case NAZIR = 27;
	case GITIN = 28;
	case KIDUSHIN = 29;
	case BAVA_KAMA = 30;
	case BAVA_METZIA = 31;
	case BAVA_BASRA = 32;
	case SHEVUOS = 33;
	case MAKOS = 34;
	case SANHEDRIN = 35;
	case AVODAH_ZARAH = 36;
	case HORAYOS = 37;
	case NIDAH = 38;
	case NO_DAF = 39;

	/**
	 * The Hebrew name of the masechta (NO_DAF renders "אין דף היום").
	 */
	public function hebrew(): string
	{
		return match ($this) {
			self::BERACHOS => "ברכות",
			self::PEAH => "פיאה",
			self::DEMAI => "דמאי",
			self::KILAYIM => "כלאים",
			self::SHEVIIS => "שביעית",
			self::TERUMOS => "תרומות",
			self::MAASROS => "מעשרות",
			self::MAASER_SHENI => "מעשר שני",
			self::CHALAH => "חלה",
			self::ORLAH => "עורלה",
			self::BIKURIM => "ביכורים",
			self::SHABBOS => "שבת",
			self::EIRUVIN => "עירובין",
			self::PESACHIM => "פסחים",
			self::BEITZAH => "ביצה",
			self::ROSH_HASHANAH => "ראש השנה",
			self::YOMA => "יומא",
			self::SUKAH => "סוכה",
			self::TAANIS => "תענית",
			self::SHEKALIM => "שקלים",
			self::MEGILAH => "מגילה",
			self::CHAGIGAH => "חגיגה",
			self::MOED_KATAN => "מועד קטן",
			self::YEVAMOS => "יבמות",
			self::KESUVOS => "כתובות",
			self::SOTAH => "סוטה",
			self::NEDARIM => "נדרים",
			self::NAZIR => "נזיר",
			self::GITIN => "גיטין",
			self::KIDUSHIN => "קידושין",
			self::BAVA_KAMA => "בבא קמא",
			self::BAVA_METZIA => "בבא מציעא",
			self::BAVA_BASRA => "בבא בתרא",
			self::SHEVUOS => "שבועות",
			self::MAKOS => "מכות",
			self::SANHEDRIN => "סנהדרין",
			self::AVODAH_ZARAH => "עבודה זרה",
			self::HORAYOS => "הוריות",
			self::NIDAH => "נידה",
			self::NO_DAF => "אין דף היום",
		};
	}

	/**
	 * The Ashkenazi transliterated English name of the masechta (NO_DAF renders "No Daf Today").
	 */
	public function english(): string
	{
		return match ($this) {
			self::BERACHOS => "Berachos",
			self::PEAH => "Pe'ah",
			self::DEMAI => "Demai",
			self::KILAYIM => "Kilayim",
			self::SHEVIIS => "Shevi'is",
			self::TERUMOS => "Terumos",
			self::MAASROS => "Ma'asros",
			self::MAASER_SHENI => "Ma'aser Sheni",
			self::CHALAH => "Chalah",
			self::ORLAH => "Orlah",
			self::BIKURIM => "Bikurim",
			self::SHABBOS => "Shabbos",
			self::EIRUVIN => "Eruvin",
			self::PESACHIM => "Pesachim",
			self::BEITZAH => "Beitzah",
			self::ROSH_HASHANAH => "Rosh Hashanah",
			self::YOMA => "Yoma",
			self::SUKAH => "Sukah",
			self::TAANIS => "Ta'anis",
			self::SHEKALIM => "Shekalim",
			self::MEGILAH => "Megilah",
			self::CHAGIGAH => "Chagigah",
			self::MOED_KATAN => "Moed Katan",
			self::YEVAMOS => "Yevamos",
			self::KESUVOS => "Kesuvos",
			self::SOTAH => "Sotah",
			self::NEDARIM => "Nedarim",
			self::NAZIR => "Nazir",
			self::GITIN => "Gitin",
			self::KIDUSHIN => "Kidushin",
			self::BAVA_KAMA => "Bava Kama",
			self::BAVA_METZIA => "Bava Metzia",
			self::BAVA_BASRA => "Bava Basra",
			self::SHEVUOS => "Shevuos",
			self::MAKOS => "Makos",
			self::SANHEDRIN => "Sanhedrin",
			self::AVODAH_ZARAH => "Avodah Zarah",
			self::HORAYOS => "Horayos",
			self::NIDAH => "Nidah",
			self::NO_DAF => "No Daf Today",
		};
	}
}
