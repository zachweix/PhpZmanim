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

namespace PhpZmanim\JewishDate\Formatter;

use PhpZmanim\JewishDate;
use PhpZmanim\Torah\Nameable;
use PhpZmanim\Torah\YomTov;

class Hebrew extends LanguageFormatter
{
	const GERESH = "׳";
	const GERSHAYIM = "״";

	const MONTHS = ["ניסן", "אייר", "סיון", "תמוז", "אב", "אלול", "תשרי", "חשון",
		"כסלו", "טבת", "שבט", "אדר", "אדר ב", "אדר א"];

	const DAYS_OF_WEEK = ["ראשון", "שני", "שלישי", "רביעי", "חמישי", "ששי", "שבת"];

	const OMER_PREFIX = "ב";

	protected static function defaultOptions(): array
	{
		return array_replace(parent::defaultOptions(), [
			'months' => self::MONTHS,
			'daysOfWeek' => self::DAYS_OF_WEEK,
			'omerPrefix' => self::OMER_PREFIX,
			'useGershGershayim' => true,
			'useFinalFormLetters' => false,
			'useLongHebrewYears' => false,
		]);
	}

	protected function validateOptions(): void
	{
		parent::validateOptions();
		$this->expectList('months', 14);
		$this->expectList('daysOfWeek', 7);
	}

	public function month(): string
	{
		$month = $this->date->getJewishMonth();
		$isLeapYear = $this->date->isJewishLeapYear();
		$months = $this->options['months'];

		if ($isLeapYear && $month == JewishDate::ADAR) {
			return $months[13] . self::GERESH; // Adar I, not Adar, in a leap year
		}
		if ($isLeapYear && $month == JewishDate::ADAR_II) {
			return $months[12] . self::GERESH;
		}

		return $months[$month - 1];
	}

	public function dayOfWeek(): string
	{
		return $this->options['daysOfWeek'][$this->date->getDayOfWeek() - 1];
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
		$returnValue .= ($kviah == JewishDate::CHASERIM ? "ח" : ($kviah == JewishDate::SHELAIMIM ? "ש" : "כ"));
		$returnValue .= $this->hebrewNumber($pesachDayOfWeek);

		return str_replace(self::GERESH, "", $returnValue);
	}

	protected function translate(Nameable $value): string
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
		return $this->hebrewNumber($omer) . " " . $this->options['omerPrefix'] . "עומר";
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

		$alafim = "אלפים";
		$efes = "אפס";

		$jHundreds = ["", "ק", "ר", "ש", "ת", "תק", "תר", "תש", "תת", "תתק"];
		$jTens = ["", "י", "כ", "ל", "מ", "נ", "ס", "ע", "פ", "צ"];
		$jTenEnds = ["", "י", "ך", "ל", "ם", "ן", "ס", "ע", "ף", "ץ"];
		$tavTaz = ["טו", "טז"];
		$jOnes = ["", "א", "ב", "ג", "ד", "ה", "ו", "ז", "ח", "ט"];

		if ($number == 0) {
			return $efes;
		}

		$shortNumber = $number % 1000;
		$singleDigitNumber = ($shortNumber < 11 || ($shortNumber < 100 && $shortNumber % 10 == 0) || ($shortNumber <= 400 && $shortNumber % 100 == 0));
		$thousands = intdiv($number, 1000);

		$useGershGershayim = $this->options['useGershGershayim'];
		$formatted = "";

		if ($number % 1000 == 0) {
			$formatted = $jOnes[$thousands];
			if ($useGershGershayim) {
				$formatted .= self::GERESH;
			}

			return $formatted . " " . $alafim;
		} elseif ($this->options['useLongHebrewYears'] && $number >= 1000) {
			$formatted = $jOnes[$thousands];
			if ($useGershGershayim) {
				$formatted .= self::GERESH;
			}
			$formatted .= " ";
		}

		$number = $number % 1000;
		$hundreds = intdiv($number, 100);
		$formatted .= $jHundreds[$hundreds];

		$number = $number % 100;
		if ($number == 15) {
			$formatted .= $tavTaz[0];
		} elseif ($number == 16) {
			$formatted .= $tavTaz[1];
		} else {
			$tens = intdiv($number, 10);
			if ($number % 10 == 0) {
				$formatted .= !$singleDigitNumber && $this->options['useFinalFormLetters']
					? $jTenEnds[$tens]
					: $jTens[$tens];
			} else {
				$formatted .= $jTens[$tens];
				$formatted .= $jOnes[$number % 10];
			}
		}

		if ($useGershGershayim) {
			if ($singleDigitNumber) {
				$formatted .= self::GERESH;
			} else {
				$formatted = substr_replace($formatted, self::GERSHAYIM, strlen($formatted) - 2, 0);
			}
		}

		return $formatted;
	}
}
