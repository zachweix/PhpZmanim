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
use PhpZmanim\GeoLocation;
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calculator\SPACalculator;

class SPACalculatorTest extends TestCase {

	/**
	 * The published NREL Solar Position Algorithm reference case (Reda & Andreas,
	 * NREL/TP-560-34302): 2003-10-17 12:30:30 local (timezone -7, so 19:30:30 UTC),
	 * longitude -105.1786, latitude 39.742476, elevation 1830.14 m, pressure 820 mb,
	 * temperature 11 C, delta-T 67 s. The paper reports a topocentric zenith of
	 * 50.111622 (elevation 39.888378) and an azimuth of 194.340241. The abridged
	 * VSOP87 tables used here reproduce these to well under an arc-second.
	 */
	private function nrelCalculator() {
		return SPACalculator::create()
			->setDeltaTOverride(67.0)
			->setPressure(820)
			->setTemperature(11);
	}

	private function nrelInstant() {
		return Carbon::parse('2003-10-17 19:30:30', 'UTC');
	}

	private function nrelLocation() {
		return new GeoLocation(39.742476, -105.1786, 'UTC', 1830.14, '');
	}

	/**
	 * @test
	 */
	public function testGetSolarElevation() {
		$elevation = $this->nrelCalculator()->getSolarElevation($this->nrelInstant(), $this->nrelLocation());
		$this->assertEqualsWithDelta(39.888378, $elevation, 0.001);
	}

	/**
	 * @test
	 */
	public function testGetSolarAzimuth() {
		$azimuth = $this->nrelCalculator()->getSolarAzimuth($this->nrelInstant(), $this->nrelLocation());
		$this->assertEqualsWithDelta(194.340241, $azimuth, 0.001);
	}

	/**
	 * @test
	 */
	public function testGetUTCSunrise() {
		$spaCalculator = SPACalculator::create();

		$tests = [
			['2017-10-17', 41.1181036, -74.0840691, 167, 11.13593689],
			['2017-10-17', 34.0201613, -118.6919095, 71, 14.00756754],
			['1955-02-26', 31.7962994, 35.1053185, 754, 4.11873932],
			['2017-06-21', 70.1498248, 9.1456867, 0, false],
		];

		foreach ($tests as $test) {
			$calendar = Carbon::parse($test[0]);
			$geo = new GeoLocation($test[1], $test[2], 'UTC', $test[3], '');

			$sunrise = $spaCalculator->getUTCSunrise($calendar, $geo, AstronomicalCalculator::GEOMETRIC_ZENITH, true);
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
		$spaCalculator = SPACalculator::create();

		$tests = [
			['2017-10-17', 41.1181036, -74.0840691, 167, 22.24099415],
			['2017-10-17', 34.0201613, -118.6919095, 71, 1.31843857],
			['1955-02-26', 31.7962994, 35.1053185, 754, 15.645123],
			['2017-06-21', 70.1498248, 9.1456867, 0, false],
		];

		foreach ($tests as $test) {
			$calendar = Carbon::parse($test[0]);
			$geo = new GeoLocation($test[1], $test[2], 'UTC', $test[3], '');

			$sunset = $spaCalculator->getUTCSunset($calendar, $geo, AstronomicalCalculator::GEOMETRIC_ZENITH, true);
			if (is_nan($sunset)) {
				$sunset = false;
			} else {
				$sunset = round($sunset, 8);
			}

			$this->assertEquals($sunset, $test[4]);
		}
	}

	/**
	 * @test
	 */
	public function testGetUTCNoon() {
		$spaCalculator = SPACalculator::create();
		$calendar = Carbon::parse('2017-10-17');
		$geo = new GeoLocation(41.1181036, -74.0840691, 'UTC', 167, '');

		$noon = round($spaCalculator->getUTCNoon($calendar, $geo), 8);

		$this->assertEquals($noon, 16.69350885);
	}

	/**
	 * @test
	 */
	public function testGetUTCMidnight() {
		$spaCalculator = SPACalculator::create();
		$calendar = Carbon::parse('2017-10-17');
		$geo = new GeoLocation(41.1181036, -74.0840691, 'UTC', 167, '');

		$midnight = round($spaCalculator->getUTCMidnight($calendar, $geo), 8);

		$this->assertEquals($midnight, 4.69189152);
	}

	/**
	 * @test
	 */
	public function testGetTimeAtAzimuth() {
		$spaCalculator = SPACalculator::create();
		$calendar = Carbon::parse('2017-10-17');
		$geo = new GeoLocation(41.1181036, -74.0840691, 'UTC', 167, '');

		$this->assertEquals(round($spaCalculator->getTimeAtAzimuth($calendar, $geo, 90.0), 8), 9.96630382);
		$this->assertEquals(round($spaCalculator->getTimeAtAzimuth($calendar, $geo, 270.0), 8), 23.43696229);
	}

	/**
	 * @test
	 */
	public function testGetTimeAtAzimuthThrowsForUnsupportedAzimuth() {
		$spaCalculator = SPACalculator::create();
		$calendar = Carbon::parse('2017-10-17');
		$geo = new GeoLocation(41.1181036, -74.0840691, 'UTC', 167, '');

		$this->expectException(\InvalidArgumentException::class);
		$spaCalculator->getTimeAtAzimuth($calendar, $geo, 123.0);
	}

	/**
	 * @test
	 */
	public function testDefaultsAndFluentSetters() {
		$spaCalculator = SPACalculator::create();

		$this->assertEquals($spaCalculator->getApplyDeltaT(), true);
		$this->assertEquals($spaCalculator->getDeltaTOverride(), null);
		$this->assertEquals($spaCalculator->getPressure(), 1013.25);
		$this->assertEquals($spaCalculator->getTemperature(), 10.0);

		$returned = $spaCalculator->setDeltaTOverride(67.0)
			->setPressure(820)
			->setTemperature(11)
			->setApplyDeltaT(false);

		$this->assertSame($returned, $spaCalculator);
		$this->assertEquals($spaCalculator->getDeltaTOverride(), 67.0);
		$this->assertEquals($spaCalculator->getPressure(), 820.0);
		$this->assertEquals($spaCalculator->getTemperature(), 11.0);
		$this->assertEquals($spaCalculator->getApplyDeltaT(), false);
	}
}
