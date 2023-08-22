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

namespace PhpZmanim\Calculator;

use Carbon\Carbon;
use PhpZmanim\Geo\GeoLocation;

abstract class AstronomicalCalculator {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private $refraction;
	private $solarRadius; 
	private $earthRadius;

	const GEOMETRIC_ZENITH = 90;

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTOR
	|--------------------------------------------------------------------------
	*/

	public function __construct() {
		$this->refraction = (double) 34 / 60.0;
		$this->solarRadius = (double) 16 / 60.0; 
		$this->earthRadius = (double) 6356.9;
	}

	public static function create() {
		return new static();
	}

	/*
	|--------------------------------------------------------------------------
	| CALCULATOR
	|--------------------------------------------------------------------------
	*/

	public static function getDefault() {
		return new NoaaCalculator();
	}

	public function getCalculatorName() {
		return static::CALCULATOR_NAME;
	}

	/*
	|--------------------------------------------------------------------------
	| EARTH RADIUS
	|--------------------------------------------------------------------------
	*/

	public function getEarthRadius() {
		return $this->earthRadius;
	}

	public function setEarthRadius(float $earthRadius) {
		$this->earthRadius = $earthRadius;

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| ABSTRACT FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	abstract public function getUTCSunrise(Carbon $calendar, GeoLocation $geoLocation, $zenith, $adjustForElevation);
	abstract public function getUTCSunset(Carbon $calendar, GeoLocation $geoLocation, $zenith, $adjustForElevation);
	abstract public function getUTCNoon(Carbon $calendar, GeoLocation $geoLocation);

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getElevationAdjustment($elevation) {
		$elevationAdjustment = rad2deg( acos($this->earthRadius / ($this->earthRadius + ($elevation / 1000.0))));
		return $elevationAdjustment;
	}

	public function adjustZenith($zenith, $elevation) {
		$adjustedZenith = $zenith;
		if ($zenith == self::GEOMETRIC_ZENITH) {
			$adjustedZenith = $zenith + ($this->getSolarRadius() + $this->getRefraction() + $this->getElevationAdjustment($elevation));
		}
		return $adjustedZenith;
	}

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function getRefraction() {
		return $this->refraction;
	}

	public function setRefraction(float $refraction) {
		$this->refraction = $refraction;

		return $this;
	}

	public function getSolarRadius() {
		return $this->solarRadius;
	}

	public function setSolarRadius(float $solarRadius) {
		$this->solarRadius = $solarRadius;

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| CLONEABLE
	|--------------------------------------------------------------------------
	*/

	public function copy() {
		return clone $this;
	}
}