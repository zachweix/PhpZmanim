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
use PhpZmanim\Calculator\MeeusCalculator;

class MeeusCalculatorTest extends TestCase
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
	| Expected values are KosherJava ground truth (MeeusCalculator with defaults,
	| applyDeltaT on), verified to match to 8 decimals. Cases span both
	| hemispheres, the equator, the dateline, polar no-event, a leap day, a year
	| boundary and a future ΔT date. String keys label each case.
	*/

	public static function sunriseProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 11.13565507],
			'LA Standard Date' => ['2017-10-17', self::LA, 14.00730639],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 4.11849856],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, false],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 19.68267444],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 9.38974837],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 18.60510228],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 12.95697826],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 9.44873166],
		];
	}

	public static function sunsetProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 22.24115497],
			'LA Standard Date' => ['2017-10-17', self::LA, 1.31857880],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 15.64527430],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, false],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 8.56380822],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 21.53114700],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 5.65803778],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 20.21223110],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 0.58039250],
		];
	}

	public static function noonProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 16.69344845],
			'LA Standard Date' => ['2017-10-17', self::LA, 19.66689969],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 9.87812628],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, 11.42050168],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 2.12793408],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 15.46045983],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 0.13159547],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 16.58453220],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 17.01674900],
		];
	}

	public static function midnightProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 4.69183111],
			'LA Standard Date' => ['2017-10-17', self::LA, 7.66529295],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 21.87675832],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, 23.42232487],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 14.12636235],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 3.46440271],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 12.13342359],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 4.58635433],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 5.01821298],
		];
	}

	public static function timeAtAzimuthProvider(): array
	{
		return [
			'NJ East' => ['2017-10-17', self::NJ, 90.0, 9.96630242],
			'NJ West' => ['2017-10-17', self::NJ, 270.0, 23.43696025],
			'LA East' => ['2017-10-17', self::LA, 90.0, 12.71802243],
			'LA West' => ['2017-10-17', self::LA, 270.0, 2.63792382],
			'Sydney East' => ['2020-02-29', self::SYDNEY, 90.0, 20.92601400],
			'Sydney West' => ['2020-02-29', self::SYDNEY, 270.0, 7.34685529],
			'Macapa East No Cross' => ['2000-01-01', self::MACAPA, 90.0, false],
			'Polar East' => ['2017-06-21', self::NORWAY, 90.0, 6.01986159],
		];
	}

	public static function solarElevationProvider(): array
	{
		return [
			'NJ Noon' => ['2017-10-17 12:00:00', self::NJ, 8.24234881],
			'NJ Evening' => ['2017-10-17 18:30:00', self::NJ, 33.57213757],
			'LA Evening' => ['2017-10-17 20:00:00', self::LA, 46.19473579],
			'Polar Midnight' => ['2017-06-21 00:00:00', self::NORWAY, 3.98711013],
			'Sydney Morning' => ['2020-02-29 20:00:00', self::SYDNEY, 2.89445709],
			'Macapa Local Noon' => ['2000-01-01 15:00:00', self::MACAPA, 65.99208585],
			'Suva Afternoon' => ['2023-06-21 02:00:00', self::SUVA, 40.24688524],
		];
	}

	public static function solarAzimuthProvider(): array
	{
		return [
			'NJ Noon' => ['2017-10-17 12:00:00', self::NJ, 110.13820751],
			'NJ Evening' => ['2017-10-17 18:30:00', self::NJ, 212.62255322],
			'LA Evening' => ['2017-10-17 20:00:00', self::LA, 187.12661462],
			'Polar Midnight' => ['2017-06-21 00:00:00', self::NORWAY, 8.01211355],
			'Sydney Morning' => ['2020-02-29 20:00:00', self::SYDNEY, 97.32257044],
			'Macapa Local Noon' => ['2000-01-01 15:00:00', self::MACAPA, 164.22488663],
			'Suva Afternoon' => ['2023-06-21 02:00:00', self::SUVA, 325.62695543],
		];
	}

	public static function sunriseZenithProvider(): array
	{
		return [
			'NJ Civil' => ['2017-10-17', self::NJ, 96.0, 10.70916170],
			'NJ Nautical' => ['2017-10-17', self::NJ, 102.0, 10.17586674],
			'NJ Astronomical' => ['2017-10-17', self::NJ, 108.0, 9.64430764],
			'Sydney Civil' => ['2020-02-29', self::SYDNEY, 96.0, 19.27848095],
		];
	}

	public static function sunriseNoElevationProvider(): array
	{
		return [
			'NJ No Elev Adj' => ['2017-10-17', self::NJ, 11.17316120],
			'Jerusalem No Elev Adj' => ['1955-02-26', self::JERUSALEM, 4.18861444],
		];
	}

	public static function deltaTOffProvider(): array
	{
		return [
			'NJ dT off' => ['2017-10-17', self::NJ, 11.13563996],
			'Jerusalem dT off' => ['1955-02-26', self::JERUSALEM, 4.11850671],
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
		$actual = MeeusCalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), self::ZENITH, true
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('sunsetProvider')]
	public function getUTCSunset(string $date, array $location, $expected): void
	{
		$actual = MeeusCalculator::create()->getUTCSunset(
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
		$actual = MeeusCalculator::create()->getUTCNoon(Carbon::parse($date), $this->geo($location));
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('midnightProvider')]
	public function getUTCMidnight(string $date, array $location, $expected): void
	{
		$actual = MeeusCalculator::create()->getUTCMidnight(Carbon::parse($date), $this->geo($location));
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
		$actual = MeeusCalculator::create()->getTimeAtAzimuth(
			Carbon::parse($date), $this->geo($location), $azimuth
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	public function getTimeAtAzimuthRejectsUnsupportedAzimuth(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		MeeusCalculator::create()->getTimeAtAzimuth(
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
		$actual = MeeusCalculator::create()->getSolarElevation(
			Carbon::parse($datetime, 'UTC'), $this->geo($location)
		);
		$this->assertEqualsWithDelta($expected, $actual, 1e-8);
	}

	#[Test]
	#[DataProvider('solarAzimuthProvider')]
	public function getSolarAzimuth(string $datetime, array $location, float $expected): void
	{
		$actual = MeeusCalculator::create()->getSolarAzimuth(
			Carbon::parse($datetime, 'UTC'), $this->geo($location)
		);
		$this->assertEqualsWithDelta($expected, $actual, 1e-8);
	}

	/*
	|--------------------------------------------------------------------------
	| ZENITH / ELEVATION / ΔT FLAG VARIATIONS
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('sunriseZenithProvider')]
	public function getUTCSunriseAtZenith(string $date, array $location, float $zenith, $expected): void
	{
		$actual = MeeusCalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), $zenith, true
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('sunriseNoElevationProvider')]
	public function getUTCSunriseWithoutElevationAdjustment(string $date, array $location, $expected): void
	{
		$actual = MeeusCalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), self::ZENITH, false
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('deltaTOffProvider')]
	public function getUTCSunriseWithDeltaTDisabled(string $date, array $location, $expected): void
	{
		$actual = MeeusCalculator::create()->setApplyDeltaT(false)->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), self::ZENITH, true
		);
		$this->assertUtcHour($expected, $actual);
	}
}
