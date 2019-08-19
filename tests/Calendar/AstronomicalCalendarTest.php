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
use PhpZmanim\Calendar\AstronomicalCalendar;
use PhpZmanim\Geo\GeoLocation;

class AstronomicalCalendarTest extends TestCase {

	protected $locations;

	protected function setUp(): void {
		parent::setUp();

		/*
		 * Setup some basic data for our tests
		 */

		$this->locations = [
			['Lakewood, NJ', 40.0721087, -74.2400243, 15, 'America/New_York'],
			['Jerusalem, Israel', 31.7781161, 35.233804, 740, 'Asia/Jerusalem'],
			['Los Angeles, CA', 34.0201613, -118.6919095, 71, 'America/Los_Angeles'],
			['Tokyo, Japan', 35.6733227, 139.6403486, 40, 'Asia/Tokyo'],
			['Fort Conger, NU Canada', 81.7449398, -64.7945858, 127, 'America/Toronto'],
			['Apia, Samoa', -13.8599098, -171.8031745, 1858, 'Pacific/Apia'],
		];
	}

	/** 
	 * @test
	 */
	public function testSunrise() {
		$expected_dates = [
			"2017-10-17T07:09:11-04:00",
			"2017-10-17T06:39:32+03:00",
			"2017-10-17T07:00:25-07:00",
			"2017-10-17T05:48:20+09:00",
			null,
			"2017-10-17T06:54:18+14:00",
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunrise = $astronomicalCalendar->getSunrise();
			if (!is_null($sunrise)) {
				$sunrise = $sunrise->format('Y-m-d\TH:i:sP');
			}

			$this->assertEquals($sunrise, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testSeaLevelSunrise() {
		$expected_dates = [
			"2017-10-17T07:09:51-04:00",
			"2017-10-17T06:43:43+03:00",
			"2017-10-17T07:01:45-07:00",
			"2017-10-17T05:49:21+09:00",
			null,
			"2017-10-17T07:00:05+14:00",
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunrise = $astronomicalCalendar->getSeaLevelSunrise();
			if (!is_null($sunrise)) {
				$sunrise = $sunrise->format('Y-m-d\TH:i:sP');
			}

			$this->assertEquals($sunrise, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testUTCSunrise() {
		$expected_dates = [
			11.15327065,
			3.65893934,
			14.00708152,
			20.8057012,
			null,
			16.90510688,
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunrise = $astronomicalCalendar->getUTCSunrise(90);
			if (is_nan($sunrise)) {
				$sunrise = null;
			} else {
				$sunrise = round($sunrise, 8);
			}

			$this->assertEquals($sunrise, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testUTCSeaLevelSunrise() {
		$expected_dates = [
			11.16434723,
			3.72862262,
			14.02926518,
			20.82268461,
			null,
			17.00158411,
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunrise = $astronomicalCalendar->getUTCSeaLevelSunrise(90);
			if (is_nan($sunrise)) {
				$sunrise = null;
			} else {
				$sunrise = round($sunrise, 8);
			}

			$this->assertEquals($sunrise, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testSunriseOffsetByDegreesForBasicLocations() {
		$expected_dates = [
			"2017-10-17T06:10:57-04:00",
			"2017-10-17T05:50:43+03:00",
			"2017-10-17T06:07:22-07:00",
			"2017-10-17T04:53:55+09:00",
			"2017-10-17T04:47:28-04:00",
			"2017-10-17T06:13:13+14:00",
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunrise = $astronomicalCalendar->getSunriseOffsetByDegrees(102);
			if (!is_null($sunrise)) {
				$sunrise = $sunrise->format('Y-m-d\TH:i:sP');
			}

			$this->assertEquals($sunrise, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testSunriseOffsetByDegreesForArcticTimeZoneExtremities() {
		$geo = new GeoLocation('Daneborg, Greenland', 74.2999996, -20.2420877, 0, 'America/Godthab');

		$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 4, 20);

		$sunrise = $astronomicalCalendar->getSunriseOffsetByDegrees(94);
		$sunrise = $sunrise->format('Y-m-d\TH:i:sP');

		$this->assertEquals($sunrise, '2017-04-19T23:54:23-02:00');
	}

	/** 
	 * @test
	 */
	public function testSunset() {
		$expected_dates = [
			"2017-10-17T18:14:38-04:00",
			"2017-10-17T18:08:46+03:00",
			"2017-10-17T18:19:05-07:00",
			"2017-10-17T17:04:46+09:00",
			null,
			"2017-10-17T19:31:07+14:00",
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunset = $astronomicalCalendar->getSunset();
			if (!is_null($sunset)) {
				$sunset = $sunset->format('Y-m-d\TH:i:sP');
			}

			$this->assertEquals($sunset, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testSeaLevelSunset() {
		$expected_dates = [
			"2017-10-17T18:13:58-04:00",
			"2017-10-17T18:04:36+03:00",
			"2017-10-17T18:17:45-07:00",
			"2017-10-17T17:03:45+09:00",
			null,
			"2017-10-17T19:25:19+14:00",
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunset = $astronomicalCalendar->getSeaLevelSunset();
			if (!is_null($sunset)) {
				$sunset = $sunset->format('Y-m-d\TH:i:sP');
			}

			$this->assertEquals($sunset, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testUTCSunset() {
		$expected_dates = [
			22.24410903,
			15.14635336,
			1.31819979,
			8.07962871,
			null,
			5.51873532,
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunset = $astronomicalCalendar->getUTCSunset(90);
			if (is_nan($sunset)) {
				$sunset = null;
			} else {
				$sunset = round($sunset, 8);
			}

			$this->assertEquals($sunset, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testUTCSeaLevelSunset() {
		$expected_dates = [
			22.23304301,
			15.07671429,
			1.29603174,
			8.06265871,
			null,
			5.42214918,
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunset = $astronomicalCalendar->getUTCSeaLevelSunset(90);
			if (is_nan($sunset)) {
				$sunset = null;
			} else {
				$sunset = round($sunset, 8);
			}

			$this->assertEquals($sunset, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testSunsetOffsetByDegreesForBasicLocations() {
		$expected_dates = [
			"2017-10-17T19:12:49-04:00",
			"2017-10-17T18:57:33+03:00",
			"2017-10-17T19:12:05-07:00",
			"2017-10-17T17:59:08+09:00",
			"2017-10-17T19:15:04-04:00",
			"2017-10-17T20:12:15+14:00",
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunset = $astronomicalCalendar->getSunsetOffsetByDegrees(102);
			if (!is_null($sunset)) {
				$sunset = $sunset->format('Y-m-d\TH:i:sP');
			}

			$this->assertEquals($sunset, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testSunsetOffsetByDegreesForArcticTimeZoneExtremities() {
		$geo = new GeoLocation('Hooper Bay, Alaska', 61.520182, -166.1740437, 8, 'America/Anchorage');

		$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 6, 21);

		$sunset = $astronomicalCalendar->getSunsetOffsetByDegrees(94);
		$sunset = $sunset->format('Y-m-d\TH:i:sP');

		$this->assertEquals($sunset, '2017-06-22T02:00:16-08:00');
	}

	/** 
	 * @test
	 */
	public function testTemporalHour() {
		$expected_dates = [
			0.92239132,
			0.94567431,
			0.93889721,
			0.93666451,
			null,
			1.03504709,
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);
			$sunset = $astronomicalCalendar->getTemporalHour();
			if (!is_null($sunset)) {
				$sunset = $sunset / 3600;
				$sunset = round($sunset, 8);
			}

			$this->assertEquals($sunset, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testSunTransit() {
		$expected_dates = [
			"2017-10-17T12:41:55-04:00",
			"2017-10-17T12:24:09+03:00",
			"2017-10-17T12:39:45-07:00",
			"2017-10-17T11:26:33+09:00",
			null,
			"2017-10-17T13:12:42+14:00",
		];

		foreach ($this->locations as $index => $location) {
			$geo = new GeoLocation($location[0], $location[1], $location[2], $location[3], $location[4]);

			$astronomicalCalendar = new AstronomicalCalendar($geo, 2017, 10, 17);

			$sunTransit = $astronomicalCalendar->getSunTransit();
			if (!is_null($sunTransit)) {
				$sunTransit = $sunTransit->format('Y-m-d\TH:i:sP');
			}

			$this->assertEquals($sunTransit, $expected_dates[ $index ]);
		}
	}

	/** 
	 * @test
	 */
	public function testDefaultData() {
		$geo = new GeoLocation();

		$astronomicalCalendar1 = new AstronomicalCalendar();
		$astronomicalCalendar2 = new AstronomicalCalendar(null, 1);
		$astronomicalCalendar3 = new AstronomicalCalendar($geo, 1, 1);

		$this->assertEquals($astronomicalCalendar1->getGeoLocation(), $geo);
		$this->assertEquals($astronomicalCalendar2, $astronomicalCalendar3);
	}
}