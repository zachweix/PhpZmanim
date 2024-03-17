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

class YerushalmiYomiCalculator {
	const DAF_YOMI_START_DAY = "1980-02-02";
	const WHOLE_SHAS_DAFS = 1554;
	const BLATT_PER_MASECHTA = [
			68, 37, 34, 44, 31, 59, 26, 33, 28, 20, 13, 92, 65, 71, 22, 22, 42, 26, 26, 33, 34, 22,
			19, 85, 72, 47, 40, 47, 54, 48, 44, 37, 34, 44, 9, 57, 37, 19, 13];

	public static function getDafYomiYerushalmi(JewishCalendar $calendar) {
		$requested = $calendar->getGregorianCalendar()->utc();

		if ($calendar->getYomTovIndex() == JewishCalendar::YOM_KIPPUR ||
				$calendar->getYomTovIndex() == JewishCalendar::TISHA_BEAV ) {
			return null;
		}

		if ($requested->toDateString() < self::DAF_YOMI_START_DAY) {
			throw new \Exception($requested->toDateString() . " is prior to organized Daf Yomi Yerushalmi cycles that started on February 2, 1980");
		}

		$nextCycle = Carbon::createMidnightDate(1980, 2, 2, "UTC");
		$prevCycle = $nextCycle->clone();

		while ($requested->gt($nextCycle)) {
			$prevCycle->setDate($nextCycle->year, $nextCycle->month, $nextCycle->day);
			
			$nextCycle->addDays(self::WHOLE_SHAS_DAFS);
			$nextCycle->addDays(self::getNumOfSpecialDays($prevCycle, $nextCycle));		
		}
		
		// Get the number of days from cycle start until request.
		$dafNo = (int) $requested->diffInDays($prevCycle, true);

		$specialDays = self::getNumOfSpecialDays($prevCycle, $requested);

		$total = $dafNo - $specialDays;

		$masechta = 0;
		for ($j = 0; $j < count(self::BLATT_PER_MASECHTA); $j++) {
			if ($total < self::BLATT_PER_MASECHTA[$j]) {
				$dafYomi = new Daf($masechta, $total + 1);
				break;
			}
			$total -= self::BLATT_PER_MASECHTA[$j];
			$masechta++;
		}

		return $dafYomi ?? null;
	}

	private static function getNumOfSpecialDays($start, $end) {
		$startYear = JewishDate::create($start)->getJewishYear();
		$endYear = JewishDate::create($end)->getJewishYear();
		
		$specialDays = 0;
		
		$yom_kippur = new JewishCalendar(5770, 7, 10);
		$tisha_beav = new JewishCalendar(5770, 5, 9);

		for ($i = $startYear; $i <= $endYear; $i++) {
			$yom_kippur->setJewishYear($i);
			$tisha_beav->setJewishYear($i);
			
			if (self::isBetween($start, $yom_kippur->getGregorianCalendar(), $end)) {
				$specialDays++;
			}
			if (self::isBetween($start, $tisha_beav->getGregorianCalendar(), $end)) {
				$specialDays++;
			}
		}
		
		return $specialDays;
	}

	private static function isBetween($start, $date, $end) {
		return $start->lt($date) && $end->gt($date);
	}
}
