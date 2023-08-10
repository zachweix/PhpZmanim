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

class Parsha {
	/*
	|--------------------------------------------------------------------------
	| CLASS CONSTANTS
	|--------------------------------------------------------------------------
	*/

	const NONE = 0;
	const BERESHIS = 1;
	const NOACH = 2;
	const LECH_LECHA = 3;
	const VAYERA = 4;
	const CHAYEI_SARA = 5;
	const TOLDOS = 6;
	const VAYETZEI = 7;
	const VAYISHLACH = 8;
	const VAYESHEV = 9;
	const MIKETZ = 10;
	const VAYIGASH = 11;
	const VAYECHI = 12;
	const SHEMOS = 13;
	const VAERA = 14;
	const BO = 15;
	const BESHALACH = 16;
	const YISRO = 17;
	const MISHPATIM = 18;
	const TERUMAH = 19;
	const TETZAVEH = 21;
	const KI_SISA = 22;
	const VAYAKHEL = 23;
	const PEKUDEI = 24;
	const VAYIKRA = 25;
	const TZAV = 26;
	const SHMINI = 27;
	const TAZRIA = 28;
	const METZORA = 29;
	const ACHREI_MOS = 30;
	const KEDOSHIM = 31;
	const EMOR = 32;
	const BEHAR = 33;
	const BECHUKOSAI = 34;
	const BAMIDBAR = 35;
	const NASSO = 36;
	const BEHAALOSCHA = 37;
	const SHLACH = 38;
	const KORACH = 39;
	const CHUKAS = 40;
	const BALAK = 41;
	const PINCHAS = 42;
	const MATOS = 43;
	const MASEI = 44;
	const DEVARIM = 45;
	const VAESCHANAN = 46;
	const EIKEV = 47;
	const REEH = 48;
	const SHOFTIM = 49;
	const KI_SEITZEI = 50;
	const KI_SAVO = 51;
	const NITZAVIM = 52;
	const VAYEILECH = 53;
	const HAAZINU = 54;
	const VZOS_HABERACHA = 55;
	const VAYAKHEL_PEKUDEI = 56;
	const TAZRIA_METZORA = 57;
	const ACHREI_MOS_KEDOSHIM = 58;
	const BEHAR_BECHUKOSAI = 59;
	const CHUKAS_BALAK = 60;
	const MATOS_MASEI = 61;
	const NITZAVIM_VAYEILECH = 62;
	const SHKALIM = 63;
	const ZACHOR = 64;
	const PARA = 65;
	const HACHODESH = 66;
	const SHUVA = 67;
	const SHIRA = 68;
	const HAGADOL = 69;
	const CHAZON = 70;
	const NACHAMU = 71;

	const PARSHA_LIST = [
		[Parsha::NONE, Parsha::VAYEILECH, Parsha::HAAZINU, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL_PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::NONE, Parsha::SHMINI, Parsha::TAZRIA_METZORA, Parsha::ACHREI_MOS_KEDOSHIM, Parsha::EMOR, Parsha::BEHAR_BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM_VAYEILECH],
		[Parsha::NONE, Parsha::VAYEILECH, Parsha::HAAZINU, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL_PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::NONE, Parsha::SHMINI, Parsha::TAZRIA_METZORA, Parsha::ACHREI_MOS_KEDOSHIM, Parsha::EMOR, Parsha::BEHAR_BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NONE, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS_BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM_VAYEILECH],
		[Parsha::NONE, Parsha::HAAZINU, Parsha::NONE, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL_PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::NONE, Parsha::NONE, Parsha::SHMINI, Parsha::TAZRIA_METZORA, Parsha::ACHREI_MOS_KEDOSHIM, Parsha::EMOR, Parsha::BEHAR_BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM],
		[Parsha::NONE, Parsha::HAAZINU, Parsha::NONE, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL, Parsha::PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::NONE, Parsha::SHMINI, Parsha::TAZRIA_METZORA, Parsha::ACHREI_MOS_KEDOSHIM, Parsha::EMOR, Parsha::BEHAR_BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM],
		[Parsha::NONE, Parsha::NONE, Parsha::HAAZINU, Parsha::NONE, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL_PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::NONE, Parsha::SHMINI, Parsha::TAZRIA_METZORA, Parsha::ACHREI_MOS_KEDOSHIM, Parsha::EMOR, Parsha::BEHAR_BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM],
		[Parsha::NONE, Parsha::NONE, Parsha::HAAZINU, Parsha::NONE, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL_PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::NONE, Parsha::SHMINI, Parsha::TAZRIA_METZORA, Parsha::ACHREI_MOS_KEDOSHIM, Parsha::EMOR, Parsha::BEHAR_BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM_VAYEILECH],
		[Parsha::NONE, Parsha::VAYEILECH, Parsha::HAAZINU, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL, Parsha::PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::SHMINI, Parsha::TAZRIA, Parsha::METZORA, Parsha::NONE, Parsha::ACHREI_MOS, Parsha::KEDOSHIM, Parsha::EMOR, Parsha::BEHAR, Parsha::BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NONE, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS_BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM_VAYEILECH],
		[Parsha::NONE, Parsha::VAYEILECH, Parsha::HAAZINU, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL, Parsha::PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::SHMINI, Parsha::TAZRIA, Parsha::METZORA, Parsha::NONE, Parsha::NONE, Parsha::ACHREI_MOS, Parsha::KEDOSHIM, Parsha::EMOR, Parsha::BEHAR, Parsha::BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM],
		[Parsha::NONE, Parsha::HAAZINU, Parsha::NONE, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL, Parsha::PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::SHMINI, Parsha::TAZRIA, Parsha::METZORA, Parsha::ACHREI_MOS, Parsha::NONE, Parsha::KEDOSHIM, Parsha::EMOR, Parsha::BEHAR, Parsha::BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS, Parsha::MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM],
		[Parsha::NONE, Parsha::HAAZINU, Parsha::NONE, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL, Parsha::PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::SHMINI, Parsha::TAZRIA, Parsha::METZORA, Parsha::ACHREI_MOS, Parsha::NONE, Parsha::KEDOSHIM, Parsha::EMOR, Parsha::BEHAR, Parsha::BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS, Parsha::MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM_VAYEILECH],
		[Parsha::NONE, Parsha::NONE, Parsha::HAAZINU, Parsha::NONE, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL, Parsha::PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::SHMINI, Parsha::TAZRIA, Parsha::METZORA, Parsha::NONE, Parsha::ACHREI_MOS, Parsha::KEDOSHIM, Parsha::EMOR, Parsha::BEHAR, Parsha::BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM_VAYEILECH],
		[Parsha::NONE, Parsha::NONE, Parsha::HAAZINU, Parsha::NONE, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL, Parsha::PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::SHMINI, Parsha::TAZRIA, Parsha::METZORA, Parsha::NONE, Parsha::ACHREI_MOS, Parsha::KEDOSHIM, Parsha::EMOR, Parsha::BEHAR, Parsha::BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NONE, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS_BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM_VAYEILECH],
		[Parsha::NONE, Parsha::VAYEILECH, Parsha::HAAZINU, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL_PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::NONE, Parsha::SHMINI, Parsha::TAZRIA_METZORA, Parsha::ACHREI_MOS_KEDOSHIM, Parsha::EMOR, Parsha::BEHAR_BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM_VAYEILECH],
		[Parsha::NONE, Parsha::HAAZINU, Parsha::NONE, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL_PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::NONE, Parsha::SHMINI, Parsha::TAZRIA_METZORA, Parsha::ACHREI_MOS_KEDOSHIM, Parsha::EMOR, Parsha::BEHAR, Parsha::BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM],
		[Parsha::NONE, Parsha::VAYEILECH, Parsha::HAAZINU, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL, Parsha::PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::SHMINI, Parsha::TAZRIA, Parsha::METZORA, Parsha::NONE, Parsha::ACHREI_MOS, Parsha::KEDOSHIM, Parsha::EMOR, Parsha::BEHAR, Parsha::BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM_VAYEILECH],
		[Parsha::NONE, Parsha::VAYEILECH, Parsha::HAAZINU, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL, Parsha::PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::SHMINI, Parsha::TAZRIA, Parsha::METZORA, Parsha::NONE, Parsha::ACHREI_MOS, Parsha::KEDOSHIM, Parsha::EMOR, Parsha::BEHAR, Parsha::BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS, Parsha::MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM],
		[Parsha::NONE, Parsha::NONE, Parsha::HAAZINU, Parsha::NONE, Parsha::NONE, Parsha::BERESHIS, Parsha::NOACH, Parsha::LECH_LECHA, Parsha::VAYERA, Parsha::CHAYEI_SARA, Parsha::TOLDOS, Parsha::VAYETZEI, Parsha::VAYISHLACH, Parsha::VAYESHEV, Parsha::MIKETZ, Parsha::VAYIGASH, Parsha::VAYECHI, Parsha::SHEMOS, Parsha::VAERA, Parsha::BO, Parsha::BESHALACH, Parsha::YISRO, Parsha::MISHPATIM, Parsha::TERUMAH, Parsha::TETZAVEH, Parsha::KI_SISA, Parsha::VAYAKHEL, Parsha::PEKUDEI, Parsha::VAYIKRA, Parsha::TZAV, Parsha::SHMINI, Parsha::TAZRIA, Parsha::METZORA, Parsha::NONE, Parsha::ACHREI_MOS, Parsha::KEDOSHIM, Parsha::EMOR, Parsha::BEHAR, Parsha::BECHUKOSAI, Parsha::BAMIDBAR, Parsha::NASSO, Parsha::BEHAALOSCHA, Parsha::SHLACH, Parsha::KORACH, Parsha::CHUKAS, Parsha::BALAK, Parsha::PINCHAS, Parsha::MATOS_MASEI, Parsha::DEVARIM, Parsha::VAESCHANAN, Parsha::EIKEV, Parsha::REEH, Parsha::SHOFTIM, Parsha::KI_SEITZEI, Parsha::KI_SAVO, Parsha::NITZAVIM_VAYEILECH]
	];
}