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

namespace PhpZmanim\JewishDate\Formatter;

use PhpZmanim\JewishDate;
use PhpZmanim\Torah\Nameable;
use PhpZmanim\Torah\YomTov;

class Hebrew extends LanguageFormatter
{
	const GERESH = "\327\263";
	const GERSHAYIM = "\327\264";

	const MONTHS = ["\327\240\327\231\327\241\327\237", "\327\220\327\231\327\231\327\250",
		"\327\241\327\231\327\225\327\237", "\327\252\327\236\327\225\327\226", "\327\220\327\221", "\327\220\327\234\327\225\327\234",
		"\327\252\327\251\327\250\327\231", "\327\227\327\251\327\225\327\237", "\327\233\327\241\327\234\327\225",
		"\327\230\327\221\327\252", "\327\251\327\221\327\230", "\327\220\327\223\327\250", "\327\220\327\223\327\250 \327\221",
		"\327\220\327\223\327\250 \327\220"];

	const DAYS_OF_WEEK = ["\327\250\327\220\327\251\327\225\327\237", "\327\251\327\240\327\231",
		"\327\251\327\234\327\231\327\251\327\231", "\327\250\327\221\327\231\327\242\327\231", "\327\227\327\236\327\231\327\251\327\231",
		"\327\251\327\251\327\231", "\327\251\327\221\327\252"];

	const OMER_PREFIX = "\327\221";

	public function month(): string
	{
		$month = $this->date->getJewishMonth();
		$isLeapYear = $this->date->isJewishLeapYear();

		if ($isLeapYear && $month == JewishDate::ADAR) {
			return self::MONTHS[13] . self::GERESH; // Adar I, not Adar, in a leap year
		}
		if ($isLeapYear && $month == JewishDate::ADAR_II) {
			return self::MONTHS[12] . self::GERESH;
		}

		return self::MONTHS[$month - 1];
	}

	public function dayOfWeek(): string
	{
		return self::DAYS_OF_WEEK[$this->date->getDayOfWeek() - 1];
	}

	/**
	 * The kviah (year type) of the date's Jewish year. Hebrew only.
	 */
	public function kviah(): string
	{
		$year = $this->date->getJewishYear();
		$roshHashana = new JewishDate($year, JewishDate::TISHREI, 1);
		$kviah = $roshHashana->getCheshvanKislevKviah();
		$roshHashanaDayOfWeek = $roshHashana->getDayOfWeek();

		$pesachDayOfWeek = $roshHashanaDayOfWeek + 1;
		if ($kviah == JewishDate::KESIDRAN) {
			$pesachDayOfWeek++;
		} elseif ($kviah == JewishDate::SHELAIMIM) {
			$pesachDayOfWeek += 2;
		}
		if ($roshHashana->isJewishLeapYear()) {
			$pesachDayOfWeek += 2;
		}
		if ($pesachDayOfWeek > 7) {
			$pesachDayOfWeek -= 7;
		}

		$returnValue = $this->hebrewNumber($roshHashanaDayOfWeek);
		$returnValue .= ($kviah == JewishDate::CHASERIM ? "\327\227" : ($kviah == JewishDate::SHELAIMIM ? "\327\251" : "\327\233"));
		$returnValue .= $this->hebrewNumber($pesachDayOfWeek);

		return str_replace(self::GERESH, "", $returnValue);
	}

	protected function name(Nameable $value): string
	{
		return $value->hebrew();
	}

	protected function formatNumber(int $number): string
	{
		return $this->hebrewNumber($number);
	}

	protected function dateYearSeparator(): string
	{
		return " ";
	}

	protected function chanukah(YomTov $yomTov, int $dayOfChanukah): string
	{
		return $this->hebrewNumber($dayOfChanukah) . " " . $yomTov->hebrew();
	}

	protected function formatOmer(int $omer): string
	{
		return $this->hebrewNumber($omer) . " " . self::OMER_PREFIX . "\327\242\327\225\327\236\327\250";
	}

	/**
	 * Render a number (0-9999) as a Hebrew numeral, e.g. 5771 => תשע״א. Uses gershayim
	 * marks, short years (no thousands prefix) and non-final-form letters.
	 */
	private function hebrewNumber(int $number): string
	{
		if ($number < 0) {
			throw new \InvalidArgumentException("negative numbers can't be formatted");
		}
		if ($number > 9999) {
			throw new \InvalidArgumentException("numbers > 9999 can't be formatted");
		}

		$alafim = "\327\220\327\234\327\244\327\231\327\235";
		$efes = "\327\220\327\244\327\241";

		$jHundreds = ["", "\327\247", "\327\250", "\327\251", "\327\252", "\327\252\327\247", "\327\252\327\250",
			"\327\252\327\251", "\327\252\327\252", "\327\252\327\252\327\247"];
		$jTens = ["", "\327\231", "\327\233", "\327\234", "\327\236", "\327\240", "\327\241", "\327\242",
			"\327\244", "\327\246"];
		$tavTaz = ["\327\230\327\225", "\327\230\327\226"];
		$jOnes = ["", "\327\220", "\327\221", "\327\222", "\327\223", "\327\224", "\327\225", "\327\226",
			"\327\227", "\327\230"];

		if ($number == 0) {
			return $efes;
		}

		$shortNumber = $number % 1000;
		$singleDigitNumber = ($shortNumber < 11 || ($shortNumber < 100 && $shortNumber % 10 == 0) || ($shortNumber <= 400 && $shortNumber % 100 == 0));
		$thousands = intdiv($number, 1000);

		if ($number % 1000 == 0) {
			return $jOnes[$thousands] . self::GERESH . " " . $alafim;
		}

		$number = $number % 1000;
		$hundreds = intdiv($number, 100);
		$formatted = $jHundreds[$hundreds];

		$number = $number % 100;
		if ($number == 15) {
			$formatted .= $tavTaz[0];
		} elseif ($number == 16) {
			$formatted .= $tavTaz[1];
		} else {
			$tens = intdiv($number, 10);
			$formatted .= $jTens[$tens];
			if ($number % 10 != 0) {
				$formatted .= $jOnes[$number % 10];
			}
		}

		if ($singleDigitNumber) {
			$formatted .= self::GERESH;
		} else {
			$formatted = substr_replace($formatted, self::GERSHAYIM, strlen($formatted) - 2, 0);
		}

		return $formatted;
	}
}
