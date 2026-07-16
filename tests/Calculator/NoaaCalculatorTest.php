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
use PhpZmanim\Calculator\NoaaCalculator;

class NoaaCalculatorTest extends TestCase
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
	| Expected values are KosherJava ground truth (NoaaCalculator with defaults),
	| verified to match to 8 decimals. Cases span both hemispheres, the equator,
	| the dateline, polar no-event, a leap day, a year boundary and a future ΔT
	| date. String keys label each case.
	*/

	public static function sunriseProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 11.13540831],
			'LA Standard Date' => ['2017-10-17', self::LA, 14.00704301],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 4.11872483],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, false],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 19.68246733],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 9.39008078],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 18.60488873],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 12.95743787],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 9.44903860],
		];
	}

	public static function sunsetProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 22.24081501],
			'LA Standard Date' => ['2017-10-17', self::LA, 1.31823827],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 15.64544001],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, false],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 8.56372998],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 21.53146217],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 5.65781259],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 20.21256620],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 0.58065990],
		];
	}

	public static function noonProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 16.69315478],
			'LA Standard Date' => ['2017-10-17', self::LA, 19.66659743],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 9.87832210],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, 11.42090460],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 2.12779094],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 15.46078241],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 0.13137581],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 16.58492880],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 17.01703523],
		];
	}

	public static function midnightProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 4.69150247],
			'LA Standard Date' => ['2017-10-17', self::LA, 7.66495567],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 21.87692914],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, 23.42271259],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 14.12619634],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 3.46470789],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 12.13318970],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 4.58673539],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 5.01848806],
		];
	}

	public static function timeAtAzimuthProvider(): array
	{
		return [
			'NJ East' => ['2017-10-17', self::NJ, 90.0, 9.96599151],
			'NJ West' => ['2017-10-17', self::NJ, 270.0, 23.43667951],
			'LA East' => ['2017-10-17', self::LA, 90.0, 12.71769344],
			'LA West' => ['2017-10-17', self::LA, 270.0, 2.63764230],
			'Sydney East' => ['2020-02-29', self::SYDNEY, 90.0, 20.92605878],
			'Sydney West' => ['2020-02-29', self::SYDNEY, 270.0, 7.34651923],
			'Macapa East No Cross' => ['2000-01-01', self::MACAPA, 90.0, false],
			'Polar East' => ['2017-06-21', self::NORWAY, 90.0, 6.02028371],
		];
	}

	public static function solarElevationProvider(): array
	{
		return [
			'NJ Noon' => ['2017-10-17 12:00:00', self::NJ, 8.24496867],
			'NJ Evening' => ['2017-10-17 18:30:00', self::NJ, 33.56992906],
			'LA Evening' => ['2017-10-17 20:00:00', self::LA, 46.19384884],
			'Polar Midnight' => ['2017-06-21 00:00:00', self::NORWAY, 3.98724677],
			'Sydney Morning' => ['2020-02-29 20:00:00', self::SYDNEY, 2.89748529],
			'Macapa Local Noon' => ['2000-01-01 15:00:00', self::MACAPA, 65.99072692],
			'Suva Afternoon' => ['2023-06-21 02:00:00', self::SUVA, 40.24513182],
		];
	}

	public static function solarAzimuthProvider(): array
	{
		return [
			'NJ Noon' => ['2017-10-17 12:00:00', self::NJ, 110.14145579],
			'NJ Evening' => ['2017-10-17 18:30:00', self::NJ, 212.62717267],
			'LA Evening' => ['2017-10-17 20:00:00', self::LA, 187.13298966],
			'Polar Midnight' => ['2017-06-21 00:00:00', self::NORWAY, 8.00635444],
			'Sydney Morning' => ['2020-02-29 20:00:00', self::SYDNEY, 97.32262775],
			'Macapa Local Noon' => ['2000-01-01 15:00:00', self::MACAPA, 164.21440268],
			'Suva Afternoon' => ['2023-06-21 02:00:00', self::SUVA, 325.62368833],
		];
	}

	public static function sunriseZenithProvider(): array
	{
		return [
			'NJ Civil' => ['2017-10-17', self::NJ, 96.0, 10.70891556],
			'NJ Nautical' => ['2017-10-17', self::NJ, 102.0, 10.17562194],
			'NJ Astronomical' => ['2017-10-17', self::NJ, 108.0, 9.64406486],
			'Sydney Civil' => ['2020-02-29', self::SYDNEY, 96.0, 19.27827196],
		];
	}

	public static function sunriseNoElevationProvider(): array
	{
		return [
			'NJ No Elev Adj' => ['2017-10-17', self::NJ, 11.17291440],
			'Jerusalem No Elev Adj' => ['1955-02-26', self::JERUSALEM, 4.18884065],
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
		$actual = NoaaCalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), self::ZENITH, true
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('sunsetProvider')]
	public function getUTCSunset(string $date, array $location, $expected): void
	{
		$actual = NoaaCalculator::create()->getUTCSunset(
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
		$actual = NoaaCalculator::create()->getUTCNoon(Carbon::parse($date), $this->geo($location));
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('midnightProvider')]
	public function getUTCMidnight(string $date, array $location, $expected): void
	{
		$actual = NoaaCalculator::create()->getUTCMidnight(Carbon::parse($date), $this->geo($location));
		$this->assertUtcHour($expected, $actual);
	}

	/*
	|--------------------------------------------------------------------------
	| TIME AT AZIMUTH
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('timeAtAzimuthProvider')]
	public function getTimeAtAzimuth(string $date, array $location, float $azimuth, $expected): void
	{
		$actual = NoaaCalculator::create()->getTimeAtAzimuth(
			Carbon::parse($date), $this->geo($location), $azimuth
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	public function getTimeAtAzimuthRejectsUnsupportedAzimuth(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		NoaaCalculator::create()->getTimeAtAzimuth(
			Carbon::parse('2017-10-17'),
			$this->geo(self::NJ),
			123.0
		);
	}

	/*
	|--------------------------------------------------------------------------
	| SOLAR ELEVATION / AZIMUTH
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('solarElevationProvider')]
	public function getSolarElevation(string $datetime, array $location, float $expected): void
	{
		$actual = NoaaCalculator::create()->getSolarElevation(
			Carbon::parse($datetime, 'UTC'), $this->geo($location)
		);
		$this->assertEqualsWithDelta($expected, $actual, 1e-8);
	}

	#[Test]
	#[DataProvider('solarAzimuthProvider')]
	public function getSolarAzimuth(string $datetime, array $location, float $expected): void
	{
		$actual = NoaaCalculator::create()->getSolarAzimuth(
			Carbon::parse($datetime, 'UTC'), $this->geo($location)
		);
		$this->assertEqualsWithDelta($expected, $actual, 1e-8);
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
		$actual = NoaaCalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), $zenith, true
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('sunriseNoElevationProvider')]
	public function getUTCSunriseWithoutElevationAdjustment(string $date, array $location, $expected): void
	{
		$actual = NoaaCalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), self::ZENITH, false
		);
		$this->assertUtcHour($expected, $actual);
	}
}
