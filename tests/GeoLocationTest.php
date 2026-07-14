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
use PHPUnit\Framework\Attributes\Test;
use PhpZmanim\GeoLocation;

class GeoLocationTest extends TestCase
{
	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTION
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function constructWithDefaults(): void
	{
		$geoLocation = GeoLocation::create();

		$this->assertEquals($geoLocation->getLatitude(), 51.4772);
		$this->assertEquals($geoLocation->getLongitude(), 0.0);
		$this->assertEquals($geoLocation->getTimezone(), 'GMT');
		$this->assertEquals($geoLocation->getElevation(), 0.0);
		$this->assertEquals($geoLocation->getLocationName(), null);
	}

	#[Test]
	public function constructWithData(): void
	{
		$geoLocation = GeoLocation::create(latitude: 40.0828, longitude: -74.2094, elevation: 20, timezone: 'America/New_York', locationName: 'Lakewood, NJ');

		$this->assertEquals($geoLocation->getLatitude(), 40.0828);
		$this->assertEquals($geoLocation->getLongitude(), -74.2094);
		$this->assertEquals($geoLocation->getTimezone(), 'America/New_York');
		$this->assertEquals($geoLocation->getElevation(), 20.0);
		$this->assertEquals($geoLocation->getLocationName(), 'Lakewood, NJ');
	}

	#[Test]
	public function createFactoryMatchesConstructor(): void
	{
		$geoLocation = GeoLocation::create(latitude: 40.0828, longitude: -74.2094, elevation: 20, timezone: 'America/New_York', locationName: 'Lakewood, NJ');
		$geoLocation2 = new GeoLocation(latitude: 40.0828, longitude: -74.2094, elevation: 20, timezone: 'America/New_York', locationName: 'Lakewood, NJ');

		$this->assertEquals($geoLocation->getLatitude(), $geoLocation2->getLatitude());
		$this->assertEquals($geoLocation->getLongitude(), $geoLocation2->getLongitude());
		$this->assertEquals($geoLocation->getTimezone(), $geoLocation2->getTimezone());
		$this->assertEquals($geoLocation->getElevation(), $geoLocation2->getElevation());
		$this->assertEquals($geoLocation->getLocationName(), $geoLocation2->getLocationName());
	}

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function settersAreFluentAndUpdateValues(): void
	{
		$geoLocation = GeoLocation::create();

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

	#[Test]
	public function setLatitudeRejectsOutOfRange(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		GeoLocation::create()->setLatitude(90.1);
	}

	#[Test]
	public function setLatitudeRejectsNaN(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		GeoLocation::create()->setLatitude(NAN);
	}

	#[Test]
	public function setLongitudeRejectsOutOfRange(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		GeoLocation::create()->setLongitude(-180.1);
	}

	#[Test]
	public function setLongitudeRejectsNaN(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		GeoLocation::create()->setLongitude(NAN);
	}

	#[Test]
	public function setElevationRejectsNegative(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		GeoLocation::create()->setElevation(-1);
	}

	#[Test]
	public function setElevationRejectsInfinite(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		GeoLocation::create()->setElevation(INF);
	}

	#[Test]
	public function constructorValidatesLatitude(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		GeoLocation::create(latitude: 100);
	}

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function getLocalMeanTimeOffset(): void
	{
		$datetime = Carbon::parse('2017-01-01', 'UTC');

		$gmt = GeoLocation::create();
		$this->assertEquals($gmt->getLocalMeanTimeOffset($datetime), 0.0);

		$lakewood = GeoLocation::create(latitude: 40.0828, longitude: -74.2094, elevation: 20, timezone: 'America/New_York', locationName: 'Lakewood, NJ');
		$this->assertEquals($lakewood->getLocalMeanTimeOffset($datetime), 189744.0);
	}

	#[Test]
	public function antimeridianAdjustmentGmt(): void
	{
		$datetime = Carbon::parse('2017-01-01', 'UTC');
		$geoLocation = GeoLocation::create();

		$this->assertEquals($geoLocation->getAntimeridianAdjustment($datetime), 0);
	}

	#[Test]
	public function antimeridianAdjustmentNy(): void
	{
		$datetime = Carbon::parse('2017-01-01', 'UTC');
		$geoLocation = GeoLocation::create(latitude: 40.0828, longitude: -74.2094, elevation: 20, timezone: 'America/New_York', locationName: 'Lakewood, NJ');

		$this->assertEquals($geoLocation->getAntimeridianAdjustment($datetime), 0);
	}

	#[Test]
	public function antimeridianAdjustmentEast(): void
	{
		$datetime = Carbon::parse('2017-01-01', 'UTC');
		$geoLocation = GeoLocation::create(latitude: -13.8599098, longitude: -171.8031745, elevation: 1858, timezone: 'Pacific/Apia', locationName: 'Apia, Samoa');

		$this->assertEquals($geoLocation->getAntimeridianAdjustment($datetime), -1);
	}

	#[Test]
	public function antimeridianAdjustmentWest(): void
	{
		$datetime = Carbon::parse('2017-01-01', 'UTC');
		$geoLocation = GeoLocation::create(latitude: -13.8599098, longitude: 179, elevation: 0, timezone: 'Etc/GMT+12', locationName: 'GMT +12');

		$this->assertEquals($geoLocation->getAntimeridianAdjustment($datetime), 1);
	}

	/*
	|--------------------------------------------------------------------------
	| GEODESIC FORMULAS
	|--------------------------------------------------------------------------
	| Coordinates are the signed decimal degrees of the classic Ordnance Survey
	| reference points (Cornwall and Scotland). Vincenty uses fractional-second
	| precision; the rhumb-line points are rounded to whole seconds.
	*/

	#[Test]
	public function vincentyFormulae(): void
	{
		$pointA = GeoLocation::create(latitude: 50.06632222222222, longitude: -5.71475, timezone: 'Etc/GMT+12');
		$pointB = GeoLocation::create(latitude: 58.64402222222222, longitude: -3.0700944444444445, timezone: 'Etc/GMT+12');

		$this->assertEquals(round($pointA->getGeodesicInitialBearing($pointB), 8), 9.14186191);
		$this->assertEquals(round($pointA->getGeodesicFinalBearing($pointB), 8), 11.29720127);
		$this->assertEquals(round($pointA->getGeodesicDistance($pointB), 8), 969954.11445043);
	}

	#[Test]
	public function geodesicDistanceForCoincidentPointsIsZero(): void
	{
		$point = GeoLocation::create(latitude: 40.0828, longitude: -74.2094, timezone: 'America/New_York');

		$this->assertEquals($point->getGeodesicDistance($point), 0);
	}

	#[Test]
	public function rhumbLineBearing(): void
	{
		$pointA = GeoLocation::create(latitude: 50.06638888888889, longitude: -5.714722222222222, timezone: 'Etc/GMT+12');
		$pointB = GeoLocation::create(latitude: 58.64388888888889, longitude: -3.07, timezone: 'Etc/GMT+12');

		$this->assertEquals(round($pointA->getRhumbLineBearing($pointB), 8), 10.14069288);
	}

	#[Test]
	public function rhumbLineDistance(): void
	{
		$pointA = GeoLocation::create(latitude: 50.06638888888889, longitude: -5.714722222222222, timezone: 'Etc/GMT+12');
		$pointB = GeoLocation::create(latitude: 58.64388888888889, longitude: -3.07, timezone: 'Etc/GMT+12');

		$this->assertEquals(round($pointA->getRhumbLineDistance($pointB), 8), 969995.8368008);
	}

	/*
	|--------------------------------------------------------------------------
	| CLONEABLE
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function copyReturnsIndependentClone(): void
	{
		$geoLocation = GeoLocation::create(latitude: 40.0828, longitude: -74.2094, elevation: 20, timezone: 'America/New_York', locationName: 'Lakewood, NJ');
		$copy = $geoLocation->copy();

		$this->assertNotSame($geoLocation, $copy);
		$this->assertEquals($copy->getLatitude(), 40.0828);

		$copy->setLatitude(10);
		$this->assertEquals($geoLocation->getLatitude(), 40.0828);
		$this->assertEquals($copy->getLatitude(), 10.0);
	}
}
