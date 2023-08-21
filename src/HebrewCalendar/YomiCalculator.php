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

class YomiCalculator {
	const DAF_YOMI_JULIAN_START_DAY = 2423673;
	const SHEKALIM_JULIAN_CHANGE_DAY = 2442587;

	public static function getDafYomiBavli(JewishCalendar $calendar) {
		$year = $calendar->getGregorianYear();
		$month = $calendar->getGregorianMonth();
		$day = $calendar->getGregorianDayOfMonth();

		$blattPerMasechta = [64, 157, 105, 121, 22, 88, 56, 40, 35, 31, 32, 29, 27, 122, 112, 91, 66, 49, 90, 82,
				119, 119, 176, 113, 24, 49, 76, 14, 120, 110, 142, 61, 34, 34, 28, 22, 4, 9, 5, 73];

		$julianDay = self::getJulianDay($year, $month, $day);

		if ($julianDay < self::DAF_YOMI_JULIAN_START_DAY) {
			throw new \Exception($year . '-' . $month . '-' . $day . " is prior to organized Daf Yomi Bavli cycles that started on September 11, 1923");
		}

		if ($julianDay >= self::SHEKALIM_JULIAN_CHANGE_DAY) {
			$cycleNo = 8 + (int) (($julianDay - self::SHEKALIM_JULIAN_CHANGE_DAY) / 2711);
			$dafNo = (($julianDay - self::SHEKALIM_JULIAN_CHANGE_DAY) % 2711);
		} else {
			$cycleNo = 1 + (int) (($julianDay - self::DAF_YOMI_JULIAN_START_DAY) / 2702);
			$dafNo = (($julianDay - self::DAF_YOMI_JULIAN_START_DAY) % 2702);
		}

		$total = 0;
		$masechta = -1;
		$blatt = 0;

		if ($cycleNo <= 7) {
			$blattPerMasechta[4] = 13;
		} else {
			$blattPerMasechta[4] = 22;
		}

		for ($j = 0; $j < count($blattPerMasechta); $j++) {
			$masechta++;
			$total = $total + $blattPerMasechta[$j] - 1;
			if ($dafNo < $total) {
				$blatt = 1 + $blattPerMasechta[$j] - ($total - $dafNo);
				// Fiddle with the weird ones near the end.
				if ($masechta == 36) {
					$blatt += 21;
				} else if ($masechta == 37) {
					$blatt += 24;
				} else if ($masechta == 38) {
					$blatt += 32;
				}
				$dafYomi = new Daf($masechta, $blatt);
				break;
			}
		}

		return $dafYomi ?? null;
	}

	private static function getJulianDay($year, $month, $day) {
		if ($month <= 2) {
			$year -= 1;
			$month += 12;
		}
		$a = (int) ($year / 100);
		$b = 2 - $a + (int) ($a / 4);
		return (int) (floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $b - 1524.5);
	}
}