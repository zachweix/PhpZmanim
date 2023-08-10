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

class Daf {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private $masechtaNumber;
	private $daf;

	public static $masechtosBavliTransliterated = ["Berachos", "Shabbos", "Eruvin", "Pesachim", "Shekalim",
			"Yoma", "Sukkah", "Beitzah", "Rosh Hashana", "Taanis", "Megillah", "Moed Katan", "Chagigah", "Yevamos",
			"Kesubos", "Nedarim", "Nazir", "Sotah", "Gitin", "Kiddushin", "Bava Kamma", "Bava Metzia", "Bava Basra",
			"Sanhedrin", "Makkos", "Shevuos", "Avodah Zarah", "Horiyos", "Zevachim", "Menachos", "Chullin", "Bechoros",
			"Arachin", "Temurah", "Kerisos", "Meilah", "Kinnim", "Tamid", "Midos", "Niddah"];

	public static $masechtosBavli = ["\u05D1\u05E8\u05DB\u05D5\u05EA", "\u05E9\u05D1\u05EA",
			"\u05E2\u05D9\u05E8\u05D5\u05D1\u05D9\u05DF", "\u05E4\u05E1\u05D7\u05D9\u05DD",
			"\u05E9\u05E7\u05DC\u05D9\u05DD", "\u05D9\u05D5\u05DE\u05D0", "\u05E1\u05D5\u05DB\u05D4",
			"\u05D1\u05D9\u05E6\u05D4", "\u05E8\u05D0\u05E9 \u05D4\u05E9\u05E0\u05D4",
			"\u05EA\u05E2\u05E0\u05D9\u05EA", "\u05DE\u05D2\u05D9\u05DC\u05D4",
			"\u05DE\u05D5\u05E2\u05D3 \u05E7\u05D8\u05DF", "\u05D7\u05D2\u05D9\u05D2\u05D4",
			"\u05D9\u05D1\u05DE\u05D5\u05EA", "\u05DB\u05EA\u05D5\u05D1\u05D5\u05EA", "\u05E0\u05D3\u05E8\u05D9\u05DD",
			"\u05E0\u05D6\u05D9\u05E8", "\u05E1\u05D5\u05D8\u05D4", "\u05D2\u05D9\u05D8\u05D9\u05DF",
			"\u05E7\u05D9\u05D3\u05D5\u05E9\u05D9\u05DF", "\u05D1\u05D1\u05D0 \u05E7\u05DE\u05D0",
			"\u05D1\u05D1\u05D0 \u05DE\u05E6\u05D9\u05E2\u05D0", "\u05D1\u05D1\u05D0 \u05D1\u05EA\u05E8\u05D0",
			"\u05E1\u05E0\u05D4\u05D3\u05E8\u05D9\u05DF", "\u05DE\u05DB\u05D5\u05EA",
			"\u05E9\u05D1\u05D5\u05E2\u05D5\u05EA", "\u05E2\u05D1\u05D5\u05D3\u05D4 \u05D6\u05E8\u05D4",
			"\u05D4\u05D5\u05E8\u05D9\u05D5\u05EA", "\u05D6\u05D1\u05D7\u05D9\u05DD", "\u05DE\u05E0\u05D7\u05D5\u05EA",
			"\u05D7\u05D5\u05DC\u05D9\u05DF", "\u05D1\u05DB\u05D5\u05E8\u05D5\u05EA", "\u05E2\u05E8\u05DB\u05D9\u05DF",
			"\u05EA\u05DE\u05D5\u05E8\u05D4", "\u05DB\u05E8\u05D9\u05EA\u05D5\u05EA", "\u05DE\u05E2\u05D9\u05DC\u05D4",
			"\u05E7\u05D9\u05E0\u05D9\u05DD", "\u05EA\u05DE\u05D9\u05D3", "\u05DE\u05D9\u05D3\u05D5\u05EA",
			"\u05E0\u05D3\u05D4"];
	
	public static $masechtosYerushalmiTransliterated = ["Berachos", "Pe'ah", "Demai", "Kilayim", "Shevi'is",
			"Terumos", "Ma'asros", "Ma'aser Sheni", "Chalah", "Orlah", "Bikurim", "Shabbos", "Eruvin", "Pesachim",
			"Beitzah", "Rosh Hashanah", "Yoma", "Sukah", "Ta'anis", "Shekalim", "Megilah", "Chagigah", "Moed Katan",
			"Yevamos", "Kesuvos", "Sotah", "Nedarim", "Nazir", "Gitin", "Kidushin", "Bava Kama", "Bava Metzia",
			"Bava Basra", "Shevuos", "Makos", "Sanhedrin", "Avodah Zarah", "Horayos", "Nidah", "No Daf Today"];
	
	public static $masechtosYerushalmi = ["\u05d1\u05e8\u05db\u05d5\u05ea","\u05e4\u05d9\u05d0\u05d4",
			"\u05d3\u05de\u05d0\u05d9","\u05db\u05dc\u05d0\u05d9\u05dd","\u05e9\u05d1\u05d9\u05e2\u05d9\u05ea",
			"\u05ea\u05e8\u05d5\u05de\u05d5\u05ea","\u05de\u05e2\u05e9\u05e8\u05d5\u05ea","\u05de\u05e2\u05e9\u05e8 \u05e9\u05e0\u05d9",
			"\u05d7\u05dc\u05d4","\u05e2\u05d5\u05e8\u05dc\u05d4","\u05d1\u05d9\u05db\u05d5\u05e8\u05d9\u05dd",
			"\u05e9\u05d1\u05ea","\u05e2\u05d9\u05e8\u05d5\u05d1\u05d9\u05df","\u05e4\u05e1\u05d7\u05d9\u05dd",
			"\u05d1\u05d9\u05e6\u05d4","\u05e8\u05d0\u05e9 \u05d4\u05e9\u05e0\u05d4","\u05d9\u05d5\u05de\u05d0",
			"\u05e1\u05d5\u05db\u05d4","\u05ea\u05e2\u05e0\u05d9\u05ea","\u05e9\u05e7\u05dc\u05d9\u05dd","\u05de\u05d2\u05d9\u05dc\u05d4",
			"\u05d7\u05d2\u05d9\u05d2\u05d4","\u05de\u05d5\u05e2\u05d3 \u05e7\u05d8\u05df","\u05d9\u05d1\u05de\u05d5\u05ea",
			"\u05db\u05ea\u05d5\u05d1\u05d5\u05ea","\u05e1\u05d5\u05d8\u05d4","\u05e0\u05d3\u05e8\u05d9\u05dd","\u05e0\u05d6\u05d9\u05e8",
			"\u05d2\u05d9\u05d8\u05d9\u05df","\u05e7\u05d9\u05d3\u05d5\u05e9\u05d9\u05df","\u05d1\u05d1\u05d0 \u05e7\u05de\u05d0",
			"\u05d1\u05d1\u05d0 \u05de\u05e6\u05d9\u05e2\u05d0","\u05d1\u05d1\u05d0 \u05d1\u05ea\u05e8\u05d0",
			"\u05e9\u05d1\u05d5\u05e2\u05d5\u05ea","\u05de\u05db\u05d5\u05ea","\u05e1\u05e0\u05d4\u05d3\u05e8\u05d9\u05df",
			"\u05e2\u05d1\u05d5\u05d3\u05d4 \u05d6\u05e8\u05d4","\u05d4\u05d5\u05e8\u05d9\u05d5\u05ea","\u05e0\u05d9\u05d3\u05d4",
			"\u05d0\u05d9\u05df \u05d3\u05e3 \u05d4\u05d9\u05d5\u05dd"];

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTOR
	|--------------------------------------------------------------------------
	*/

	public function __construct($masechtaNumber, $daf) {
		$this->masechtaNumber = $masechtaNumber;
		$this->daf = $daf;
	}

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function getMasechtaNumber() {
		return $this->masechtaNumber;
	}

	public function setMasechtaNumber($masechtaNumber) {
		$this->masechtaNumber = $masechtaNumber;

		return $this;
	}

	public function getDaf() {
		return $this->daf;
	}

	public function setDaf($daf) {
		$this->daf = $daf;

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| BAVLI MASECHTA INFORMATION
	|--------------------------------------------------------------------------
	*/

	public function getMasechtaTransliterated() {
		return self::$masechtosBavliTransliterated[$this->masechtaNumber];
	}

	public function setMasechtaTransliterated($masechtosBavliTransliterated) {
		self::$masechtosBavliTransliterated = $masechtosBavliTransliterated;
	}

	public function getMasechta() {
		return self::$masechtosBavli[$this->masechtaNumber];
	}

	/*
	|--------------------------------------------------------------------------
	| YERUSHALMI MASECHTA INFORMATION
	|--------------------------------------------------------------------------
	*/

	public function getYerushalmiMasechtaTransliterated() {
		return self::$masechtosYerushalmiTransliterated[$this->masechtaNumber];
	}

	public function setYerushalmiMasechtaTransliterated($masechtosYerushalmiTransliterated) {
		self::$masechtosYerushalmiTransliterated = $masechtosYerushalmiTransliterated;
	}

	public static function getYerushalmiMasechtosTransliterated() {
		return self::$masechtosYerushalmiTransliterated;
	}

	public static function getYerushalmiMasechtos() {
		return self::$masechtosYerushalmi;
	}

	public function getYerushalmiMasechta() {
		return self::$masechtosYerushalmi[$this->masechtaNumber];
	}
}
