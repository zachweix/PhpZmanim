<?php

/**
 * Zmanim PHP API
 * Copyright (C) 2019 Zachary Weixelbaum
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

use ArgumentCountError;
use Carbon\Carbon;
use Exception;
use PhpZmanim\Calculator\NoaaCalculator;
use PhpZmanim\Calculator\SunTimesCalculator;
use PhpZmanim\Calendar\ComplexZmanimCalendar;
use PhpZmanim\Geo\GeoLocation;

/**
 * This class is meant to be a wrapper of ComplexZmanimCalendar. You can extend
 * this class to include any Zmanim calculations that aren't included in
 * AstronomicalCalendar, ZmanimCalendar, or ComplexZmanimCalendar
 */
class Zmanim extends ComplexZmanimCalendar {

	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	public function __get($arg) {
		$response = null;

		try {
			$response = $this->get($arg);
		} catch (ArgumentCountError $e) {
			$response = null;
		} catch (Exception $e) {
			$response = null;
		}

		return $response;
	}

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public static function create($year = null, $month = null, $day = null, $locationName = null,
		$latitude = 51.4772, $longitude = 0.0, $elevation = 0.0, $timeZone = "GMT") {
		$geoLocation = new GeoLocation($locationName, $latitude, $longitude, $elevation, $timeZone);

		return new Zmanim($geoLocation, $year, $month, $day);
	}

	public function setCalculatorType($type) {
		switch ($type) {
			case 'SunTimes':
				$this->setAstronomicalCalculator(new NoaaCalculator());
				break;
			
			case 'Noaa':
				$this->setAstronomicalCalculator(new SunTimesCalculator());
				break;
			
			default:
				throw new \Exception("Only SunTimes and Noaa are implemented currently");
				break;
		}
	}

	public function setDate($year, $month, $day) {
		$this->getCalendar()->setDate($year, $month, $day);
	}

	public function addDays($value) {
		$this->getCalendar()->addDays($value);
	}

	public function subDays($value) {
		$this->getCalendar()->subDays($value);
	}

	public function get($zman, ...$args) {
		$method_name = "get" . $zman;
		if (method_exists($this, $method_name)) {
			return $this->$method_name(...$args);
		} else {
			throw new \Exception("Requested Zman does not exist");
		}
	}
}