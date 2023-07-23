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

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use PhpZmanim\Geo\GeoLocation;
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calculator\NoaaCalculator;

class NoaaCalculatorTest extends TestCase {

	/** 
	 * @test
	 */
	public function testCalculatorName() {
		$noaaCalculator = new NoaaCalculator();
		$this->assertEquals($noaaCalculator->getCalculatorName(), "US National Oceanic and Atmospheric Administration Algorithm");
	}

	/** 
	 * @test
	 */
	public function testGetUTCSunrise() {
		$noaaCalculator = new NoaaCalculator();

		$tests = [
			['2017-10-17', 41.1181036, -74.0840691, 167, 11.13543634],
			['2017-10-17', 34.0201613, -118.6919095, 71, 14.00708152],
			['1955-02-26', 31.7962994, 35.1053185, 754, 4.11885084],
			['2017-06-21', 70.1498248, 9.1456867, 0, false],
		];

		foreach ($tests as $test) {
			$calendar = Carbon::parse($test[0]);
			$geo = new GeoLocation("", $test[1], $test[2], $test[3], "UTC");

			$sunrise = $noaaCalculator->getUTCSunrise($calendar, $geo, AstronomicalCalculator::GEOMETRIC_ZENITH, true);
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
		$noaaCalculator = new NoaaCalculator();

		$tests = [
			['2017-10-17', 41.1181036, -74.0840691, 167, 22.24078702],
			['1955-02-26', 31.7962994, 35.1053185, 754, 15.64531391],
			['2017-06-21', 70.1498248, 9.1456867, 0, false],
		];

		foreach ($tests as $test) {
			$calendar = Carbon::parse($test[0]);
			$geo = new GeoLocation("", $test[1], $test[2], $test[3], "UTC");

			$sunset = $noaaCalculator->getUTCSunset($calendar, $geo, AstronomicalCalculator::GEOMETRIC_ZENITH, true);
			if (is_nan($sunset)) {
				$sunset = false;
			} else {
				$sunset = round($sunset, 8);
			}

			$this->assertEquals($sunset, $test[4]);
		}
	}
}