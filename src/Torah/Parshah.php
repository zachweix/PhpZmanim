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

enum Parshah: int implements Nameable
{
	case NONE = 0;
	case BERESHIS = 1;
	case NOACH = 2;
	case LECH_LECHA = 3;
	case VAYERA = 4;
	case CHAYEI_SARA = 5;
	case TOLDOS = 6;
	case VAYETZEI = 7;
	case VAYISHLACH = 8;
	case VAYESHEV = 9;
	case MIKETZ = 10;
	case VAYIGASH = 11;
	case VAYECHI = 12;
	case SHEMOS = 13;
	case VAERA = 14;
	case BO = 15;
	case BESHALACH = 16;
	case YISRO = 17;
	case MISHPATIM = 18;
	case TERUMAH = 19;
	case TETZAVEH = 20;
	case KI_SISA = 21;
	case VAYAKHEL = 22;
	case PEKUDEI = 23;
	case VAYIKRA = 24;
	case TZAV = 25;
	case SHMINI = 26;
	case TAZRIA = 27;
	case METZORA = 28;
	case ACHREI_MOS = 29;
	case KEDOSHIM = 30;
	case EMOR = 31;
	case BEHAR = 32;
	case BECHUKOSAI = 33;
	case BAMIDBAR = 34;
	case NASSO = 35;
	case BEHAALOSCHA = 36;
	case SHLACH = 37;
	case KORACH = 38;
	case CHUKAS = 39;
	case BALAK = 40;
	case PINCHAS = 41;
	case MATOS = 42;
	case MASEI = 43;
	case DEVARIM = 44;
	case VAESCHANAN = 45;
	case EIKEV = 46;
	case REEH = 47;
	case SHOFTIM = 48;
	case KI_SEITZEI = 49;
	case KI_SAVO = 50;
	case NITZAVIM = 51;
	case VAYEILECH = 52;
	case HAAZINU = 53;
	case VZOS_HABERACHA = 54;
	case VAYAKHEL_PEKUDEI = 55;
	case TAZRIA_METZORA = 56;
	case ACHREI_MOS_KEDOSHIM = 57;
	case BEHAR_BECHUKOSAI = 58;
	case CHUKAS_BALAK = 59;
	case MATOS_MASEI = 60;
	case NITZAVIM_VAYEILECH = 61;
	case SHKALIM = 62;
	case ZACHOR = 63;
	case PARA = 64;
	case HACHODESH = 65;
	case SHUVA = 66;
	case SHIRA = 67;
	case HAGADOL = 68;
	case CHAZON = 69;
	case NACHAMU = 70;

	/**
	 * The Hebrew name of the parshah.
	 */
	public function hebrew(): string
	{
		return match ($this) {
			self::NONE => "",
			self::BERESHIS => "בראשית",
			self::NOACH => "נח",
			self::LECH_LECHA => "לך לך",
			self::VAYERA => "וירא",
			self::CHAYEI_SARA => "חיי שרה",
			self::TOLDOS => "תולדות",
			self::VAYETZEI => "ויצא",
			self::VAYISHLACH => "וישלח",
			self::VAYESHEV => "וישב",
			self::MIKETZ => "מקץ",
			self::VAYIGASH => "ויגש",
			self::VAYECHI => "ויחי",
			self::SHEMOS => "שמות",
			self::VAERA => "וארא",
			self::BO => "בא",
			self::BESHALACH => "בשלח",
			self::YISRO => "יתרו",
			self::MISHPATIM => "משפטים",
			self::TERUMAH => "תרומה",
			self::TETZAVEH => "תצוה",
			self::KI_SISA => "כי תשא",
			self::VAYAKHEL => "ויקהל",
			self::PEKUDEI => "פקודי",
			self::VAYIKRA => "ויקרא",
			self::TZAV => "צו",
			self::SHMINI => "שמיני",
			self::TAZRIA => "תזריע",
			self::METZORA => "מצרע",
			self::ACHREI_MOS => "אחרי מות",
			self::KEDOSHIM => "קדושים",
			self::EMOR => "אמור",
			self::BEHAR => "בהר",
			self::BECHUKOSAI => "בחקתי",
			self::BAMIDBAR => "במדבר",
			self::NASSO => "נשא",
			self::BEHAALOSCHA => "בהעלתך",
			self::SHLACH => "שלח לך",
			self::KORACH => "קרח",
			self::CHUKAS => "חוקת",
			self::BALAK => "בלק",
			self::PINCHAS => "פינחס",
			self::MATOS => "מטות",
			self::MASEI => "מסעי",
			self::DEVARIM => "דברים",
			self::VAESCHANAN => "ואתחנן",
			self::EIKEV => "עקב",
			self::REEH => "ראה",
			self::SHOFTIM => "שופטים",
			self::KI_SEITZEI => "כי תצא",
			self::KI_SAVO => "כי תבוא",
			self::NITZAVIM => "נצבים",
			self::VAYEILECH => "וילך",
			self::HAAZINU => "האזינו",
			self::VZOS_HABERACHA => "וזאת הברכה ",
			self::VAYAKHEL_PEKUDEI => "ויקהל פקודי",
			self::TAZRIA_METZORA => "תזריע מצרע",
			self::ACHREI_MOS_KEDOSHIM => "אחרי מות קדושים",
			self::BEHAR_BECHUKOSAI => "בהר בחקתי",
			self::CHUKAS_BALAK => "חוקת בלק",
			self::MATOS_MASEI => "מטות מסעי",
			self::NITZAVIM_VAYEILECH => "נצבים וילך",
			self::SHKALIM => "שקלים",
			self::ZACHOR => "זכור",
			self::PARA => "פרה",
			self::HACHODESH => "החדש",
			self::SHUVA => "שובה",
			self::SHIRA => "שירה",
			self::HAGADOL => "הגדול",
			self::CHAZON => "חזון",
			self::NACHAMU => "נחמו",
		};
	}

	/**
	 * The Ashkenazi transliterated English name of the parshah.
	 */
	public function english(): string
	{
		return match ($this) {
			self::NONE => "",
			self::BERESHIS => "Bereshis",
			self::NOACH => "Noach",
			self::LECH_LECHA => "Lech Lecha",
			self::VAYERA => "Vayera",
			self::CHAYEI_SARA => "Chayei Sara",
			self::TOLDOS => "Toldos",
			self::VAYETZEI => "Vayetzei",
			self::VAYISHLACH => "Vayishlach",
			self::VAYESHEV => "Vayeshev",
			self::MIKETZ => "Miketz",
			self::VAYIGASH => "Vayigash",
			self::VAYECHI => "Vayechi",
			self::SHEMOS => "Shemos",
			self::VAERA => "Vaera",
			self::BO => "Bo",
			self::BESHALACH => "Beshalach",
			self::YISRO => "Yisro",
			self::MISHPATIM => "Mishpatim",
			self::TERUMAH => "Terumah",
			self::TETZAVEH => "Tetzaveh",
			self::KI_SISA => "Ki Sisa",
			self::VAYAKHEL => "Vayakhel",
			self::PEKUDEI => "Pekudei",
			self::VAYIKRA => "Vayikra",
			self::TZAV => "Tzav",
			self::SHMINI => "Shmini",
			self::TAZRIA => "Tazria",
			self::METZORA => "Metzora",
			self::ACHREI_MOS => "Achrei Mos",
			self::KEDOSHIM => "Kedoshim",
			self::EMOR => "Emor",
			self::BEHAR => "Behar",
			self::BECHUKOSAI => "Bechukosai",
			self::BAMIDBAR => "Bamidbar",
			self::NASSO => "Nasso",
			self::BEHAALOSCHA => "Beha'aloscha",
			self::SHLACH => "Sh'lach",
			self::KORACH => "Korach",
			self::CHUKAS => "Chukas",
			self::BALAK => "Balak",
			self::PINCHAS => "Pinchas",
			self::MATOS => "Matos",
			self::MASEI => "Masei",
			self::DEVARIM => "Devarim",
			self::VAESCHANAN => "Vaeschanan",
			self::EIKEV => "Eikev",
			self::REEH => "Re'eh",
			self::SHOFTIM => "Shoftim",
			self::KI_SEITZEI => "Ki Seitzei",
			self::KI_SAVO => "Ki Savo",
			self::NITZAVIM => "Nitzavim",
			self::VAYEILECH => "Vayeilech",
			self::HAAZINU => "Ha'Azinu",
			self::VZOS_HABERACHA => "Vezos Habracha",
			self::VAYAKHEL_PEKUDEI => "Vayakhel Pekudei",
			self::TAZRIA_METZORA => "Tazria Metzora",
			self::ACHREI_MOS_KEDOSHIM => "Achrei Mos Kedoshim",
			self::BEHAR_BECHUKOSAI => "Behar Bechukosai",
			self::CHUKAS_BALAK => "Chukas Balak",
			self::MATOS_MASEI => "Matos Masei",
			self::NITZAVIM_VAYEILECH => "Nitzavim Vayeilech",
			self::SHKALIM => "Shekalim",
			self::ZACHOR => "Zachor",
			self::PARA => "Parah",
			self::HACHODESH => "Hachodesh",
			self::SHUVA => "Shuva",
			self::SHIRA => "Shira",
			self::HAGADOL => "Hagadol",
			self::CHAZON => "Chazon",
			self::NACHAMU => "Nachamu",
		};
	}

	/**
	 * A double dimensional array of all of the parshiyos, indexed by the year type
	 * (see JewishDate::getParshaYearType()) and then the week of the year.
	 */
	const PARSHA_LIST = [
		[self::NONE, self::VAYEILECH, self::HAAZINU, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL_PEKUDEI, self::VAYIKRA, self::TZAV, self::NONE, self::SHMINI, self::TAZRIA_METZORA, self::ACHREI_MOS_KEDOSHIM, self::EMOR, self::BEHAR_BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM_VAYEILECH],
		[self::NONE, self::VAYEILECH, self::HAAZINU, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL_PEKUDEI, self::VAYIKRA, self::TZAV, self::NONE, self::SHMINI, self::TAZRIA_METZORA, self::ACHREI_MOS_KEDOSHIM, self::EMOR, self::BEHAR_BECHUKOSAI, self::BAMIDBAR, self::NONE, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS_BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM_VAYEILECH],
		[self::NONE, self::HAAZINU, self::NONE, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL_PEKUDEI, self::VAYIKRA, self::TZAV, self::NONE, self::NONE, self::SHMINI, self::TAZRIA_METZORA, self::ACHREI_MOS_KEDOSHIM, self::EMOR, self::BEHAR_BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM],
		[self::NONE, self::HAAZINU, self::NONE, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL, self::PEKUDEI, self::VAYIKRA, self::TZAV, self::NONE, self::SHMINI, self::TAZRIA_METZORA, self::ACHREI_MOS_KEDOSHIM, self::EMOR, self::BEHAR_BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM],
		[self::NONE, self::NONE, self::HAAZINU, self::NONE, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL_PEKUDEI, self::VAYIKRA, self::TZAV, self::NONE, self::SHMINI, self::TAZRIA_METZORA, self::ACHREI_MOS_KEDOSHIM, self::EMOR, self::BEHAR_BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM],
		[self::NONE, self::NONE, self::HAAZINU, self::NONE, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL_PEKUDEI, self::VAYIKRA, self::TZAV, self::NONE, self::SHMINI, self::TAZRIA_METZORA, self::ACHREI_MOS_KEDOSHIM, self::EMOR, self::BEHAR_BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM_VAYEILECH],
		[self::NONE, self::VAYEILECH, self::HAAZINU, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL, self::PEKUDEI, self::VAYIKRA, self::TZAV, self::SHMINI, self::TAZRIA, self::METZORA, self::NONE, self::ACHREI_MOS, self::KEDOSHIM, self::EMOR, self::BEHAR, self::BECHUKOSAI, self::BAMIDBAR, self::NONE, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS_BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM_VAYEILECH],
		[self::NONE, self::VAYEILECH, self::HAAZINU, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL, self::PEKUDEI, self::VAYIKRA, self::TZAV, self::SHMINI, self::TAZRIA, self::METZORA, self::NONE, self::NONE, self::ACHREI_MOS, self::KEDOSHIM, self::EMOR, self::BEHAR, self::BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM],
		[self::NONE, self::HAAZINU, self::NONE, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL, self::PEKUDEI, self::VAYIKRA, self::TZAV, self::SHMINI, self::TAZRIA, self::METZORA, self::ACHREI_MOS, self::NONE, self::KEDOSHIM, self::EMOR, self::BEHAR, self::BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS, self::MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM],
		[self::NONE, self::HAAZINU, self::NONE, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL, self::PEKUDEI, self::VAYIKRA, self::TZAV, self::SHMINI, self::TAZRIA, self::METZORA, self::ACHREI_MOS, self::NONE, self::KEDOSHIM, self::EMOR, self::BEHAR, self::BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS, self::MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM_VAYEILECH],
		[self::NONE, self::NONE, self::HAAZINU, self::NONE, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL, self::PEKUDEI, self::VAYIKRA, self::TZAV, self::SHMINI, self::TAZRIA, self::METZORA, self::NONE, self::ACHREI_MOS, self::KEDOSHIM, self::EMOR, self::BEHAR, self::BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM_VAYEILECH],
		[self::NONE, self::NONE, self::HAAZINU, self::NONE, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL, self::PEKUDEI, self::VAYIKRA, self::TZAV, self::SHMINI, self::TAZRIA, self::METZORA, self::NONE, self::ACHREI_MOS, self::KEDOSHIM, self::EMOR, self::BEHAR, self::BECHUKOSAI, self::BAMIDBAR, self::NONE, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS_BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM_VAYEILECH],
		[self::NONE, self::VAYEILECH, self::HAAZINU, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL_PEKUDEI, self::VAYIKRA, self::TZAV, self::NONE, self::SHMINI, self::TAZRIA_METZORA, self::ACHREI_MOS_KEDOSHIM, self::EMOR, self::BEHAR_BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM_VAYEILECH],
		[self::NONE, self::HAAZINU, self::NONE, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL_PEKUDEI, self::VAYIKRA, self::TZAV, self::NONE, self::SHMINI, self::TAZRIA_METZORA, self::ACHREI_MOS_KEDOSHIM, self::EMOR, self::BEHAR, self::BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM],
		[self::NONE, self::VAYEILECH, self::HAAZINU, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL, self::PEKUDEI, self::VAYIKRA, self::TZAV, self::SHMINI, self::TAZRIA, self::METZORA, self::NONE, self::ACHREI_MOS, self::KEDOSHIM, self::EMOR, self::BEHAR, self::BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM_VAYEILECH],
		[self::NONE, self::VAYEILECH, self::HAAZINU, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL, self::PEKUDEI, self::VAYIKRA, self::TZAV, self::SHMINI, self::TAZRIA, self::METZORA, self::NONE, self::ACHREI_MOS, self::KEDOSHIM, self::EMOR, self::BEHAR, self::BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS, self::MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM],
		[self::NONE, self::NONE, self::HAAZINU, self::NONE, self::NONE, self::BERESHIS, self::NOACH, self::LECH_LECHA, self::VAYERA, self::CHAYEI_SARA, self::TOLDOS, self::VAYETZEI, self::VAYISHLACH, self::VAYESHEV, self::MIKETZ, self::VAYIGASH, self::VAYECHI, self::SHEMOS, self::VAERA, self::BO, self::BESHALACH, self::YISRO, self::MISHPATIM, self::TERUMAH, self::TETZAVEH, self::KI_SISA, self::VAYAKHEL, self::PEKUDEI, self::VAYIKRA, self::TZAV, self::SHMINI, self::TAZRIA, self::METZORA, self::NONE, self::ACHREI_MOS, self::KEDOSHIM, self::EMOR, self::BEHAR, self::BECHUKOSAI, self::BAMIDBAR, self::NASSO, self::BEHAALOSCHA, self::SHLACH, self::KORACH, self::CHUKAS, self::BALAK, self::PINCHAS, self::MATOS_MASEI, self::DEVARIM, self::VAESCHANAN, self::EIKEV, self::REEH, self::SHOFTIM, self::KI_SEITZEI, self::KI_SAVO, self::NITZAVIM_VAYEILECH]
	];
}
