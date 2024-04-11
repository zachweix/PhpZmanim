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

	public static $masechtosBavli = ["\327\221\327\250\327\233\327\225\327\252", "\327\251\327\221\327\252",
			"\327\242\327\231\327\250\327\225\327\221\327\231\327\237", "\327\244\327\241\327\227\327\231\327\235",
			"\327\251\327\247\327\234\327\231\327\235", "\327\231\327\225\327\236\327\220", "\327\241\327\225\327\233\327\224",
			"\327\221\327\231\327\246\327\224", "\327\250\327\220\327\251 \327\224\327\251\327\240\327\224",
			"\327\252\327\242\327\240\327\231\327\252", "\327\236\327\222\327\231\327\234\327\224",
			"\327\236\327\225\327\242\327\223 \327\247\327\230\327\237", "\327\227\327\222\327\231\327\222\327\224",
			"\327\231\327\221\327\236\327\225\327\252", "\327\233\327\252\327\225\327\221\327\225\327\252", "\327\240\327\223\327\250\327\231\327\235",
			"\327\240\327\226\327\231\327\250", "\327\241\327\225\327\230\327\224", "\327\222\327\231\327\230\327\231\327\237",
			"\327\247\327\231\327\223\327\225\327\251\327\231\327\237", "\327\221\327\221\327\220 \327\247\327\236\327\220",
			"\327\221\327\221\327\220 \327\236\327\246\327\231\327\242\327\220", "\327\221\327\221\327\220 \327\221\327\252\327\250\327\220",
			"\327\241\327\240\327\224\327\223\327\250\327\231\327\237", "\327\236\327\233\327\225\327\252",
			"\327\251\327\221\327\225\327\242\327\225\327\252", "\327\242\327\221\327\225\327\223\327\224 \327\226\327\250\327\224",
			"\327\224\327\225\327\250\327\231\327\225\327\252", "\327\226\327\221\327\227\327\231\327\235", "\327\236\327\240\327\227\327\225\327\252",
			"\327\227\327\225\327\234\327\231\327\237", "\327\221\327\233\327\225\327\250\327\225\327\252", "\327\242\327\250\327\233\327\231\327\237",
			"\327\252\327\236\327\225\327\250\327\224", "\327\233\327\250\327\231\327\252\327\225\327\252", "\327\236\327\242\327\231\327\234\327\224",
			"\327\247\327\231\327\240\327\231\327\235", "\327\252\327\236\327\231\327\223", "\327\236\327\231\327\223\327\225\327\252",
			"\327\240\327\223\327\224"];
	
	public static $masechtosYerushalmiTransliterated = ["Berachos", "Pe'ah", "Demai", "Kilayim", "Shevi'is",
			"Terumos", "Ma'asros", "Ma'aser Sheni", "Chalah", "Orlah", "Bikurim", "Shabbos", "Eruvin", "Pesachim",
			"Beitzah", "Rosh Hashanah", "Yoma", "Sukah", "Ta'anis", "Shekalim", "Megilah", "Chagigah", "Moed Katan",
			"Yevamos", "Kesuvos", "Sotah", "Nedarim", "Nazir", "Gitin", "Kidushin", "Bava Kama", "Bava Metzia",
			"Bava Basra", "Shevuos", "Makos", "Sanhedrin", "Avodah Zarah", "Horayos", "Nidah", "No Daf Today"];
	
	public static $masechtosYerushalmi = ["\327\221\327\250\327\233\327\225\327\252","\327\244\327\231\327\220\327\224",
			"\327\223\327\236\327\220\327\231","\327\233\327\234\327\220\327\231\327\235","\327\251\327\221\327\231\327\242\327\231\327\252",
			"\327\252\327\250\327\225\327\236\327\225\327\252","\327\236\327\242\327\251\327\250\327\225\327\252","\327\236\327\242\327\251\327\250 \327\251\327\240\327\231",
			"\327\227\327\234\327\224","\327\242\327\225\327\250\327\234\327\224","\327\221\327\231\327\233\327\225\327\250\327\231\327\235",
			"\327\251\327\221\327\252","\327\242\327\231\327\250\327\225\327\221\327\231\327\237","\327\244\327\241\327\227\327\231\327\235",
			"\327\221\327\231\327\246\327\224","\327\250\327\220\327\251 \327\224\327\251\327\240\327\224","\327\231\327\225\327\236\327\220",
			"\327\241\327\225\327\233\327\224","\327\252\327\242\327\240\327\231\327\252","\327\251\327\247\327\234\327\231\327\235","\327\236\327\222\327\231\327\234\327\224",
			"\327\227\327\222\327\231\327\222\327\224","\327\236\327\225\327\242\327\223 \327\247\327\230\327\237","\327\231\327\221\327\236\327\225\327\252",
			"\327\233\327\252\327\225\327\221\327\225\327\252","\327\241\327\225\327\230\327\224","\327\240\327\223\327\250\327\231\327\235","\327\240\327\226\327\231\327\250",
			"\327\222\327\231\327\230\327\231\327\237","\327\247\327\231\327\223\327\225\327\251\327\231\327\237","\327\221\327\221\327\220 \327\247\327\236\327\220",
			"\327\221\327\221\327\220 \327\236\327\246\327\231\327\242\327\220","\327\221\327\221\327\220 \327\221\327\252\327\250\327\220",
			"\327\251\327\221\327\225\327\242\327\225\327\252","\327\236\327\233\327\225\327\252","\327\241\327\240\327\224\327\223\327\250\327\231\327\237",
			"\327\242\327\221\327\225\327\223\327\224 \327\226\327\250\327\224","\327\224\327\225\327\250\327\231\327\225\327\252","\327\240\327\231\327\223\327\224",
			"\327\220\327\231\327\237 \327\223\327\243 \327\224\327\231\327\225\327\235"];

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
