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
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\GeoLocation;
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
trait Creator
{
	public function __construct(Carbon|int|null $year = null, ?int $month = null, ?int $day = null, ?GeoLocation $geoLocation = null, ?AstronomicalCalculator $calculator = null)
	{
		$this->geoLocation = $geoLocation?->copy() ?? GeoLocation::create();
		$this->astronomicalCalculator = $calculator?->copy() ?? AstronomicalCalculator::getDefault();
		$this->setDate($year, $month, $day);
	}

	public static function create(Carbon|int|null $year = null, ?int $month = null, ?int $day = null,
		$latitude = 51.4772, $longitude = 0.0, $elevation = 0.0, $timezone = 'GMT', ?AstronomicalCalculator $calculator = null) {
		$geoLocation = GeoLocation::create($latitude, $longitude, $elevation, $timezone);

		return new static($year, $month, $day, $geoLocation, $calculator);
	}

	public function __clone()
	{
		$this->date = $this->date->copy();
		$this->geoLocation = $this->geoLocation->copy();
		$this->astronomicalCalculator = $this->astronomicalCalculator->copy();
	}

	public function copy(): Zman
	{
		return clone $this;
	}

	public function equals(Zman $zmanim): bool
	{
		if ($this === $zmanim) {
			return true;
		}

		return $this->date->eq($zmanim->getDate()) &&
			$this->geoLocation->equals($zmanim->getGeoLocation()) &&
			$this->astronomicalCalculator->equals($zmanim->getAstronomicalCalculator());
	}
}