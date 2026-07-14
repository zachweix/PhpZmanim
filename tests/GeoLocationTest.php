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

class GeoLocationTest extends TestCase {

	/**
	 * Convert degrees/minutes/seconds to signed decimal degrees, matching the
	 * formula the removed setLatitudeFromDegrees()/setLongitudeFromDegrees()
	 * helpers used, so the geodesic reference values stay identical.
	 */
	private function dms($degrees, $minutes, $seconds, $direction) {
		$value = $degrees + (($minutes + ($seconds / 60.0)) / 60.0);
		return ($direction === 'S' || $direction === 'W') ? -$value : $value;
	}

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTION
	|--------------------------------------------------------------------------
	*/

	/** @test */
	public function constructWithDefaults() {
		$geoLocation = new GeoLocation();

		$this->assertEquals($geoLocation->getLatitude(), 51.4772);
		$this->assertEquals($geoLocation->getLongitude(), 0.0);
		$this->assertEquals($geoLocation->getTimezone(), 'GMT');
		$this->assertEquals($geoLocation->getElevation(), 0.0);
		$this->assertEquals($geoLocation->getLocationName(), null);
	}

	/** @test */
	public function constructWithData() {
		$geoLocation = new GeoLocation(40.0828, -74.2094, 'America/New_York', 20, 'Lakewood, NJ');

		$this->assertEquals($geoLocation->getLatitude(), 40.0828);
		$this->assertEquals($geoLocation->getLongitude(), -74.2094);
		$this->assertEquals($geoLocation->getTimezone(), 'America/New_York');
		$this->assertEquals($geoLocation->getElevation(), 20.0);
		$this->assertEquals($geoLocation->getLocationName(), 'Lakewood, NJ');
	}

	/** @test */
	public function createFactoryMatchesConstructor() {
		$geoLocation = GeoLocation::create(40.0828, -74.2094, 'America/New_York', 20, 'Lakewood, NJ');

		$this->assertEquals($geoLocation->getLatitude(), 40.0828);
		$this->assertEquals($geoLocation->getLongitude(), -74.2094);
		$this->assertEquals($geoLocation->getTimezone(), 'America/New_York');
		$this->assertEquals($geoLocation->getElevation(), 20.0);
		$this->assertEquals($geoLocation->getLocationName(), 'Lakewood, NJ');
	}

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	/** @test */
	public function settersAreFluentAndUpdateValues() {
		$geoLocation = new GeoLocation();

		$returned = $geoLocation->setLatitude(41.1181036)
			->setLongitude(-74.2094)
			->setTimezone('America/New_York')
			->setElevation(20)
			->setLocationName('Lakewood, NJ');

		$this->assertSame($returned, $geoLocation);
		$this->assertEquals($geoLocation->getLatitude(), 41.1181036);
		$this->assertEquals($geoLocation->getLongitude(), -74.2094);
		$this->assertEquals($geoLocation->getTimezone(), 'America/New_York');
		$this->assertEquals($geoLocation->getElevation(), 20.0);
		$this->assertEquals($geoLocation->getLocationName(), 'Lakewood, NJ');
	}

	/** @test */
	public function setLatitudeRejectsOutOfRange() {
		$this->expectException(\InvalidArgumentException::class);
		(new GeoLocation())->setLatitude(90.1);
	}

	/** @test */
	public function setLatitudeRejectsNaN() {
		$this->expectException(\InvalidArgumentException::class);
		(new GeoLocation())->setLatitude(NAN);
	}

	/** @test */
	public function setLongitudeRejectsOutOfRange() {
		$this->expectException(\InvalidArgumentException::class);
		(new GeoLocation())->setLongitude(-180.1);
	}

	/** @test */
	public function setLongitudeRejectsNaN() {
		$this->expectException(\InvalidArgumentException::class);
		(new GeoLocation())->setLongitude(NAN);
	}

	/** @test */
	public function setElevationRejectsNegative() {
		$this->expectException(\InvalidArgumentException::class);
		(new GeoLocation())->setElevation(-1);
	}

	/** @test */
	public function setElevationRejectsInfinite() {
		$this->expectException(\InvalidArgumentException::class);
		(new GeoLocation())->setElevation(INF);
	}

	/** @test */
	public function constructorValidatesLatitude() {
		$this->expectException(\InvalidArgumentException::class);
		new GeoLocation(100);
	}

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	/** @test */
	public function getLocalMeanTimeOffset() {
		$datetime = Carbon::parse('2017-01-01', 'UTC');

		$gmt = new GeoLocation();
		$this->assertEquals($gmt->getLocalMeanTimeOffset($datetime), 0.0);

		$lakewood = new GeoLocation(40.0828, -74.2094, 'America/New_York', 20, 'Lakewood, NJ');
		$this->assertEquals($lakewood->getLocalMeanTimeOffset($datetime), 189744.0);
	}

	/** @test */
	public function antimeridianAdjustmentGmt() {
		$datetime = Carbon::parse('2017-01-01', 'UTC');
		$geoLocation = new GeoLocation();

		$this->assertEquals($geoLocation->getAntimeridianAdjustment($datetime), 0);
	}

	/** @test */
	public function antimeridianAdjustmentNy() {
		$datetime = Carbon::parse('2017-01-01', 'UTC');
		$geoLocation = new GeoLocation(40.0828, -74.2094, 'America/New_York', 20, 'Lakewood, NJ');

		$this->assertEquals($geoLocation->getAntimeridianAdjustment($datetime), 0);
	}

	/** @test */
	public function antimeridianAdjustmentEast() {
		$datetime = Carbon::parse('2017-01-01', 'UTC');
		$geoLocation = new GeoLocation(-13.8599098, -171.8031745, 'Pacific/Apia', 1858, 'Apia, Samoa');

		$this->assertEquals($geoLocation->getAntimeridianAdjustment($datetime), -1);
	}

	/** @test */
	public function antimeridianAdjustmentWest() {
		$datetime = Carbon::parse('2017-01-01', 'UTC');
		$geoLocation = new GeoLocation(-13.8599098, 179, 'Etc/GMT+12', 0, 'GMT +12');

		$this->assertEquals($geoLocation->getAntimeridianAdjustment($datetime), 1);
	}

	/*
	|--------------------------------------------------------------------------
	| GEODESIC FORMULAS
	|--------------------------------------------------------------------------
	*/

	/** @test */
	public function vincentyFormulae() {
		$pointA = new GeoLocation($this->dms(50, 3, 58.76, 'N'), $this->dms(5, 42, 53.1, 'W'), 'Etc/GMT+12');
		$pointB = new GeoLocation($this->dms(58, 38, 38.48, 'N'), $this->dms(3, 4, 12.34, 'W'), 'Etc/GMT+12');

		$this->assertEquals(round($pointA->getGeodesicInitialBearing($pointB), 8), 9.14186191);
		$this->assertEquals(round($pointA->getGeodesicFinalBearing($pointB), 8), 11.29720127);
		$this->assertEquals(round($pointA->getGeodesicDistance($pointB), 8), 969954.11445043);
	}

	/** @test */
	public function geodesicDistanceForCoincidentPointsIsZero() {
		$point = new GeoLocation(40.0828, -74.2094, 'America/New_York');

		$this->assertEquals($point->getGeodesicDistance($point), 0);
	}

	/** @test */
	public function rhumbLineBearing() {
		$pointA = new GeoLocation($this->dms(50, 3, 59, 'N'), $this->dms(5, 42, 53, 'W'), 'Etc/GMT+12');
		$pointB = new GeoLocation($this->dms(58, 38, 38, 'N'), $this->dms(3, 4, 12, 'W'), 'Etc/GMT+12');

		$this->assertEquals(round($pointA->getRhumbLineBearing($pointB), 8), 10.14069288);
	}

	/** @test */
	public function rhumbLineDistance() {
		$pointA = new GeoLocation($this->dms(50, 3, 59, 'N'), $this->dms(5, 42, 53, 'W'), 'Etc/GMT+12');
		$pointB = new GeoLocation($this->dms(58, 38, 38, 'N'), $this->dms(3, 4, 12, 'W'), 'Etc/GMT+12');

		$this->assertEquals(round($pointA->getRhumbLineDistance($pointB), 8), 969995.8368008);
	}

	/*
	|--------------------------------------------------------------------------
	| CLONEABLE
	|--------------------------------------------------------------------------
	*/

	/** @test */
	public function copyReturnsIndependentClone() {
		$geoLocation = new GeoLocation(40.0828, -74.2094, 'America/New_York', 20, 'Lakewood, NJ');
		$copy = $geoLocation->copy();

		$this->assertNotSame($geoLocation, $copy);
		$this->assertEquals($copy->getLatitude(), 40.0828);

		$copy->setLatitude(10);
		$this->assertEquals($geoLocation->getLatitude(), 40.0828);
		$this->assertEquals($copy->getLatitude(), 10.0);
	}
}
