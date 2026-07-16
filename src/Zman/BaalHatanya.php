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

namespace PhpZmanim\Zman;

use Carbon\Carbon;
use PhpZmanim\Zman;

/**
 * @property Carbon $date;
 * @property GeoLocation $geoLocation;
 * @property AstronomicalCalculator $astronomicalCalculator;
 * @property bool $useElevation;
 * @property int $candleLightingOffset;
 * @property bool $useAstronomicalChatzos;
 * @property bool $useAstronomicalChatzosForOtherZmanim;
 * @property float $ateretTorahSunsetOffset;
 */
trait BaalHatanya
{
	public function getAlosBaalHatanya(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(Zman::ZENITH_16_POINT_9);
	}

	public function getSofZmanShmaBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanShma($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getSofZmanTfilaBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getSofZmanAchilasChametzBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanAchilasChametz($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getSofZmanBiurChametzBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanBiurChametz($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getMinchaGedolaBaalHatanya(): Carbon|null
	{
		return $this->getMinchaGedola($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getMinchaKetanaBaalHatanya(): Carbon|null
	{
		return $this->getMinchaKetana($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getPlagHaminchaBaalHatanya(): Carbon|null
	{
		return $this->getPlagHamincha($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getTzaisBaalHatanya(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(Zman::ZENITH_6_DEGREES);
	}
}