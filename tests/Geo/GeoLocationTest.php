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

use PHPUnit\Framework\TestCase;
use PhpZmanim\Geo\GeoLocation;

class GeoLocationTest extends TestCase {

	/** @test */
	public function createGeoLocation() {
		$geoLocation = new GeoLocation();

		$this->assertEquals($geoLocation->getLocationName(), null);
		$this->assertEquals($geoLocation->getLatitude(), 51.4772);
		$this->assertEquals($geoLocation->getLongitude(), 0.0);
		$this->assertEquals($geoLocation->getElevation(), 0.0);
		$this->assertEquals($geoLocation->getTimeZone(), "GMT");
	}

	/** @test */
	public function createGeoLocationWithData() {
		$geoLocation = new GeoLocation("Lakewood, NJ", 40.0828, -74.2094, 20, "America/New_York");

		$this->assertEquals($geoLocation->getLocationName(), "Lakewood, NJ");
		$this->assertEquals($geoLocation->getLatitude(), 40.0828);
		$this->assertEquals($geoLocation->getLongitude(), -74.2094);
		$this->assertEquals($geoLocation->getElevation(), 20.0);
		$this->assertEquals($geoLocation->getTimeZone(), "America/New_York");

		return [
			'geo' => $geoLocation,
		];
	}

	/** 
	 * @test
	 */
	public function updateLatitudeAndLongitude() {
		$geoLocation = new GeoLocation("Lakewood, NJ", 40.0828, -74.2094, 20, "America/New_York");

		$geoLocation->setLatitudeFromDegrees(41, 7, 5.17296, 'N');
		$this->assertEquals($geoLocation->getLatitude(), 41.1181036);

		$geoLocation->setLatitudeFromDegrees(41, 7, 5.17296, 'S');
		$this->assertEquals($geoLocation->getLatitude(), -41.1181036);

		$geoLocation->setLongitudeFromDegrees(41, 7, 5.17296, 'E');
		$this->assertEquals($geoLocation->getLongitude(), 41.1181036);

		$geoLocation->setLongitudeFromDegrees(41, 7, 5.17296, 'W');
		$this->assertEquals($geoLocation->getLongitude(), -41.1181036);
	}

	/** 
	 * @test
	 */
	public function testAntimeridianAdjustmentGmt() {
		$geoLocation = new GeoLocation();

		$this->assertEquals($geoLocation->getAntimeridianAdjustment(), 0);
	}

	/** 
	 * @test
	 */
	public function testAntimeridianAdjustmentNy() {
		$geoLocation = new GeoLocation("Lakewood, NJ", 40.0828, -74.2094, 20, "America/New_York");

		$this->assertEquals($geoLocation->getAntimeridianAdjustment(), 0);
	}

	/** 
	 * @test
	 */
	public function testAntimeridianAdjustmentEast() {
		$geoLocation = new GeoLocation("Apia, Samoa", -13.8599098, -171.8031745, 1858, "Pacific/Apia");

		$this->assertEquals($geoLocation->getAntimeridianAdjustment(), -1);
	}

	/** 
	 * @test
	 */
	public function testAntimeridianAdjustmentWest() {
		$geoLocation = new GeoLocation("GMT +12", -13.8599098, 179, 0, "Etc/GMT+12");

		$this->assertEquals($geoLocation->getAntimeridianAdjustment(), 1);
	}

	/** 
	 * @test
	 */
	public function testVincentyFormulae() {
		$pointA = new GeoLocation("", 0 , 0, 0, "Etc/GMT+12");
		$pointA->setLatitudeFromDegrees(50, 3, 58.76, "N");
		$pointA->setLongitudeFromDegrees(5, 42, 53.1, "W");

		$pointB = new GeoLocation("", 0 , 0, 0, "Etc/GMT+12");
		$pointB->setLatitudeFromDegrees(58, 38, 38.48, "N");
		$pointB->setLongitudeFromDegrees(3, 4, 12.34, "W");

		$initialBearing = $pointA->getGeodesicInitialBearing($pointB);
		$finalBearing = $pointA->getGeodesicFinalBearing($pointB);
		$distance = $pointA->getGeodesicDistance($pointB);

		$this->assertEquals(round($initialBearing, 8), 9.14186191);
		$this->assertEquals(round($finalBearing, 8), 11.29720127);
		$this->assertEquals(round($distance, 8), 969954.11445043);
	}

	/** 
	 * @test
	 */
	public function testRhumbLineBearing() {
		$pointA = new GeoLocation("", 0 , 0, 0, "Etc/GMT+12");
		$pointA->setLatitudeFromDegrees(50, 3, 59, "N");
		$pointA->setLongitudeFromDegrees(5, 42, 53, "W");

		$pointB = new GeoLocation("", 0 , 0, 0, "Etc/GMT+12");
		$pointB->setLatitudeFromDegrees(58, 38, 38, "N");
		$pointB->setLongitudeFromDegrees(3, 4, 12, "W");

		$rhumbLineBearing = $pointA->getRhumbLineBearing($pointB);

		$this->assertEquals(round($rhumbLineBearing, 8), 10.14069288);
	}

	/** 
	 * @test
	 */
	public function testRhumbLineDistance() {
		$pointA = new GeoLocation("", 0 , 0, 0, "Etc/GMT+12");
		$pointA->setLatitudeFromDegrees(50, 3, 59, "N");
		$pointA->setLongitudeFromDegrees(5, 42, 53, "W");

		$pointB = new GeoLocation("", 0 , 0, 0, "Etc/GMT+12");
		$pointB->setLatitudeFromDegrees(58, 38, 38, "N");
		$pointB->setLongitudeFromDegrees(3, 4, 12, "W");

		$rhumbLineDistance = $pointA->getRhumbLineDistance($pointB);

		$this->assertEquals(round($rhumbLineDistance, 8), 969995.8368008);
	}
}