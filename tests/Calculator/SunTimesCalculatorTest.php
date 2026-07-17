<?php

/**
 * Zmanim PHP API
 * Copyright (C) 2019-2026 Zachary Weixelbaum
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
use PHPUnit\Framework\Attributes\DataProvider;
use PhpZmanim\GeoLocation;
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calculator\SunTimesCalculator;

class SunTimesCalculatorTest extends TestCase
{
	use CalculatorTestLocations;

	/*
	|--------------------------------------------------------------------------
	| CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private const ZENITH = AstronomicalCalculator::GEOMETRIC_ZENITH;

	/*
	|--------------------------------------------------------------------------
	| HELPERS
	|--------------------------------------------------------------------------
	*/

	/**
	 * Build a GeoLocation from a labeled ['lat', 'lon', 'elev'] array. Named
	 * arguments keep the call self-documenting against GeoLocation::create()'s
	 * (latitude, longitude, elevation, timezone, locationName) signature.
	 */
	private function geo(array $loc): GeoLocation
	{
		return GeoLocation::create(
			latitude: $loc['lat'],
			longitude: $loc['lon'],
			elevation: $loc['elev'],
		);
	}

	/**
	 * Assert a UTC-hour result: an exact NaN when the event does not occur
	 * (expected === false), otherwise equal to 8 decimals.
	 */
	private function assertUtcHour($expected, float $actual): void
	{
		if ($expected === false) {
			$this->assertNan($actual);
		} else {
			$this->assertEqualsWithDelta($expected, $actual, 1e-8);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| DATA PROVIDERS
	|--------------------------------------------------------------------------
	| Expected values are KosherJava ground truth (SunTimesCalculator). Cases span
	| both hemispheres, the equator, the dateline, polar no-event, a leap day, a
	| year boundary and a future date. The USNO algorithm returns NaN for noon and
	| midnight at the polar location, unlike the other calculators. String keys
	| label each case.
	*/

	public static function sunriseProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::$NJ, 11.12643745],
			'LA Standard Date' => ['2017-10-17', self::$LA, 14.00076197],
			'Jerusalem Hist. Date' => ['1955-02-26', self::$JERUSALEM, 4.11037446],
			'Norway Polar Solstice' => ['2017-06-21', self::$NORWAY, false],
			'Sydney Leap Day' => ['2020-02-29', self::$SYDNEY, 19.68725081],
			'Macapa Equator New Year' => ['2000-01-01', self::$MACAPA, 9.39464043],
			'Suva Fiji Solstice' => ['2023-06-21', self::$SUVA, 18.60351329],
			'Ushuaia Southern Winter' => ['2017-06-21', self::$USHUAIA, 12.95527929],
			'NJ Future DeltaT' => ['2100-07-04', self::$NJ, 9.43972880],
		];
	}

	public static function sunsetProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::$NJ, 22.25383390],
			'LA Standard Date' => ['2017-10-17', self::$LA, 1.32889810],
			'Jerusalem Hist. Date' => ['1955-02-26', self::$JERUSALEM, 15.65101844],
			'Norway Polar Solstice' => ['2017-06-21', self::$NORWAY, false],
			'Sydney Leap Day' => ['2020-02-29', self::$SYDNEY, 8.56114080],
			'Macapa Equator New Year' => ['2000-01-01', self::$MACAPA, 21.53593703],
			'Suva Fiji Solstice' => ['2023-06-21', self::$SUVA, 5.65641338],
			'Ushuaia Southern Winter' => ['2017-06-21', self::$USHUAIA, 20.20896383],
			'NJ Future DeltaT' => ['2100-07-04', self::$NJ, 0.57971462],
		];
	}

	public static function noonProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::$NJ, 16.69011947],
			'LA Standard Date' => ['2017-10-17', self::$LA, 19.66482200],
			'Jerusalem Hist. Date' => ['1955-02-26', self::$JERUSALEM, 9.88071908],
			'Norway Polar No Noon' => ['2017-06-21', self::$NORWAY, false],
			'Sydney Leap Day' => ['2020-02-29', self::$SYDNEY, 2.12420286],
			'Macapa Equator New Year' => ['2000-01-01', self::$MACAPA, 15.46529012],
			'Suva Fiji Solstice' => ['2023-06-21', self::$SUVA, 0.12996327],
			'Ushuaia Southern Winter' => ['2017-06-21', self::$USHUAIA, 16.58212152],
			'NJ Future DeltaT' => ['2100-07-04', self::$NJ, 17.00973685],
		];
	}

	public static function midnightProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::$NJ, 4.69011947],
			'LA Standard Date' => ['2017-10-17', self::$LA, 7.66482200],
			'Jerusalem Hist. Date' => ['1955-02-26', self::$JERUSALEM, 21.88071908],
			'Norway Polar No Midnight' => ['2017-06-21', self::$NORWAY, false],
			'Sydney Leap Day' => ['2020-02-29', self::$SYDNEY, 14.12420286],
			'Macapa Equator New Year' => ['2000-01-01', self::$MACAPA, 3.46529012],
			'Suva Fiji Solstice' => ['2023-06-21', self::$SUVA, 12.12996327],
			'Ushuaia Southern Winter' => ['2017-06-21', self::$USHUAIA, 4.58212152],
			'NJ Future DeltaT' => ['2100-07-04', self::$NJ, 5.00973685],
		];
	}

	public static function sunriseZenithProvider(): array
	{
		return [
			'NJ Civil' => ['2017-10-17', self::$NJ, 96.0, 10.70056986],
			'NJ Nautical' => ['2017-10-17', self::$NJ, 102.0, 10.16784691],
			'NJ Astronomical' => ['2017-10-17', self::$NJ, 108.0, 9.63665090],
			'Sydney Civil' => ['2020-02-29', self::$SYDNEY, 96.0, 19.28346936],
		];
	}

	public static function sunriseNoElevationProvider(): array
	{
		return [
			'NJ No Elev Adj' => ['2017-10-17', self::$NJ, 11.16388056],
			'Jerusalem No Elev Adj' => ['1955-02-26', self::$JERUSALEM, 4.18050398],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| SUNRISE / SUNSET
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('sunriseProvider')]
	public function getUTCSunrise(string $date, array $location, $expected): void
	{
		$actual = SunTimesCalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), self::ZENITH, true
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('sunsetProvider')]
	public function getUTCSunset(string $date, array $location, $expected): void
	{
		$actual = SunTimesCalculator::create()->getUTCSunset(
			Carbon::parse($date), $this->geo($location), self::ZENITH, true
		);
		$this->assertUtcHour($expected, $actual);
	}

	/*
	|--------------------------------------------------------------------------
	| NOON / MIDNIGHT
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('noonProvider')]
	public function getUTCNoon(string $date, array $location, $expected): void
	{
		$actual = SunTimesCalculator::create()->getUTCNoon(Carbon::parse($date), $this->geo($location));
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('midnightProvider')]
	public function getUTCMidnight(string $date, array $location, $expected): void
	{
		$actual = SunTimesCalculator::create()->getUTCMidnight(Carbon::parse($date), $this->geo($location));
		$this->assertUtcHour($expected, $actual);
	}

	/*
	|--------------------------------------------------------------------------
	| ZENITH / ELEVATION FLAG VARIATIONS
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('sunriseZenithProvider')]
	public function getUTCSunriseAtZenith(string $date, array $location, float $zenith, $expected): void
	{
		$actual = SunTimesCalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), $zenith, true
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('sunriseNoElevationProvider')]
	public function getUTCSunriseWithoutElevationAdjustment(string $date, array $location, $expected): void
	{
		$actual = SunTimesCalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), self::ZENITH, false
		);
		$this->assertUtcHour($expected, $actual);
	}

	/*
	|--------------------------------------------------------------------------
	| UNSUPPORTED OPERATIONS
	|--------------------------------------------------------------------------
	| SunTimesCalculator does not implement these; each must throw.
	*/

	#[Test]
	public function getTimeAtAzimuth(): void
	{
		$this->expectException(\BadMethodCallException::class);
		SunTimesCalculator::create()->getTimeAtAzimuth(
			Carbon::parse('2017-10-17'),
			$this->geo(self::$NJ),
			90.0
		);
	}

	#[Test]
	public function getSolarElevation(): void
	{
		$this->expectException(\BadMethodCallException::class);
		SunTimesCalculator::create()->getSolarElevation(
			Carbon::parse('2017-10-17'),
			$this->geo(self::$NJ),
			90.0
		);
	}

	#[Test]
	public function getSolarAzimuth(): void
	{
		$this->expectException(\BadMethodCallException::class);
		SunTimesCalculator::create()->getSolarAzimuth(
			Carbon::parse('2017-10-17'),
			$this->geo(self::$NJ),
			90.0
		);
	}
}
