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
			self::BERACHOS => "\327\221\327\250\327\233\327\225\327\252",
			self::SHABBOS => "\327\251\327\221\327\252",
			self::EIRUVIN => "\327\242\327\231\327\250\327\225\327\221\327\231\327\237",
			self::PESACHIM => "\327\244\327\241\327\227\327\231\327\235",
			self::SHEKALIM => "\327\251\327\247\327\234\327\231\327\235",
			self::YOMA => "\327\231\327\225\327\236\327\220",
			self::SUKKAH => "\327\241\327\225\327\233\327\224",
			self::BEITZAH => "\327\221\327\231\327\246\327\224",
			self::ROSH_HASHANA => "\327\250\327\220\327\251 \327\224\327\251\327\240\327\224",
			self::TAANIS => "\327\252\327\242\327\240\327\231\327\252",
			self::MEGILLAH => "\327\236\327\222\327\231\327\234\327\224",
			self::MOED_KATAN => "\327\236\327\225\327\242\327\223 \327\247\327\230\327\237",
			self::CHAGIGAH => "\327\227\327\222\327\231\327\222\327\224",
			self::YEVAMOS => "\327\231\327\221\327\236\327\225\327\252",
			self::KESUBOS => "\327\233\327\252\327\225\327\221\327\225\327\252",
			self::NEDARIM => "\327\240\327\223\327\250\327\231\327\235",
			self::NAZIR => "\327\240\327\226\327\231\327\250",
			self::SOTAH => "\327\241\327\225\327\230\327\224",
			self::GITIN => "\327\222\327\231\327\230\327\231\327\237",
			self::KIDDUSHIN => "\327\247\327\231\327\223\327\225\327\251\327\231\327\237",
			self::BAVA_KAMMA => "\327\221\327\221\327\220 \327\247\327\236\327\220",
			self::BAVA_METZIA => "\327\221\327\221\327\220 \327\236\327\246\327\231\327\242\327\220",
			self::BAVA_BASRA => "\327\221\327\221\327\220 \327\221\327\252\327\250\327\220",
			self::SANHEDRIN => "\327\241\327\240\327\224\327\223\327\250\327\231\327\237",
			self::MAKKOS => "\327\236\327\233\327\225\327\252",
			self::SHEVUOS => "\327\251\327\221\327\225\327\242\327\225\327\252",
			self::AVODAH_ZARAH => "\327\242\327\221\327\225\327\223\327\224 \327\226\327\250\327\224",
			self::HORIYOS => "\327\224\327\225\327\250\327\231\327\225\327\252",
			self::ZEVACHIM => "\327\226\327\221\327\227\327\231\327\235",
			self::MENACHOS => "\327\236\327\240\327\227\327\225\327\252",
			self::CHULLIN => "\327\227\327\225\327\234\327\231\327\237",
			self::BECHOROS => "\327\221\327\233\327\225\327\250\327\225\327\252",
			self::ARACHIN => "\327\242\327\250\327\233\327\231\327\237",
			self::TEMURAH => "\327\252\327\236\327\225\327\250\327\224",
			self::KERISOS => "\327\233\327\250\327\231\327\252\327\225\327\252",
			self::MEILAH => "\327\236\327\242\327\231\327\234\327\224",
			self::KINNIM => "\327\247\327\231\327\240\327\231\327\235",
			self::TAMID => "\327\252\327\236\327\231\327\223",
			self::MIDOS => "\327\236\327\231\327\223\327\225\327\252",
			self::NIDDAH => "\327\240\327\223\327\224",
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
