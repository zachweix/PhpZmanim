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

enum MasechtaBavli: int implements Nameable
{
	case BERACHOS = 0;
	case SHABBOS = 1;
	case EIRUVIN = 2;
	case PESACHIM = 3;
	case SHEKALIM = 4;
	case YOMA = 5;
	case SUKKAH = 6;
	case BEITZAH = 7;
	case ROSH_HASHANA = 8;
	case TAANIS = 9;
	case MEGILLAH = 10;
	case MOED_KATAN = 11;
	case CHAGIGAH = 12;
	case YEVAMOS = 13;
	case KESUBOS = 14;
	case NEDARIM = 15;
	case NAZIR = 16;
	case SOTAH = 17;
	case GITIN = 18;
	case KIDDUSHIN = 19;
	case BAVA_KAMMA = 20;
	case BAVA_METZIA = 21;
	case BAVA_BASRA = 22;
	case SANHEDRIN = 23;
	case MAKKOS = 24;
	case SHEVUOS = 25;
	case AVODAH_ZARAH = 26;
	case HORIYOS = 27;
	case ZEVACHIM = 28;
	case MENACHOS = 29;
	case CHULLIN = 30;
	case BECHOROS = 31;
	case ARACHIN = 32;
	case TEMURAH = 33;
	case KERISOS = 34;
	case MEILAH = 35;
	case KINNIM = 36;
	case TAMID = 37;
	case MIDOS = 38;
	case NIDDAH = 39;

	/**
	 * The Hebrew name of the masechta.
	 */
	public function hebrew(): string
	{
		return match ($this) {
			self::BERACHOS => "ברכות",
			self::SHABBOS => "שבת",
			self::EIRUVIN => "עירובין",
			self::PESACHIM => "פסחים",
			self::SHEKALIM => "שקלים",
			self::YOMA => "יומא",
			self::SUKKAH => "סוכה",
			self::BEITZAH => "ביצה",
			self::ROSH_HASHANA => "ראש השנה",
			self::TAANIS => "תענית",
			self::MEGILLAH => "מגילה",
			self::MOED_KATAN => "מועד קטן",
			self::CHAGIGAH => "חגיגה",
			self::YEVAMOS => "יבמות",
			self::KESUBOS => "כתובות",
			self::NEDARIM => "נדרים",
			self::NAZIR => "נזיר",
			self::SOTAH => "סוטה",
			self::GITIN => "גיטין",
			self::KIDDUSHIN => "קידושין",
			self::BAVA_KAMMA => "בבא קמא",
			self::BAVA_METZIA => "בבא מציעא",
			self::BAVA_BASRA => "בבא בתרא",
			self::SANHEDRIN => "סנהדרין",
			self::MAKKOS => "מכות",
			self::SHEVUOS => "שבועות",
			self::AVODAH_ZARAH => "עבודה זרה",
			self::HORIYOS => "הוריות",
			self::ZEVACHIM => "זבחים",
			self::MENACHOS => "מנחות",
			self::CHULLIN => "חולין",
			self::BECHOROS => "בכורות",
			self::ARACHIN => "ערכין",
			self::TEMURAH => "תמורה",
			self::KERISOS => "כריתות",
			self::MEILAH => "מעילה",
			self::KINNIM => "קינים",
			self::TAMID => "תמיד",
			self::MIDOS => "מידות",
			self::NIDDAH => "נדה",
		};
	}

	/**
	 * The Ashkenazi transliterated English name of the masechta.
	 */
	public function english(): string
	{
		return match ($this) {
			self::BERACHOS => "Berachos",
			self::SHABBOS => "Shabbos",
			self::EIRUVIN => "Eruvin",
			self::PESACHIM => "Pesachim",
			self::SHEKALIM => "Shekalim",
			self::YOMA => "Yoma",
			self::SUKKAH => "Sukkah",
			self::BEITZAH => "Beitzah",
			self::ROSH_HASHANA => "Rosh Hashana",
			self::TAANIS => "Taanis",
			self::MEGILLAH => "Megillah",
			self::MOED_KATAN => "Moed Katan",
			self::CHAGIGAH => "Chagigah",
			self::YEVAMOS => "Yevamos",
			self::KESUBOS => "Kesubos",
			self::NEDARIM => "Nedarim",
			self::NAZIR => "Nazir",
			self::SOTAH => "Sotah",
			self::GITIN => "Gitin",
			self::KIDDUSHIN => "Kiddushin",
			self::BAVA_KAMMA => "Bava Kamma",
			self::BAVA_METZIA => "Bava Metzia",
			self::BAVA_BASRA => "Bava Basra",
			self::SANHEDRIN => "Sanhedrin",
			self::MAKKOS => "Makkos",
			self::SHEVUOS => "Shevuos",
			self::AVODAH_ZARAH => "Avodah Zarah",
			self::HORIYOS => "Horiyos",
			self::ZEVACHIM => "Zevachim",
			self::MENACHOS => "Menachos",
			self::CHULLIN => "Chullin",
			self::BECHOROS => "Bechoros",
			self::ARACHIN => "Arachin",
			self::TEMURAH => "Temurah",
			self::KERISOS => "Kerisos",
			self::MEILAH => "Meilah",
			self::KINNIM => "Kinnim",
			self::TAMID => "Tamid",
			self::MIDOS => "Midos",
			self::NIDDAH => "Niddah",
		};
	}
}
