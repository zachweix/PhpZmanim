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
use PhpZmanim\Torah\MasechtaYerushalmi;
use PhpZmanim\Torah\Nameable;
use PhpZmanim\Torah\YomTov;

/**
 * The shared contract and language-neutral skeletons for formatting a JewishDate.
 * Concrete subclasses (Hebrew, English) supply only the language-specific rendering
 * hooks. Every piece returns a string, empty when the piece does not apply.
 */
abstract class LanguageFormatter
{
	public function __construct(
		protected readonly JewishDate $date
	) {}

	/*
	|--------------------------------------------------------------------------
	| SHARED PIECES (skeletons)
	|--------------------------------------------------------------------------
	*/

	public function date(): string
	{
		return $this->formatNumber($this->date->getJewishDayOfMonth())
			. " " . $this->month()
			. $this->dateYearSeparator() . $this->formatNumber($this->date->getJewishYear());
	}

	public function parshah(): string
	{
		return $this->name($this->date->getParshah());
	}

	public function specialShabbos(): string
	{
		return $this->name($this->date->getSpecialShabbos());
	}

	public function yomTov(): string
	{
		$yomTov = $this->date->getYomTov();

		if ($yomTov === YomTov::CHANUKAH) {
			return $this->chanukah($yomTov, $this->date->getDayOfChanukah());
		}

		return $this->name($yomTov);
	}

	public function roshChodesh(): string
	{
		if (!$this->date->isRoshChodesh()) {
			return "";
		}

		$monthName = $this->withDate($this->date->copy()->setJewishMonth($this->roshChodeshMonth()))->month();

		return $this->name(YomTov::ROSH_CHODESH) . " " . $monthName;
	}

	public function omer(): string
	{
		$omer = $this->date->getDayOfOmer();
		if ($omer == -1) {
			return "";
		}

		return $this->formatOmer($omer);
	}

	public function dafYomiBavli(): string
	{
		$daf = $this->date->getDafYomiBavli();

		return $this->name($daf->getMasechta()) . " " . $this->formatNumber($daf->getDaf());
	}

	public function dafYomiYerushalmi(): string
	{
		$daf = $this->date->getDafYomiYerushalmi();
		if ($daf === null) {
			return $this->name(MasechtaYerushalmi::NO_DAF);
		}

		return $this->name($daf->getMasechta()) . " " . $this->formatNumber($daf->getDaf());
	}

	/*
	|--------------------------------------------------------------------------
	| LANGUAGE-SPECIFIC HOOKS
	|--------------------------------------------------------------------------
	*/

	abstract public function month(): string;

	abstract public function dayOfWeek(): string;

	abstract protected function name(Nameable $value): string;

	abstract protected function formatNumber(int $number): string;

	abstract protected function dateYearSeparator(): string;

	abstract protected function chanukah(YomTov $yomTov, int $dayOfChanukah): string;

	abstract protected function formatOmer(int $omer): string;

	/*
	|--------------------------------------------------------------------------
	| SHARED HELPERS
	|--------------------------------------------------------------------------
	*/

	/**
	 * The month whose Rosh Chodesh this day belongs to. On the 30th (the first of a
	 * two day Rosh Chodesh) that is the following month.
	 */
	protected function roshChodeshMonth(): int
	{
		$month = $this->date->getJewishMonth();
		if ($this->date->getJewishDayOfMonth() == 30) {
			if ($month < JewishDate::ADAR || ($month == JewishDate::ADAR && $this->date->isJewishLeapYear())) {
				$month++;
			} else { // roll to Nissan
				$month = JewishDate::NISSAN;
			}
		}

		return $month;
	}

	/**
	 * A formatter of the same language bound to a different date.
	 */
	protected function withDate(JewishDate $date): static
	{
		return new static($date);
	}
}
