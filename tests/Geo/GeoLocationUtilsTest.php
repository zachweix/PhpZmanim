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

use PHPUnit\Framework\TestCase;
use PhpZmanim\Geo\GeoLocation;
use PhpZmanim\Geo\GeoLocationUtils;

class GeoLocationUtilsTest extends TestCase {

	/** 
	 * @test
	 */
	public function testVincentyFormulae() {
		$pointA = new GeoLocation("",
			[50, 3, 58.76, "N"],
			[5, 42, 53.1, "W"],
			0, "Etc/GMT+12");
		$pointB = new GeoLocation("",
			[58, 38, 38.48, "N"],
			[3, 4, 12.34, "W"],
			0, "Etc/GMT+12");

		$this->assertEquals(GeoLocationUtils::getGeodesicInitialBearing($pointA, $pointB), 9.141861908318441);
		$this->assertEquals(GeoLocationUtils::getGeodesicFinalBearing($pointA, $pointB), 11.297201271086411);
		$this->assertEquals(GeoLocationUtils::getGeodesicDistance($pointA, $pointB), 969954.114450429);
	}

	/** 
	 * @test
	 */
	public function testRhumbLineBearing() {
		$pointA = new GeoLocation("",
			[50, 3, 59, "N"],
			[5, 42, 53, "W"],
			0, "Etc/GMT+12");
		$pointB = new GeoLocation("",
			[58, 38, 38, "N"],
			[3, 4, 12, "W"],
			0, "Etc/GMT+12");

		$this->assertEquals(GeoLocationUtils::getRhumbLineBearing($pointA, $pointB), 10.14069287904878);
	}

	/** 
	 * @test
	 */
	public function testRhumbLineDistance() {
		$pointA = new GeoLocation("",
			[50, 3, 59, "N"],
			[5, 42, 53, "W"],
			0, "Etc/GMT+12");
		$pointB = new GeoLocation("",
			[58, 38, 38, "N"],
			[3, 4, 12, "W"],
			0, "Etc/GMT+12");

		$this->assertEquals(GeoLocationUtils::getRhumbLineDistance($pointA, $pointB), 969995.8368007989);
	}
}