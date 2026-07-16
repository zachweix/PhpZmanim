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

namespace PhpZmanim\JewishDate;

use PhpZmanim\JewishDate;
use PhpZmanim\Torah\YomTov;

/**
 * @property int $jewishMonth;
 * @property int $jewishDay;
 * @property int $dayOfWeek;
 * @property bool $inIsrael;
 */
trait Melacha
{
	// The following are from JewishCalendar

	public function isYomTovAssurBemelacha(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $yomTov === YomTov::PESACH || $yomTov === YomTov::SHAVUOS || $yomTov === YomTov::SUCCOS
			|| $yomTov === YomTov::SHEMINI_ATZERES || $yomTov === YomTov::SIMCHAS_TORAH
			|| $yomTov === YomTov::ROSH_HASHANA || $yomTov === YomTov::YOM_KIPPUR;
	}

	public function isAssurBemelacha(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $this->dayOfWeek == 7 || $this->isYomTovAssurBemelacha($yomTov);
	}

	public function isTonightMutarBemelacha(): bool
	{
		if (!$this->isAssurBemelacha()) {
			return false;
		}

		$tomorrow = $this->copy();
		$tomorrow->addDays(1);

		return !$tomorrow->isAssurBemelacha();
	}

	public function hasCandleLighting(?YomTov $yomTov = null): bool
	{
		return $this->isTomorrowShabbosOrYomTov($yomTov);
	}

	public function isTomorrowShabbosOrYomTov(?YomTov $yomTov = null): bool
	{
		$yomTov = $yomTov ?? $this->getYomTov();
		return $this->dayOfWeek == 6 || $this->isErevYomTov($yomTov) || $this->isErevYomTovSheni();
	}

	public function isErevYomTovSheni(): bool
	{
		return ($this->jewishMonth == JewishDate::TISHREI && $this->jewishDay == 1)
			|| (!$this->inIsrael
				&& (($this->jewishMonth == JewishDate::NISSAN && ($this->jewishDay == 15 || $this->jewishDay == 21))
					|| ($this->jewishMonth == JewishDate::TISHREI && ($this->jewishDay == 15 || $this->jewishDay == 22))
					|| ($this->jewishMonth == JewishDate::SIVAN && $this->jewishDay == 6)));
	}
}
