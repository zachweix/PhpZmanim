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
use PhpZmanim\Torah\YomTov;

/**
 * @property int $jewishMonth;
 * @property int $jewishDay;
 * @property int $jewishYear;
 * @property int $dayOfWeek;
 * @property bool $inIsrael;
 * @property bool $isMukafChoma;
 */
trait Holidays
{
	// The following are from JewishCalendar

	public function getYomTov(): YomTov
	{
		$day = $this->jewishDay;
		$dayOfWeek = $this->dayOfWeek;

		switch ($this->jewishMonth) {
			case JewishDate::NISSAN:
				if ($day == 14) {
					return YomTov::EREV_PESACH;
				}
				if ($day == 15 || $day == 21 || (!$this->inIsrael && ($day == 16 || $day == 22))) {
					return YomTov::PESACH;
				}
				if (($day >= 17 && $day <= 20) || ($day == 16 && $this->inIsrael)) {
					return YomTov::CHOL_HAMOED_PESACH;
				}
				if (($day == 22 && $this->inIsrael) || ($day == 23 && !$this->inIsrael)) {
					return YomTov::ISRU_CHAG;
				}
				if ($this->useModernHolidays
					&& (($day == 26 && $dayOfWeek == 5)
						|| ($day == 28 && $dayOfWeek == 2)
						|| ($day == 27 && $dayOfWeek != 1 && $dayOfWeek != 6))) {
					return YomTov::YOM_HASHOAH;
				}
				break;
			case JewishDate::IYAR:
				if ($this->useModernHolidays
					&& (($day == 4 && $dayOfWeek == 3)
						|| (($day == 3 || $day == 2) && $dayOfWeek == 4) || ($day == 5 && $dayOfWeek == 2))) {
					return YomTov::YOM_HAZIKARON;
				}
				if ($this->useModernHolidays
					&& (($day == 5 && $dayOfWeek == 4)
						|| (($day == 4 || $day == 3) && $dayOfWeek == 5) || ($day == 6 && $dayOfWeek == 3))) {
					return YomTov::YOM_HAATZMAUT;
				}
				if ($day == 14) {
					return YomTov::PESACH_SHENI;
				}
				if ($day == 18) {
					return YomTov::LAG_BAOMER;
				}
				if ($this->useModernHolidays && $day == 28) {
					return YomTov::YOM_YERUSHALAYIM;
				}
				break;
			case JewishDate::SIVAN:
				if ($day == 5) {
					return YomTov::EREV_SHAVUOS;
				}
				if ($day == 6 || ($day == 7 && !$this->inIsrael)) {
					return YomTov::SHAVUOS;
				}
				if (($day == 7 && $this->inIsrael) || ($day == 8 && !$this->inIsrael)) {
					return YomTov::ISRU_CHAG;
				}
				break;
			case JewishDate::TAMMUZ:
				if (($day == 17 && $dayOfWeek != 7) || ($day == 18 && $dayOfWeek == 1)) {
					return YomTov::SEVENTEEN_OF_TAMMUZ;
				}
				break;
			case JewishDate::AV:
				if (($dayOfWeek == 1 && $day == 10) || ($dayOfWeek != 7 && $day == 9)) {
					return YomTov::TISHA_BEAV;
				}
				if ($day == 15) {
					return YomTov::TU_BEAV;
				}
				break;
			case JewishDate::ELUL:
				if ($day == 29) {
					return YomTov::EREV_ROSH_HASHANA;
				}
				break;
			case JewishDate::TISHREI:
				if ($day == 1 || $day == 2) {
					return YomTov::ROSH_HASHANA;
				}
				if (($day == 3 && $dayOfWeek != 7) || ($day == 4 && $dayOfWeek == 1)) {
					return YomTov::FAST_OF_GEDALYAH;
				}
				if ($day == 9) {
					return YomTov::EREV_YOM_KIPPUR;
				}
				if ($day == 10) {
					return YomTov::YOM_KIPPUR;
				}
				if ($day == 14) {
					return YomTov::EREV_SUCCOS;
				}
				if ($day == 15 || ($day == 16 && !$this->inIsrael)) {
					return YomTov::SUCCOS;
				}
				if (($day >= 17 && $day <= 20) || ($day == 16 && $this->inIsrael)) {
					return YomTov::CHOL_HAMOED_SUCCOS;
				}
				if ($day == 21) {
					return YomTov::HOSHANA_RABBA;
				}
				if ($day == 22) {
					return YomTov::SHEMINI_ATZERES;
				}
				if ($day == 23 && !$this->inIsrael) {
					return YomTov::SIMCHAS_TORAH;
				}
				if (($day == 23 && $this->inIsrael) || ($day == 24 && !$this->inIsrael)) {
					return YomTov::ISRU_CHAG;
				}
				break;
			case JewishDate::KISLEV:
				if ($day >= 25) {
					return YomTov::CHANUKAH;
				}
				break;
			case JewishDate::TEVES:
				if ($day == 1 || $day == 2 || ($day == 3 && self::isKislevShortForYear($this->jewishYear))) {
					return YomTov::CHANUKAH;
				}
				if ($day == 10) {
					return YomTov::TENTH_OF_TEVES;
				}
				break;
			case JewishDate::SHEVAT:
				if ($day == 15) {
					return YomTov::TU_BESHVAT;
				}
				break;
			case JewishDate::ADAR:
				if (!self::isJewishLeapYearForYear($this->jewishYear)) {
					if ((($day == 11 || $day == 12) && $dayOfWeek == 5) || ($day == 13 && !($dayOfWeek == 6 || $dayOfWeek == 7))) {
						return YomTov::FAST_OF_ESTHER;
					}
					if ($day == 14) {
						return YomTov::PURIM;
					}
					if ($day == 15) {
						return YomTov::SHUSHAN_PURIM;
					}
				} else {
					if ($day == 14) {
						return YomTov::PURIM_KATAN;
					}
					if ($day == 15) {
						return YomTov::SHUSHAN_PURIM_KATAN;
					}
				}
				break;
			case JewishDate::ADAR_II:
				if ((($day == 11 || $day == 12) && $dayOfWeek == 5) || ($day == 13 && !($dayOfWeek == 6 || $dayOfWeek == 7))) {
					return YomTov::FAST_OF_ESTHER;
				}
				if ($day == 14) {
					return YomTov::PURIM;
				}
				if ($day == 15) {
					return YomTov::SHUSHAN_PURIM;
				}
				break;
		}

		return YomTov::NONE;
	}

	public function getYomTovIndex(): int
	{
		return $this->getYomTov()->value;
	}

	public function isYomTov(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		if (($this->isErevYomTov($yomTov) && $yomTov !== YomTov::HOSHANA_RABBA && $yomTov !== YomTov::CHOL_HAMOED_PESACH)
			|| ($this->isTaanis($yomTov) && $yomTov !== YomTov::YOM_KIPPUR) || $yomTov === YomTov::ISRU_CHAG) {
			return false;
		}

		return $yomTov !== YomTov::NONE;
	}

	public function isErevYomTov(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::EREV_PESACH || $yomTov === YomTov::EREV_SHAVUOS || $yomTov === YomTov::EREV_ROSH_HASHANA
			|| $yomTov === YomTov::EREV_YOM_KIPPUR || $yomTov === YomTov::EREV_SUCCOS || $yomTov === YomTov::HOSHANA_RABBA
			|| ($yomTov === YomTov::CHOL_HAMOED_PESACH && $this->jewishDay == 20);
	}

	public function isAseresYemeiTeshuva(): bool
	{
		return $this->jewishMonth == JewishDate::TISHREI && $this->jewishDay <= 10;
	}

	public function isPesach(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::PESACH || $yomTov === YomTov::CHOL_HAMOED_PESACH;
	}

	public function isCholHamoedPesach(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::CHOL_HAMOED_PESACH;
	}

	public function isShavuos(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::SHAVUOS;
	}

	public function isRoshHashana(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::ROSH_HASHANA;
	}

	public function isYomKippur(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::YOM_KIPPUR;
	}

	public function isSuccos(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::SUCCOS || $yomTov === YomTov::CHOL_HAMOED_SUCCOS || $yomTov === YomTov::HOSHANA_RABBA;
	}

	public function isHoshanaRabba(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::HOSHANA_RABBA;
	}

	public function isShminiAtzeres(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::SHEMINI_ATZERES;
	}

	public function isSimchasTorah(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::SIMCHAS_TORAH;
	}

	public function isCholHamoedSuccos(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::CHOL_HAMOED_SUCCOS || $yomTov === YomTov::HOSHANA_RABBA;
	}

	public function isCholHamoed(?YomTov $yomTov = null): bool
	{
		return $this->isCholHamoedPesach($yomTov) || $this->isCholHamoedSuccos($yomTov);
	}

	public function isChanukah(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::CHANUKAH;
	}

	public function getDayOfChanukah(): int
	{
		$day = $this->jewishDay;
		if ($this->isChanukah()) {
			if ($this->jewishMonth == JewishDate::KISLEV) {
				return $day - 24;
			} else {
				return self::isKislevShortForYear($this->jewishYear) ? $day + 5 : $day + 6;
			}
		}

		return -1;
	}

	public function isPurim(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		if ($this->isMukafChoma) {
			return $yomTov === YomTov::SHUSHAN_PURIM;
		}

		return $yomTov === YomTov::PURIM;
	}

	public function getDayOfOmer(): int
	{
		$omer = -1;
		$month = $this->jewishMonth;
		$day = $this->jewishDay;

		if ($month == JewishDate::NISSAN && $day >= 16) {
			$omer = $day - 15;
		} elseif ($month == JewishDate::IYAR) {
			$omer = $day + 15;
		} elseif ($month == JewishDate::SIVAN && $day < 6) {
			$omer = $day + 44;
		}

		return $omer;
	}

	public function isTaanis(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::SEVENTEEN_OF_TAMMUZ || $yomTov === YomTov::TISHA_BEAV || $yomTov === YomTov::YOM_KIPPUR
			|| $yomTov === YomTov::FAST_OF_GEDALYAH || $yomTov === YomTov::TENTH_OF_TEVES || $yomTov === YomTov::FAST_OF_ESTHER;
	}

	public function isTaanisBechoros(): bool
	{
		$day = $this->jewishDay;
		$dayOfWeek = $this->dayOfWeek;

		return $this->jewishMonth == JewishDate::NISSAN && (($day == 14 && $dayOfWeek != 7) || ($day == 12 && $dayOfWeek == 5));
	}

	public function isTishaBav(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::TISHA_BEAV;
	}

	public function isIsruChag(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::ISRU_CHAG;
	}
}
