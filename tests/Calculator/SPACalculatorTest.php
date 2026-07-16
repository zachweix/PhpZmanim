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
use PhpZmanim\Calculator\SPACalculator;

class SPACalculatorTest extends TestCase
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
	| Expected values are KosherJava ground truth (SPACalculator with defaults),
	| verified to match to 8 decimals. Cases span both hemispheres, the equator,
	| the dateline, polar no-event, a leap day, a year boundary and a future ΔT
	| date. String keys label each case.
	*/

	public static function sunriseProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 11.13593689],
			'LA Standard Date' => ['2017-10-17', self::LA, 14.00756754],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 4.11873932],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, false],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 19.68293661],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 9.38998685],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 18.60534965],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 12.95741199],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 9.44912615],
		];
	}

	public static function sunsetProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 22.24099415],
			'LA Standard Date' => ['2017-10-17', self::LA, 1.31843857],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 15.64512300],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, false],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 8.56366884],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 21.53102552],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 5.65791427],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 20.21191830],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 0.58028674],
		];
	}

	public static function noonProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 16.69350885],
			'LA Standard Date' => ['2017-10-17', self::LA, 19.66696009],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 9.87817106],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, 11.42056214],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 2.12799525],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 15.46051831],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 0.13165740],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 16.58459266],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 17.01689317],
		];
	}

	public static function midnightProvider(): array
	{
		return [
			'NJ Standard Date' => ['2017-10-17', self::NJ, 4.69189152],
			'LA Standard Date' => ['2017-10-17', self::LA, 7.66535335],
			'Jerusalem Hist. Date' => ['1955-02-26', self::JERUSALEM, 21.87680310],
			'Norway Polar Solstice' => ['2017-06-21', self::NORWAY, 23.42238534],
			'Sydney Leap Day' => ['2020-02-29', self::SYDNEY, 14.12642351],
			'Macapa Equator New Year' => ['2000-01-01', self::MACAPA, 3.46446119],
			'Suva Fiji Solstice' => ['2023-06-21', self::SUVA, 12.13348552],
			'Ushuaia Southern Winter' => ['2017-06-21', self::USHUAIA, 4.58641480],
			'NJ Future DeltaT' => ['2100-07-04', self::NJ, 5.01835716],
		];
	}

	public static function timeAtAzimuthProvider(): array
	{
		return [
			'NJ East' => ['2017-10-17', self::NJ, 90.0, 9.96630382],
			'NJ West' => ['2017-10-17', self::NJ, 270.0, 23.43696229],
			'LA East' => ['2017-10-17', self::LA, 90.0, 12.71802376],
			'LA West' => ['2017-10-17', self::LA, 270.0, 2.63792595],
			'Sydney East' => ['2020-02-29', self::SYDNEY, 90.0, 20.92601636],
			'Sydney West' => ['2020-02-29', self::SYDNEY, 270.0, 7.34685680],
			'Macapa East No Cross' => ['2000-01-01', self::MACAPA, 90.0, false],
			'Polar East' => ['2017-06-21', self::NORWAY, 90.0, 6.01986373],
		];
	}

	public static function solarElevationProvider(): array
	{
		return [
			'NJ Noon' => ['2017-10-17 12:00:00', self::NJ, 8.24142367],
			'NJ Evening' => ['2017-10-17 18:30:00', self::NJ, 33.57165658],
			'LA Evening' => ['2017-10-17 20:00:00', self::LA, 46.19391692],
			'Polar Midnight' => ['2017-06-21 00:00:00', self::NORWAY, 3.98549481],
			'Sydney Morning' => ['2020-02-29 20:00:00', self::SYDNEY, 2.89087872],
			'Macapa Local Noon' => ['2000-01-01 15:00:00', self::MACAPA, 65.99118480],
			'Suva Afternoon' => ['2023-06-21 02:00:00', self::SUVA, 40.24649656],
		];
	}

	public static function solarAzimuthProvider(): array
	{
		return [
			'NJ Noon' => ['2017-10-17 12:00:00', self::NJ, 110.13757333],
			'NJ Evening' => ['2017-10-17 18:30:00', self::NJ, 212.62157928],
			'LA Evening' => ['2017-10-17 20:00:00', self::LA, 187.12533185],
			'Polar Midnight' => ['2017-06-21 00:00:00', self::NORWAY, 8.01127958],
			'Sydney Morning' => ['2020-02-29 20:00:00', self::SYDNEY, 97.32308776],
			'Macapa Local Noon' => ['2000-01-01 15:00:00', self::MACAPA, 164.22300003],
			'Suva Afternoon' => ['2023-06-21 02:00:00', self::SUVA, 325.62786587],
		];
	}

	public static function sunriseZenithProvider(): array
	{
		return [
			'NJ Civil' => ['2017-10-17', self::NJ, 96.0, 10.70943974],
			'NJ Nautical' => ['2017-10-17', self::NJ, 102.0, 10.17613961],
			'NJ Astronomical' => ['2017-10-17', self::NJ, 108.0, 9.64457480],
			'Sydney Civil' => ['2020-02-29', self::SYDNEY, 96.0, 19.27874463],
		];
	}

	public static function sunriseNoElevationProvider(): array
	{
		return [
			'NJ No Elev Adj' => ['2017-10-17', self::NJ, 11.17344334],
			'Jerusalem No Elev Adj' => ['1955-02-26', self::JERUSALEM, 4.18885558],
		];
	}

	public static function deltaTOffProvider(): array
	{
		return [
			'NJ dT off' => ['2017-10-17', self::NJ, 11.13586818],
			'Jerusalem dT off' => ['1955-02-26', self::JERUSALEM, 4.11871824],
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
		$actual = SPACalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), self::ZENITH, true
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('sunsetProvider')]
	public function getUTCSunset(string $date, array $location, $expected): void
	{
		$actual = SPACalculator::create()->getUTCSunset(
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
		$actual = SPACalculator::create()->getUTCNoon(Carbon::parse($date), $this->geo($location));
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('midnightProvider')]
	public function getUTCMidnight(string $date, array $location, $expected): void
	{
		$actual = SPACalculator::create()->getUTCMidnight(Carbon::parse($date), $this->geo($location));
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
		$actual = SPACalculator::create()->getTimeAtAzimuth(
			Carbon::parse($date), $this->geo($location), $azimuth
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	public function getTimeAtAzimuthRejectsUnsupportedAzimuth(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		SPACalculator::create()->getTimeAtAzimuth(
			Carbon::parse('2017-10-17'),
			$this->geo(self::NJ),
			123.0
		);
	}

	/*
	|--------------------------------------------------------------------------
	| SOLAR ELEVATION / AZIMUTH (default configuration)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('solarElevationProvider')]
	public function getSolarElevation(string $datetime, array $location, float $expected): void
	{
		$actual = SPACalculator::create()->getSolarElevation(
			Carbon::parse($datetime, 'UTC'), $this->geo($location)
		);
		$this->assertEqualsWithDelta($expected, $actual, 1e-8);
	}

	#[Test]
	#[DataProvider('solarAzimuthProvider')]
	public function getSolarAzimuth(string $datetime, array $location, float $expected): void
	{
		$actual = SPACalculator::create()->getSolarAzimuth(
			Carbon::parse($datetime, 'UTC'), $this->geo($location)
		);
		$this->assertEqualsWithDelta($expected, $actual, 1e-8);
	}

	/*
	|--------------------------------------------------------------------------
	| NREL PUBLISHED REFERENCE CASE
	|--------------------------------------------------------------------------
	| The published NREL Solar Position Algorithm reference case (Reda & Andreas,
	| NREL/TP-560-34302): 2003-10-17 12:30:30 local (timezone -7, so 19:30:30 UTC),
	| longitude -105.1786, latitude 39.742476, elevation 1830.14 m, pressure 820 mb,
	| temperature 11 C, delta-T 67 s. The paper reports a topocentric elevation of
	| 39.888378 and an azimuth of 194.340241. KosherJava reproduces these to well
	| under an arc-second (elevation 39.88835527, azimuth 194.34023294), and the PHP
	| port matches KosherJava, so the published values are asserted with a 0.001 delta.
	*/

	private function nrelCalculator(): SPACalculator
	{
		return SPACalculator::create()
			->setDeltaTOverride(67.0)
			->setPressure(820)
			->setTemperature(11);
	}

	private function nrelInstant(): Carbon
	{
		return Carbon::parse('2003-10-17 19:30:30', 'UTC');
	}

	private function nrelLocation(): GeoLocation
	{
		return $this->geo(['lat' => 39.742476, 'lon' => -105.1786, 'elev' => 1830.14]);
	}

	#[Test]
	public function getSolarElevationNrelReference(): void
	{
		$elevation = $this->nrelCalculator()->getSolarElevation($this->nrelInstant(), $this->nrelLocation());
		$this->assertEqualsWithDelta(39.888378, $elevation, 0.001);
	}

	#[Test]
	public function getSolarAzimuthNrelReference(): void
	{
		$azimuth = $this->nrelCalculator()->getSolarAzimuth($this->nrelInstant(), $this->nrelLocation());
		$this->assertEqualsWithDelta(194.340241, $azimuth, 0.001);
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
		$actual = SPACalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), $zenith, true
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('sunriseNoElevationProvider')]
	public function getUTCSunriseWithoutElevationAdjustment(string $date, array $location, $expected): void
	{
		$actual = SPACalculator::create()->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), self::ZENITH, false
		);
		$this->assertUtcHour($expected, $actual);
	}

	#[Test]
	#[DataProvider('deltaTOffProvider')]
	public function getUTCSunriseWithDeltaTDisabled(string $date, array $location, $expected): void
	{
		$actual = SPACalculator::create()->setApplyDeltaT(false)->getUTCSunrise(
			Carbon::parse($date), $this->geo($location), self::ZENITH, true
		);
		$this->assertUtcHour($expected, $actual);
	}

	/*
	|--------------------------------------------------------------------------
	| DEFAULTS / FLUENT SETTERS
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function defaultsAndFluentSetters(): void
	{
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
