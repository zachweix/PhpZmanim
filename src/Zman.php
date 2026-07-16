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

namespace PhpZmanim;

use Carbon\Carbon;
use InvalidArgumentException;
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calculator\NoaaCalculator;
use PhpZmanim\Calculator\SunTimesCalculator;
use PhpZmanim\GeoLocation;

class Zman
{
	use Zman\Creator;
	use Zman\Utilities;
	use Zman\Helpers;
	use Zman\Alos;
	use Zman\Misheyakir;
	use Zman\Sunrise;
	use Zman\SofZmanShma;
	use Zman\SofZmanTfila;
	use Zman\Noon;
	use Zman\MinchaGedola;
	use Zman\MinchaKetana;
	use Zman\Plag;
	use Zman\Sunset;
	use Zman\BainHashmashos;
	use Zman\Midnight;
	use Zman\Azimuth;
	use Zman\ShaahZmanis;
	use Zman\Tzais;
	use Zman\Melacha;
	use Zman\Chametz;
	use Zman\BaalHatanya;
	use Zman\AteretTorah;
	use Zman\Polar;
	use Zman\Molad;

	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	protected Carbon $date;
	private GeoLocation $geoLocation;
	private AstronomicalCalculator $astronomicalCalculator;
	private bool $useElevation = false;
	private float $candleLightingOffset = 18;
	private bool $useAstronomicalChatzos = true;
	private bool $useAstronomicalChatzosForOtherZmanim = false;
	private float $ateretTorahSunsetOffset = 40;

	const GEOMETRIC_ZENITH = 90;
	const CIVIL_ZENITH = 96;
	const NAUTICAL_ZENITH = 102;
	const ASTRONOMICAL_ZENITH = 108;

	const MINUTE_MILLIS = 60 * 1000;
	const HOUR_MILLIS = self::MINUTE_MILLIS * 60;

	const ZENITH_16_POINT_1 = self::GEOMETRIC_ZENITH + 16.1;
	const ZENITH_8_POINT_5 = self::GEOMETRIC_ZENITH + 8.5;
	const ZENITH_1_POINT_583 = self::GEOMETRIC_ZENITH + 1.583;

	const ZENITH_3_POINT_7 = self::GEOMETRIC_ZENITH + 3.7;
	const ZENITH_3_POINT_8 = self::GEOMETRIC_ZENITH + 3.8;
	const ZENITH_5_POINT_95 = self::GEOMETRIC_ZENITH + 5.95;
	const ZENITH_7_POINT_083 = self::GEOMETRIC_ZENITH + 7 + (5.0 / 60);
	const ZENITH_10_POINT_2 = self::GEOMETRIC_ZENITH + 10.2;
	const ZENITH_11_DEGREES = self::GEOMETRIC_ZENITH + 11;
	const ZENITH_11_POINT_5 = self::GEOMETRIC_ZENITH + 11.5;
	const ZENITH_12_POINT_85 = self::GEOMETRIC_ZENITH + 12.85;
	const ZENITH_13_POINT_24 = self::GEOMETRIC_ZENITH + 13.24;
	const ZENITH_19_DEGREES = self::GEOMETRIC_ZENITH + 19;
	const ZENITH_19_POINT_8 = self::GEOMETRIC_ZENITH + 19.8;
	const ZENITH_26_DEGREES = self::GEOMETRIC_ZENITH + 26.0;
	const ZENITH_4_POINT_42 = self::GEOMETRIC_ZENITH + 4.42;
	const ZENITH_4_POINT_66 = self::GEOMETRIC_ZENITH + 4.66;
	const ZENITH_4_POINT_8 = self::GEOMETRIC_ZENITH + 4.8;
	const ZENITH_16_POINT_9 = self::GEOMETRIC_ZENITH + 16.9;
	const ZENITH_6_DEGREES = self::GEOMETRIC_ZENITH + 6;
	const ZENITH_6_POINT_45 = self::GEOMETRIC_ZENITH + 6.45;
	const ZENITH_7_POINT_65 = self::GEOMETRIC_ZENITH + 7.65;
	const ZENITH_7_POINT_67 = self::GEOMETRIC_ZENITH + 7.67;
	const ZENITH_9_POINT_3 = self::GEOMETRIC_ZENITH + 9.3;
	const ZENITH_9_POINT_5 = self::GEOMETRIC_ZENITH + 9.5;
	const ZENITH_9_POINT_75 = self::GEOMETRIC_ZENITH + 9.75;
	const ZENITH_MINUS_2_POINT_1 = self::GEOMETRIC_ZENITH - 2.1;
	const ZENITH_MINUS_2_POINT_8 = self::GEOMETRIC_ZENITH - 2.8;
	const ZENITH_MINUS_3_POINT_05 = self::GEOMETRIC_ZENITH - 3.05;

	const SUNRISE = 0;
	const SUNSET = 1;
	const NOON = 2;
	const MIDNIGHT = 3;

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function getDate(): Carbon
	{
		return $this->date->copy();
	}

	public function setDate(Carbon|int|null $date = null, ?int $month = null, ?int $day = null): self
	{
		if ($date instanceof Carbon) {
			$this->date = $date->copy()->startOfDay();

			return $this;
		}

		$allNull = is_null($date) && is_null($month) && is_null($day);
		$allSet = !is_null($date) && !is_null($month) && !is_null($day);

		if (!$allNull && !$allSet) {
			throw new InvalidArgumentException('You must either provide a year, month and day or leave them all blank');
		}

		$this->date = ($allNull
			? Carbon::now($this->geoLocation->getTimezone())
			: Carbon::create($date, $month, $day, 0, 0, 0, $this->geoLocation->getTimezone())
		)->startOfDay();

		return $this;
	}

	public function addDays($value): self
	{
		$this->date->addDays($value);

		return $this;
	}

	public function subDays($value): self
	{
		$this->date->subDays($value);

		return $this;
	}

	public function getGeoLocation(): GeoLocation
	{
		return $this->geoLocation->copy();
	}

	public function setGeoLocation(GeoLocation $geoLocation): self
	{
		$this->geoLocation = $geoLocation->copy();

		return $this;
	}

	public function getAstronomicalCalculator(): AstronomicalCalculator
	{
		return $this->astronomicalCalculator->copy();
	}

	public function setAstronomicalCalculator(AstronomicalCalculator $astronomicalCalculator): self
	{
		$this->astronomicalCalculator = $astronomicalCalculator->copy();

		return $this;
	}

	public function getUseElevation(): bool
	{
		return $this->useElevation;
	}

	public function setUseElevation(bool $useElevation): self
	{
		$this->useElevation = $useElevation;

		return $this;
	}

	public function getCandleLightingOffset(): float
	{
		return $this->candleLightingOffset;
	}

	public function setCandleLightingOffset(float $candleLightingOffset): self
	{
		$this->candleLightingOffset = $candleLightingOffset;

		return $this;
	}

	public function getUseAstronomicalChatzos(): bool
	{
		return $this->useAstronomicalChatzos;
	}

	public function setUseAstronomicalChatzos(bool $useAstronomicalChatzos): self
	{
		$this->useAstronomicalChatzos = $useAstronomicalChatzos;

		return $this;
	}

	public function getUseAstronomicalChatzosForOtherZmanim(): bool
	{
		return $this->useAstronomicalChatzosForOtherZmanim;
	}

	public function setUseAstronomicalChatzosForOtherZmanim(bool $useAstronomicalChatzosForOtherZmanim): self
	{
		$this->useAstronomicalChatzosForOtherZmanim = $useAstronomicalChatzosForOtherZmanim;

		return $this;
	}

	public function getAteretTorahSunsetOffset(): float
	{
		return $this->ateretTorahSunsetOffset;
	}

	public function setAteretTorahSunsetOffset(float $ateretTorahSunsetOffset): self
	{
		$this->ateretTorahSunsetOffset = $ateretTorahSunsetOffset;

		return $this;
	}
}