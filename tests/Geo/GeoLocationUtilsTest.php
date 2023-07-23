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
use PhpZmanim\Geo\GeoLocationUtils;

class GeoLocationUtilsTest extends TestCase {

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

		$this->assertEquals(GeoLocationUtils::getGeodesicInitialBearing($pointA, $pointB), 9.141861908318441);
		$this->assertEquals(GeoLocationUtils::getGeodesicFinalBearing($pointA, $pointB), 11.297201271086411);
		$this->assertEquals(GeoLocationUtils::getGeodesicDistance($pointA, $pointB), 969954.114450429);
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

		$this->assertEquals(GeoLocationUtils::getRhumbLineBearing($pointA, $pointB), 10.14069287904878);
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

		$this->assertEquals(GeoLocationUtils::getRhumbLineDistance($pointA, $pointB), 969995.8368007989);
	}
}