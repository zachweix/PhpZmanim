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

use InvalidArgumentException;
use UnitEnum;
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
	protected readonly array $options;

	public function __construct(
		protected readonly JewishDate $date,
		array $options = []
	) {
		$defaults = static::defaultOptions();

		$unknown = array_diff_key($options, $defaults);
		if ($unknown !== []) {
			throw new InvalidArgumentException(sprintf(
				'Unknown formatter option%s: %s. Valid options are: %s.',
				count($unknown) === 1 ? '' : 's',
				implode(', ', array_keys($unknown)),
				implode(', ', array_keys($defaults))
			));
		}

		$this->options = array_replace($defaults, $options);
		$this->validateOptions();
	}

	protected static function defaultOptions(): array
	{
		return ['names' => []];
	}

	protected function validateOptions(): void
	{
		if (!is_array($this->options['names'])) {
			throw new InvalidArgumentException('The "names" option must be an array keyed by enum class name.');
		}

		foreach ($this->options['names'] as $enum => $overrides) {
			if (!is_string($enum) || !enum_exists($enum)) {
				throw new InvalidArgumentException(sprintf(
					'The "names" option must be keyed by enum class name, e.g. YomTov::class; got "%s".',
					is_string($enum) ? $enum : gettype($enum)
				));
			}
			if (!is_array($overrides)) {
				throw new InvalidArgumentException(sprintf(
					'The "names" entry for %s must be an array of case name => label.',
					$enum
				));
			}
			$cases = array_column($enum::cases(), 'name');
			$unknown = array_diff(array_keys($overrides), $cases);
			if ($unknown !== []) {
				throw new InvalidArgumentException(sprintf(
					'Unknown %s case%s in "names": %s.',
					$enum,
					count($unknown) === 1 ? '' : 's',
					implode(', ', $unknown)
				));
			}
		}
	}

	protected function expectList(string $option, int $count): void
	{
		$value = $this->options[$option];
		if (!is_array($value) || count($value) !== $count) {
			throw new InvalidArgumentException(sprintf(
				'The "%s" option must be an array of exactly %d entries, %s given.',
				$option,
				$count,
				is_array($value) ? count($value) . ' entries' : gettype($value)
			));
		}
	}

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

	abstract protected function translate(Nameable $value): string;

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
		return new static($date, $this->options);
	}

	protected function name(Nameable $value): string
	{
		if ($value instanceof UnitEnum) {
			$override = $this->options['names'][$value::class][$value->name] ?? null;
			if ($override !== null) {
				return $override;
			}
		}

		return $this->translate($value);
	}
}
