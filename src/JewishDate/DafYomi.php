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
use PhpZmanim\JewishDate;
use PhpZmanim\Torah\Daf;
use PhpZmanim\Torah\MasechtaBavli;
use PhpZmanim\Torah\MasechtaYerushalmi;
use PhpZmanim\Torah\YomTov;

/**
 * @property int $gregorianYear;
 * @property int $gregorianMonth;
 * @property int $gregorianDayOfMonth;
 */
trait DafYomi
{
	// The following are from YomiCalculator / YerushalmiYomiCalculator

	const DAF_YOMI_BAVLI_JULIAN_START_DAY = 2423673; // September 11, 1923
	const SHEKALIM_JULIAN_CHANGE_DAY = 2442587;       // June 24, 1975

	const DAF_YOMI_YERUSHALMI_WHOLE_SHAS_DAFS = 1554;
	const DAF_YOMI_YERUSHALMI_BLATT_PER_MASECHTA = [
		68, 37, 34, 44, 31, 59, 26, 33, 28, 20, 13, 92, 65, 71, 22, 22, 42, 26, 26, 33, 34, 22,
		19, 85, 72, 47, 40, 47, 54, 48, 44, 37, 34, 44, 9, 57, 37, 19, 13,
	];

	/**
	 * The Daf Yomi Bavli page for this date. The first cycle started September 11, 1923.
	 */
	public function getDafYomiBavli(): Daf
	{
		$blattPerMasechta = [64, 157, 105, 121, 22, 88, 56, 40, 35, 31, 32, 29, 27, 122, 112, 91, 66, 49, 90, 82,
			119, 119, 176, 113, 24, 49, 76, 14, 120, 110, 142, 61, 34, 34, 28, 22, 4, 9, 5, 73];

		$julianDay = self::getJulianDay($this->gregorianYear, $this->gregorianMonth, $this->gregorianDayOfMonth);

		if ($julianDay < self::DAF_YOMI_BAVLI_JULIAN_START_DAY) {
			throw new \InvalidArgumentException(sprintf('%d-%02d-%02d is prior to organized Daf Yomi Bavli cycles that started on September 11, 1923',
				$this->gregorianYear, $this->gregorianMonth, $this->gregorianDayOfMonth));
		}

		if ($julianDay >= self::SHEKALIM_JULIAN_CHANGE_DAY) {
			$cycleNo = 8 + intdiv($julianDay - self::SHEKALIM_JULIAN_CHANGE_DAY, 2711);
			$dafNo = ($julianDay - self::SHEKALIM_JULIAN_CHANGE_DAY) % 2711;
		} else {
			$cycleNo = 1 + intdiv($julianDay - self::DAF_YOMI_BAVLI_JULIAN_START_DAY, 2702);
			$dafNo = ($julianDay - self::DAF_YOMI_BAVLI_JULIAN_START_DAY) % 2702;
		}

		// Shekalim was 13 daf for the first 7 cycles (before the Vilna Shas change).
		if ($cycleNo <= 7) {
			$blattPerMasechta[4] = 13;
		}

		$total = 0;
		$masechta = -1;
		foreach ($blattPerMasechta as $blattCount) {
			$masechta++;
			$total = $total + $blattCount - 1;
			if ($dafNo < $total) {
				$blatt = 1 + $blattCount - ($total - $dafNo);
				// Fiddle with the weird ones near the end.
				if ($masechta == 36) {
					$blatt += 21;
				} elseif ($masechta == 37) {
					$blatt += 24;
				} elseif ($masechta == 38) {
					$blatt += 32;
				}
				return new Daf(MasechtaBavli::from($masechta), $blatt);
			}
		}

		throw new \RuntimeException('Unable to compute the Daf Yomi Bavli page');
	}

	/**
	 * The Daf Yomi Yerushalmi page for this date, or null on Yom Kippur / Tisha B'Av (no daf).
	 * The first cycle started February 2, 1980.
	 */
	public function getDafYomiYerushalmi(): ?Daf
	{
		$yomTov = $this->getYomTov();
		if ($yomTov === YomTov::YOM_KIPPUR || $yomTov === YomTov::TISHA_BEAV) {
			return null;
		}

		$requested = Carbon::createMidnightDate($this->gregorianYear, $this->gregorianMonth, $this->gregorianDayOfMonth, 'UTC');
		$startDay = Carbon::createMidnightDate(1980, 2, 2, 'UTC');

		if ($requested->lt($startDay)) {
			throw new \InvalidArgumentException($requested->toDateString() . ' is prior to organized Daf Yomi Yerushalmi cycles that started on February 2, 1980');
		}

		$prevCycle = $startDay->copy();
		$nextCycle = self::getYerushalmiNextCycleStart($prevCycle);

		while (!$requested->lt($nextCycle)) {
			$prevCycle = $nextCycle->copy();
			$nextCycle = self::getYerushalmiNextCycleStart($prevCycle);
		}

		$total = self::daysBetween($prevCycle, $requested) - self::getYerushalmiNumOfSpecialDays($prevCycle, $requested);

		$masechta = 0;
		foreach (self::DAF_YOMI_YERUSHALMI_BLATT_PER_MASECHTA as $blattCount) {
			if ($total < $blattCount) {
				return new Daf(MasechtaYerushalmi::from($masechta), $total + 1);
			}
			$total -= $blattCount;
			$masechta++;
		}

		return null;
	}

	/*
	|--------------------------------------------------------------------------
	| YERUSHALMI CYCLE HELPERS
	|--------------------------------------------------------------------------
	*/

	/**
	 * The start of the cycle following the one that begins on $cycleStart. The cycle end is inclusive,
	 * so special days (Yom Kippur / Tisha B'Av) landing on the tentative end extend the cycle.
	 */
	private static function getYerushalmiNextCycleStart(Carbon $cycleStart): Carbon
	{
		$endDate = $cycleStart->copy()->addDays(self::DAF_YOMI_YERUSHALMI_WHOLE_SHAS_DAFS - 1);
		$specialDays = self::getYerushalmiNumOfSpecialDays($cycleStart, $endDate);

		while ($specialDays > 0) {
			$newStart = $endDate->copy()->addDays(1);
			$endDate = $endDate->copy()->addDays($specialDays);
			$specialDays = self::getYerushalmiNumOfSpecialDays($newStart, $endDate);
		}

		return $endDate->copy()->addDays(1);
	}

	/**
	 * The number of "special days" (Yom Kippur, Tisha B'Av) after $start and on or before $end.
	 */
	private static function getYerushalmiNumOfSpecialDays(Carbon $start, Carbon $end): int
	{
		$startYear = (new JewishDate($start))->getJewishYear();
		$endYear = (new JewishDate($end))->getJewishYear();

		$specialDays = 0;
		for ($year = $startYear; $year <= $endYear; $year++) {
			$yomKippur = new JewishDate($year, JewishDate::TISHREI, 10);
			$tishaBeav = new JewishDate($year, JewishDate::AV, 9);
			if ($tishaBeav->getDayOfWeek() == 7) { // Tisha B'Av nidcheh — pushed to Sunday
				$tishaBeav->addDays(1);
			}

			if (self::yerushalmiIsBetween($start, self::toUtcMidnight($yomKippur), $end)) {
				$specialDays++;
			}
			if (self::yerushalmiIsBetween($start, self::toUtcMidnight($tishaBeav), $end)) {
				$specialDays++;
			}
		}

		return $specialDays;
	}

	private static function yerushalmiIsBetween(Carbon $start, Carbon $date, Carbon $end): bool
	{
		return $start->lt($date) && $end->gte($date); // after start, on or before end
	}

	private static function toUtcMidnight(JewishDate $date): Carbon
	{
		return Carbon::createMidnightDate($date->getGregorianYear(), $date->getGregorianMonth(), $date->getGregorianDayOfMonth(), 'UTC');
	}

	private static function daysBetween(Carbon $start, Carbon $end): int
	{
		return (int) round(($end->timestamp - $start->timestamp) / 86400);
	}

	private static function getJulianDay(int $year, int $month, int $day): int
	{
		if ($month <= 2) {
			$year -= 1;
			$month += 12;
		}
		$a = intdiv($year, 100);
		$b = 2 - $a + intdiv($a, 4);

		return (int) (floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $b - 1524.5);
	}
}
