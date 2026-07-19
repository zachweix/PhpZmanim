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

class English extends LanguageFormatter
{
	const MONTHS = ["Nissan", "Iyar", "Sivan", "Tammuz", "Av", "Elul", "Tishrei", "Cheshvan",
		"Kislev", "Teves", "Shevat", "Adar", "Adar II", "Adar I"];

	const SHABBOS = "Shabbos";

	protected static function defaultOptions(): array
	{
		return array_replace(parent::defaultOptions(), [
			'months' => self::MONTHS,
			'shabbos' => self::SHABBOS,
		]);
	}

	protected function validateOptions(): void
	{
		parent::validateOptions();
		$this->expectList('months', 14);
	}

	public function month(): string
	{
		$month = $this->date->getJewishMonth();
		$months = $this->options['months'];

		if ($this->date->isJewishLeapYear() && $month == JewishDate::ADAR) {
			return $months[13]; // Adar I, not Adar, in a leap year
		}

		return $months[$month - 1];
	}

	public function dayOfWeek(): string
	{
		if ($this->date->getDayOfWeek() == 7) {
			return $this->options['shabbos'];
		}

		return $this->date->toCarbon()->format('l');
	}

	protected function translate(Nameable $value): string
	{
		return $value->english();
	}

	protected function formatNumber(int $number): string
	{
		return (string) $number;
	}

	protected function dateYearSeparator(): string
	{
		return ", ";
	}

	protected function chanukah(YomTov $yomTov, int $dayOfChanukah): string
	{
		return $yomTov->english() . " " . $dayOfChanukah;
	}

	protected function formatOmer(int $omer): string
	{
		if ($omer == 33) { // Lag B'Omer
			return YomTov::LAG_BAOMER->english();
		}

		return "Omer " . $omer;
	}
}
