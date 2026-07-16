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

namespace PhpZmanim\JewishDate;

use Carbon\Carbon;

/**
 * @property int $jewishYear;
 * @property int $gregorianYear;
 * @property int $gregorianMonth;
 * @property int $gregorianDayOfMonth;
 */
trait Tekufa
{
	// The following are from JewishCalendar

	public function isBirkasHachamah(): bool
	{
		$elapsedDays = self::getJewishCalendarElapsedDays($this->jewishYear);
		$elapsedDays += $this->getDaysSinceStartOfJewishYear();

		return $elapsedDays % 10227 == 172;
	}

	public function getTekufasTishreiElapsedDays(): int
	{
		$days = self::getJewishCalendarElapsedDays($this->jewishYear) + ($this->getDaysSinceStartOfJewishYear() - 1) + 0.5;
		$solar = ($this->jewishYear - 1) * 365.25;

		return (int) floor($days - $solar);
	}

	public function getTekufaAsCarbon(bool $useLocalMeanTime): Carbon|null
	{
		$hours = $this->getTekufa();
		if ($hours === null) {
			return null;
		}

		$offset = $hours - 6;
		$wholeHours = (int) $offset;
		$minutes = (int) (($offset - $wholeHours) * 60);

		$tekufa = Carbon::create($this->gregorianYear, $this->gregorianMonth, $this->gregorianDayOfMonth, 0, 0, 0, "GMT+2")
			->addHours($wholeHours)
			->addMinutes($minutes);

		if ($useLocalMeanTime) {
			$tekufa->subMinutes(20)->subSeconds(56)->sub(496, "milliseconds");
		}

		return $tekufa;
	}

	private function getTekufa(): float|null
	{
		$initialTekufaOffset = 12.625;

		$days = self::getJewishCalendarElapsedDays($this->jewishYear) + $this->getDaysSinceStartOfJewishYear() + $initialTekufaOffset - 1;

		$solarDaysElapsed = fmod($days, 365.25);
		$tekufaDaysElapsed = fmod($solarDaysElapsed, 91.3125);

		if ($tekufaDaysElapsed > 0 && $tekufaDaysElapsed <= 1) {
			return fmod((1.0 - $tekufaDaysElapsed) * 24.0, 24);
		}

		return null;
	}
}
