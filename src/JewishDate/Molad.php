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

use PhpZmanim\JewishDate;

/**
 * @property int $jewishYear;
 * @property int $jewishMonth;
 * @property int $moladHours;
 * @property int $moladMinutes;
 * @property int $moladChalakim;
 */
trait Molad
{
	// The following are from JewishDate

	public function getMolad(): JewishDate
	{
		$moladDate = new JewishDate(self::getChalakimSinceMoladTohu($this->jewishYear, $this->jewishMonth));
		if ($moladDate->getMoladHours() >= 6) {
			$moladDate->addDays(1);
		}
		$moladDate->setMoladHours(($moladDate->getMoladHours() + 18) % 24);

		return $moladDate;
	}

	private static function getJewishCalendarElapsedDays(int $year): int
	{
		$chalakimSince = self::getChalakimSinceMoladTohu($year, JewishDate::TISHREI);
		$moladDay = intdiv($chalakimSince, JewishDate::CHALAKIM_PER_DAY);
		$moladParts = $chalakimSince - $moladDay * JewishDate::CHALAKIM_PER_DAY;

		return self::addDechiyos($year, $moladDay, $moladParts);
	}

	private static function addDechiyos(int $year, int $moladDay, int $moladParts): int
	{
		$roshHashanaDay = $moladDay;

		if (($moladParts >= 19440)
			|| ((($moladDay % 7) == 2) && ($moladParts >= 9924) && !self::isJewishLeapYearForYear($year))
			|| ((($moladDay % 7) == 1) && ($moladParts >= 16789) && self::isJewishLeapYearForYear($year - 1))) {
			$roshHashanaDay += 1;
		}

		if ((($roshHashanaDay % 7) == 0) || (($roshHashanaDay % 7) == 3) || (($roshHashanaDay % 7) == 5)) {
			$roshHashanaDay += 1;
		}

		return $roshHashanaDay;
	}

	private static function getChalakimSinceMoladTohu(int $year, int $month): int
	{
		$monthOfYear = self::getJewishMonthOfYear($year, $month);

		$lastYear = $year - 1;
		$monthsElapsed = (235 * intdiv($lastYear, 19))
			+ (12 * ($lastYear % 19))
			+ intdiv(7 * ($lastYear % 19) + 1, 19)
			+ ($monthOfYear - 1);

		return JewishDate::CHALAKIM_MOLAD_TOHU + (JewishDate::CHALAKIM_PER_MONTH * $monthsElapsed);
	}

	private static function moladToAbsDate(int $chalakim): int
	{
		return intdiv($chalakim, JewishDate::CHALAKIM_PER_DAY) + JewishDate::JEWISH_EPOCH;
	}

	private function setMoladTime(int $chalakim): void
	{
		$this->setMoladHours(intdiv($chalakim, JewishDate::CHALAKIM_PER_HOUR));
		$chalakim -= $this->moladHours * JewishDate::CHALAKIM_PER_HOUR;
		$this->setMoladMinutes(intdiv($chalakim, JewishDate::CHALAKIM_PER_MINUTE));
		$this->setMoladChalakim($chalakim - $this->moladMinutes * JewishDate::CHALAKIM_PER_MINUTE);
	}
}
