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

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use PhpZmanim\Geo\GeoLocation;
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calculator\SunTimesCalculator;

class SunTimesCalculatorTest extends TestCase {

	/** 
	 * @test
	 */
	public function testCalculatorName() {
		$sunTimesCalculator = new SunTimesCalculator();
		$this->assertEquals($sunTimesCalculator->getCalculatorName(), "US Naval Almanac Algorithm");
	}

	/** 
	 * @test
	 */
	public function testGetUTCSunrise() {
		$sunTimesCalculator = new SunTimesCalculator();

		$tests = [
			['2017-10-17', 41.1181036, -74.0840691, 0.167, 11.16276401],
			['1955-02-26', 31.7962994, 35.1053185, 0.754, 4.17848602],
			['2017-06-21', 70.1498248, 9.1456867, 0, false],
		];

		foreach ($tests as $test) {
			$calendar = Carbon::parse($test[0]);
			$geo = new GeoLocation("", $test[1], $test[2], $test[3], "America/New_York");

			$sunrise = $sunTimesCalculator->getUTCSunrise($calendar, $geo, AstronomicalCalculator::GEOMETRIC_ZENITH, true);
			if (is_nan($sunrise)) {
				$sunrise = false;
			} else {
				$sunrise = round($sunrise, 8);
			}

			$this->assertEquals($sunrise, $test[4]);
		}
	}

	/** 
	 * @test
	 */
	public function testGetUTCSunset() {
		$sunTimesCalculator = new SunTimesCalculator();

		$tests = [
			['2017-10-17', 41.1181036, -74.0840691, 0.167, 22.21747591],
			['1955-02-26', 31.7962994, 35.1053185, 0.754, 15.58295081],
			['2017-06-21', 70.1498248, 9.1456867, 0, false],
		];

		foreach ($tests as $test) {
			$calendar = Carbon::parse($test[0]);
			$geo = new GeoLocation("", $test[1], $test[2], $test[3], "America/New_York");

			$sunset = $sunTimesCalculator->getUTCSunset($calendar, $geo, AstronomicalCalculator::GEOMETRIC_ZENITH, true);
			if (is_nan($sunset)) {
				$sunset = false;
			} else {
				$sunset = round($sunset, 8);
			}

			$this->assertEquals($sunset, $test[4]);
		}
	}
}