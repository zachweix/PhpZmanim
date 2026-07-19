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
 * Covers the various halachos and minhagim regarding changes to daily tefila based on the Jewish
 * calendar. The many minhag settings default to their Java (KosherJava) values and can be adjusted
 * with the fluent setters below.
 *
 * @property int $jewishYear;
 * @property int $jewishMonth;
 * @property int $jewishDay;
 * @property int $dayOfWeek;
 * @property bool $inIsrael;
 */
trait TefilaRules
{
	private bool $tachanunRecitedEndOfTishrei = true;
	private bool $tachanunRecitedWeekAfterShavuos = false;
	private bool $tachanunRecited13SivanOutOfIsrael = true;
	private bool $tachanunRecitedPesachSheni = false;
	private bool $tachanunRecited15IyarOutOfIsrael = true;
	private bool $tachanunRecitedMinchaErevLagBaomer = false;
	private bool $tachanunRecitedShivasYemeiHamiluim = true;
	private bool $tachanunRecitedWeekOfHod = true;
	private bool $tachanunRecitedWeekOfPurim = true;
	private bool $tachanunRecitedFridays = true;
	private bool $tachanunRecitedSundays = true;
	private bool $tachanunRecitedMinchaAllYear = true;
	private bool $mizmorLesodaRecitedErevYomKippurAndPesach = false;

	/*
	|--------------------------------------------------------------------------
	| TACHANUN
	|--------------------------------------------------------------------------
	*/

	public function isTachanunRecitedShacharis(): bool
	{
		$yomTov = $this->getYomTov();
		$day = $this->jewishDay;
		$month = $this->jewishMonth;

		return $this->dayOfWeek != 7
			&& ($this->tachanunRecitedSundays || $this->dayOfWeek != 1)
			&& ($this->tachanunRecitedFridays || $this->dayOfWeek != 6)
			&& $month != JewishDate::NISSAN
			&& ($month != JewishDate::TISHREI || (($this->tachanunRecitedEndOfTishrei || $day <= 8)
					&& (!$this->tachanunRecitedEndOfTishrei || ($day <= 8 || $day >= 22))))
			&& ($month != JewishDate::SIVAN
					|| ((!$this->tachanunRecitedWeekAfterShavuos || $day >= 7) && ($this->tachanunRecitedWeekAfterShavuos
							|| $day >= (!$this->inIsrael && !$this->tachanunRecited13SivanOutOfIsrael ? 14 : 13))))
			&& !$this->isErevYomTov() && (!$this->isYomTov() || ($this->isTaanis()
					&& ($this->tachanunRecitedPesachSheni || $yomTov !== YomTov::PESACH_SHENI)))
			&& ($this->inIsrael || $this->tachanunRecitedPesachSheni || $this->tachanunRecited15IyarOutOfIsrael
					|| $month != JewishDate::IYAR || $day != 15)
			&& $yomTov !== YomTov::TISHA_BEAV && !$this->isIsruChag()
			&& !$this->isRoshChodesh()
			&& ($this->tachanunRecitedShivasYemeiHamiluim
					|| (($this->isJewishLeapYear() || $month != JewishDate::ADAR)
							&& (!$this->isJewishLeapYear() || $month != JewishDate::ADAR_II))
					|| $day <= 22)
			&& ($this->tachanunRecitedWeekOfPurim
					|| (($this->isJewishLeapYear() || $month != JewishDate::ADAR)
							&& (!$this->isJewishLeapYear() || $month != JewishDate::ADAR_II))
					|| $day <= 10 || $day >= 18)
			&& (!$this->useModernHolidays || ($yomTov !== YomTov::YOM_HAATZMAUT
					&& $yomTov !== YomTov::YOM_YERUSHALAYIM))
			&& ($this->tachanunRecitedWeekOfHod || $month != JewishDate::IYAR || $day <= 13 || $day >= 21);
	}

	public function isTachanunRecitedMincha(): bool
	{
		$tomorrow = $this->copy()->addDays(1);

		return $this->tachanunRecitedMinchaAllYear && $this->dayOfWeek != 6
			&& $this->isTachanunRecitedShacharis()
			&& ($tomorrow->isTachanunRecitedShacharis()
					|| $tomorrow->getYomTov() === YomTov::EREV_ROSH_HASHANA
					|| $tomorrow->getYomTov() === YomTov::EREV_YOM_KIPPUR
					|| $tomorrow->getYomTov() === YomTov::PESACH_SHENI)
			&& ($this->tachanunRecitedMinchaErevLagBaomer || $tomorrow->getYomTov() !== YomTov::LAG_BAOMER);
	}

	/*
	|--------------------------------------------------------------------------
	| VESEIN TAL UMATAR / BRACHA
	|--------------------------------------------------------------------------
	*/

	public function isVeseinTalUmatarStartDate(): bool
	{
		if ($this->inIsrael) {
			// The 7th Cheshvan can't occur on Shabbos, so always return true for 7 Cheshvan
			return $this->jewishMonth == JewishDate::CHESHVAN && $this->jewishDay == 7;
		}

		if ($this->dayOfWeek == 7) { // Not recited on Friday night
			return false;
		}
		if ($this->dayOfWeek == 1) { // When starting on Sunday, it can be the start date or delayed from Shabbos
			return $this->getTekufasTishreiElapsedDays() == 48 || $this->getTekufasTishreiElapsedDays() == 47;
		}

		return $this->getTekufasTishreiElapsedDays() == 47;
	}

	public function isVeseinTalUmatarStartingTonight(): bool
	{
		if ($this->inIsrael) {
			// The 7th Cheshvan can't occur on Shabbos, so always return true for 6 Cheshvan
			return $this->jewishMonth == JewishDate::CHESHVAN && $this->jewishDay == 6;
		}

		if ($this->dayOfWeek == 6) { // Not recited on Friday night
			return false;
		}
		if ($this->dayOfWeek == 7) { // When starting on motzai Shabbos, it can be the start date or delayed from Friday night
			return $this->getTekufasTishreiElapsedDays() == 47 || $this->getTekufasTishreiElapsedDays() == 46;
		}

		return $this->getTekufasTishreiElapsedDays() == 46;
	}

	public function isVeseinTalUmatarRecited(): bool
	{
		$month = $this->jewishMonth;
		$day = $this->jewishDay;

		if ($month == JewishDate::NISSAN && $day >= 15) {
			return false;
		}
		if ($month > JewishDate::NISSAN && $month < JewishDate::CHESHVAN) {
			return false;
		}

		if ($this->inIsrael) {
			return $month != JewishDate::CHESHVAN || $day >= 7;
		}

		return $this->getTekufasTishreiElapsedDays() >= 47;
	}

	public function isVeseinBerachaRecited(): bool
	{
		return !$this->isVeseinTalUmatarRecited();
	}

	/*
	|--------------------------------------------------------------------------
	| MASHIV HARUACH / MORID HATAL
	|--------------------------------------------------------------------------
	*/

	public function isMashivHaruachStartDate(): bool
	{
		return $this->jewishMonth == JewishDate::TISHREI && $this->jewishDay == 22;
	}

	public function isMashivHaruachEndDate(): bool
	{
		return $this->jewishMonth == JewishDate::NISSAN && $this->jewishDay == 15;
	}

	public function isMashivHaruachRecited(): bool
	{
		$startDate = new JewishDate($this->jewishYear, JewishDate::TISHREI, 22);
		$endDate = new JewishDate($this->jewishYear, JewishDate::NISSAN, 15);

		return $this->compareTo($startDate) > 0 && $this->compareTo($endDate) < 0;
	}

	public function isMoridHatalRecited(): bool
	{
		return !$this->isMashivHaruachRecited() || $this->isMashivHaruachStartDate() || $this->isMashivHaruachEndDate();
	}

	/*
	|--------------------------------------------------------------------------
	| HALLEL
	|--------------------------------------------------------------------------
	*/

	public function isHallelRecited(): bool
	{
		$day = $this->jewishDay;
		$month = $this->jewishMonth;
		$yomTov = $this->getYomTov();
		$inIsrael = $this->inIsrael;

		if ($this->isRoshChodesh()) { // RH returns false for RC
			return true;
		}
		if ($this->isChanukah()) {
			return true;
		}

		switch ($month) {
			case JewishDate::NISSAN:
				if ($day >= 15 && (($inIsrael && $day <= 21) || (!$inIsrael && $day <= 22))) {
					return true;
				}
				break;
			case JewishDate::IYAR: // modern holidays
				if ($this->useModernHolidays && ($yomTov === YomTov::YOM_HAATZMAUT
						|| $yomTov === YomTov::YOM_YERUSHALAYIM)) {
					return true;
				}
				break;
			case JewishDate::SIVAN:
				if ($day == 6 || (!$inIsrael && ($day == 7))) {
					return true;
				}
				break;
			case JewishDate::TISHREI:
				if ($day >= 15 && ($day <= 22 || (!$inIsrael && ($day <= 23)))) {
					return true;
				}
		}

		return false;
	}

	public function isHallelShalemRecited(): bool
	{
		$day = $this->jewishDay;
		$month = $this->jewishMonth;
		$inIsrael = $this->inIsrael;

		if ($this->isHallelRecited()) {
			return (!$this->isRoshChodesh() || $this->isChanukah())
				&& ($month != JewishDate::NISSAN || ((!$inIsrael || $day <= 15) && ($inIsrael || $day <= 16)));
		}

		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| AL HANISSIM / YAALEH VEYAVO / MIZMOR LESODA
	|--------------------------------------------------------------------------
	*/

	public function isAlHanissimRecited(): bool
	{
		return $this->isPurim() || $this->isChanukah();
	}

	public function isYaalehVeyavoRecited(): bool
	{
		return $this->isPesach() || $this->isShavuos() || $this->isRoshHashana()
			|| $this->isYomKippur() || $this->isSuccos() || $this->isShminiAtzeres()
			|| $this->isSimchasTorah() || $this->isRoshChodesh();
	}

	public function isMizmorLesodaRecited(): bool
	{
		if ($this->isAssurBemelacha()) {
			return false;
		}

		$yomTov = $this->getYomTov();

		return $this->mizmorLesodaRecitedErevYomKippurAndPesach || ($yomTov !== YomTov::EREV_YOM_KIPPUR
			&& $yomTov !== YomTov::EREV_PESACH && !$this->isCholHamoedPesach());
	}

	/*
	|--------------------------------------------------------------------------
	| MINHAG SETTERS AND GETTERS
	|--------------------------------------------------------------------------
	*/

	public function getTachanunRecitedEndOfTishrei(): bool
	{
		return $this->tachanunRecitedEndOfTishrei;
	}

	public function setTachanunRecitedEndOfTishrei(bool $tachanunRecitedEndOfTishrei): self
	{
		$this->tachanunRecitedEndOfTishrei = $tachanunRecitedEndOfTishrei;

		return $this;
	}

	public function getTachanunRecitedWeekAfterShavuos(): bool
	{
		return $this->tachanunRecitedWeekAfterShavuos;
	}

	public function setTachanunRecitedWeekAfterShavuos(bool $tachanunRecitedWeekAfterShavuos): self
	{
		$this->tachanunRecitedWeekAfterShavuos = $tachanunRecitedWeekAfterShavuos;

		return $this;
	}

	public function getTachanunRecited13SivanOutOfIsrael(): bool
	{
		return $this->tachanunRecited13SivanOutOfIsrael;
	}

	public function setTachanunRecited13SivanOutOfIsrael(bool $tachanunRecited13SivanOutOfIsrael): self
	{
		$this->tachanunRecited13SivanOutOfIsrael = $tachanunRecited13SivanOutOfIsrael;

		return $this;
	}

	public function getTachanunRecitedPesachSheni(): bool
	{
		return $this->tachanunRecitedPesachSheni;
	}

	public function setTachanunRecitedPesachSheni(bool $tachanunRecitedPesachSheni): self
	{
		$this->tachanunRecitedPesachSheni = $tachanunRecitedPesachSheni;

		return $this;
	}

	public function getTachanunRecited15IyarOutOfIsrael(): bool
	{
		return $this->tachanunRecited15IyarOutOfIsrael;
	}

	public function setTachanunRecited15IyarOutOfIsrael(bool $tachanunRecited15IyarOutOfIsrael): self
	{
		$this->tachanunRecited15IyarOutOfIsrael = $tachanunRecited15IyarOutOfIsrael;

		return $this;
	}

	public function getTachanunRecitedMinchaErevLagBaomer(): bool
	{
		return $this->tachanunRecitedMinchaErevLagBaomer;
	}

	public function setTachanunRecitedMinchaErevLagBaomer(bool $tachanunRecitedMinchaErevLagBaomer): self
	{
		$this->tachanunRecitedMinchaErevLagBaomer = $tachanunRecitedMinchaErevLagBaomer;

		return $this;
	}

	public function getTachanunRecitedShivasYemeiHamiluim(): bool
	{
		return $this->tachanunRecitedShivasYemeiHamiluim;
	}

	public function setTachanunRecitedShivasYemeiHamiluim(bool $tachanunRecitedShivasYemeiHamiluim): self
	{
		$this->tachanunRecitedShivasYemeiHamiluim = $tachanunRecitedShivasYemeiHamiluim;

		return $this;
	}

	public function getTachanunRecitedWeekOfHod(): bool
	{
		return $this->tachanunRecitedWeekOfHod;
	}

	public function setTachanunRecitedWeekOfHod(bool $tachanunRecitedWeekOfHod): self
	{
		$this->tachanunRecitedWeekOfHod = $tachanunRecitedWeekOfHod;

		return $this;
	}

	public function getTachanunRecitedWeekOfPurim(): bool
	{
		return $this->tachanunRecitedWeekOfPurim;
	}

	public function setTachanunRecitedWeekOfPurim(bool $tachanunRecitedWeekOfPurim): self
	{
		$this->tachanunRecitedWeekOfPurim = $tachanunRecitedWeekOfPurim;

		return $this;
	}

	public function getTachanunRecitedFridays(): bool
	{
		return $this->tachanunRecitedFridays;
	}

	public function setTachanunRecitedFridays(bool $tachanunRecitedFridays): self
	{
		$this->tachanunRecitedFridays = $tachanunRecitedFridays;

		return $this;
	}

	public function getTachanunRecitedSundays(): bool
	{
		return $this->tachanunRecitedSundays;
	}

	public function setTachanunRecitedSundays(bool $tachanunRecitedSundays): self
	{
		$this->tachanunRecitedSundays = $tachanunRecitedSundays;

		return $this;
	}

	public function getTachanunRecitedMinchaAllYear(): bool
	{
		return $this->tachanunRecitedMinchaAllYear;
	}

	public function setTachanunRecitedMinchaAllYear(bool $tachanunRecitedMinchaAllYear): self
	{
		$this->tachanunRecitedMinchaAllYear = $tachanunRecitedMinchaAllYear;

		return $this;
	}

	public function getMizmorLesodaRecitedErevYomKippurAndPesach(): bool
	{
		return $this->mizmorLesodaRecitedErevYomKippurAndPesach;
	}

	public function setMizmorLesodaRecitedErevYomKippurAndPesach(bool $mizmorLesodaRecitedErevYomKippurAndPesach): self
	{
		$this->mizmorLesodaRecitedErevYomKippurAndPesach = $mizmorLesodaRecitedErevYomKippurAndPesach;

		return $this;
	}
}
