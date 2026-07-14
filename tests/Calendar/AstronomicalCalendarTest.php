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
use PHPUnit\Framework\Attributes\DataProvider;
use PhpZmanim\Calendar\AstronomicalCalendar;
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calculator\NoaaCalculator;
use PhpZmanim\Calculator\SunTimesCalculator;
use PhpZmanim\GeoLocation;

class AstronomicalCalendarTest extends TestCase
{
	/*
	|--------------------------------------------------------------------------
	| LOCATIONS
	|--------------------------------------------------------------------------
	*/

	private const NJ = ['lat' => 40.0721087, 'lon' => -74.2400243, 'elev' => 15, 'tz' => 'America/New_York'];
	private const JLM = ['lat' => 31.7781161, 'lon' => 35.233804, 'elev' => 740, 'tz' => 'Asia/Jerusalem'];
	private const CONGER = ['lat' => 81.7449398, 'lon' => -64.7945858, 'elev' => 127, 'tz' => 'America/Toronto'];
	private const APIA = ['lat' => -13.8599098, 'lon' => -171.8031745, 'elev' => 1858, 'tz' => 'Pacific/Apia'];

	/*
	|--------------------------------------------------------------------------
	| HELPERS
	|--------------------------------------------------------------------------
	*/

	private function cal(int $year, int $month, int $day, array $loc, ?AstronomicalCalculator $calculator = null): AstronomicalCalendar
	{
		$geo = GeoLocation::create($loc['lat'], $loc['lon'], $loc['elev'], $loc['tz']);

		return new AstronomicalCalendar($year, $month, $day, $geo, $calculator);
	}

	/**
	 * Assert a returned time matches the KosherJava instant (a UTC ISO-8601 string),
	 * or null when the event does not occur. PHP is microsecond precision and Java
	 * nanosecond, so compare the absolute instant to within a millisecond.
	 */
	private function assertInstant(?string $expectedIso, ?Carbon $actual): void
	{
		if ($expectedIso === null) {
			$this->assertNull($actual);

			return;
		}

		$this->assertNotNull($actual);
		$this->assertEqualsWithDelta(
			Carbon::parse($expectedIso, 'UTC')->getPreciseTimestamp() / 1e6,
			$actual->getPreciseTimestamp() / 1e6,
			0.001
		);
	}

	/*
	|--------------------------------------------------------------------------
	| DATA PROVIDERS
	|--------------------------------------------------------------------------
	| Expected values are KosherJava ground truth (default NOAA calculator). Fort
	| Conger exercises the Arctic no-event (null) path; Apia (near the dateline)
	| exercises the date-transition where the event lands on the prior/next day.
	*/

	public static function sunriseProvider(): array
	{
		return [
			'NJ' => [2017, 10, 17, self::NJ, '2017-10-17T11:09:11.571783718Z'],
			'Jerusalem' => [2017, 10, 17, self::JLM, '2017-10-17T03:39:32.238411775Z'],
			'Fort Conger Polar' => [2017, 6, 21, self::CONGER, null],
			'Apia Dateline' => [2017, 10, 17, self::APIA, '2017-10-16T16:54:18.595524791Z'],
		];
	}

	public static function sunsetProvider(): array
	{
		return [
			'NJ' => [2017, 10, 17, self::NJ, '2017-10-17T22:14:38.994862349Z'],
			'Jerusalem' => [2017, 10, 17, self::JLM, '2017-10-17T15:08:46.815316392Z'],
			'Fort Conger Polar' => [2017, 6, 21, self::CONGER, null],
			'Apia Dateline' => [2017, 10, 17, self::APIA, '2017-10-17T05:31:07.236182413Z'],
		];
	}

	public static function sunTransitProvider(): array
	{
		return [
			'NJ' => [2017, 10, 17, self::NJ, '2017-10-17T16:42:12.781249470Z'],
			'Jerusalem' => [2017, 10, 17, self::JLM, '2017-10-17T09:24:22.754067158Z'],
			'Fort Conger Polar' => [2017, 6, 21, self::CONGER, '2017-06-21T16:21:03.597362955Z'],
			'Apia Dateline' => [2017, 10, 17, self::APIA, '2017-10-16T23:12:36.880248195Z'],
		];
	}

	public static function solarMidnightProvider(): array
	{
		return [
			'NJ' => [2017, 10, 17, self::NJ, '2017-10-18T04:42:06.833038724Z'],
			'Jerusalem' => [2017, 10, 17, self::JLM, '2017-10-17T21:24:16.713111245Z'],
			'Fort Conger Polar' => [2017, 6, 21, self::CONGER, '2017-06-22T04:21:10.101321684Z'],
			'Apia Dateline' => [2017, 10, 17, self::APIA, '2017-10-17T11:12:30.711090221Z'],
		];
	}

	public static function njSurfaceProvider(): array
	{
		return [
			'sea level sunrise' => ['getSeaLevelSunrise', '2017-10-17T11:09:51.403184642Z'],
			'sea level sunset' => ['getSeaLevelSunset', '2017-10-17T22:13:59.201432122Z'],
			'begin civil twilight' => ['getBeginCivilTwilight', '2017-10-17T10:42:27.439221886Z'],
			'begin nautical twilight' => ['getBeginNauticalTwilight', '2017-10-17T10:10:57.242471901Z'],
			'begin astronomical twilight' => ['getBeginAstronomicalTwilight', '2017-10-17T09:39:33.523030241Z'],
			'end civil twilight' => ['getEndCivilTwilight', '2017-10-17T22:41:21.435143220Z'],
			'end nautical twilight' => ['getEndNauticalTwilight', '2017-10-17T23:12:49.151356447Z'],
			'end astronomical twilight' => ['getEndAstronomicalTwilight', '2017-10-17T23:44:09.707296295Z'],
		];
	}

	public static function timeAtAzimuthProvider(): array
	{
		return [
			'due east (90)' => [90.0, '2017-10-17T09:56:55.137696391Z'],
			'due west (270)' => [270.0, '2017-10-17T23:28:31.886147237Z'],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| SUNRISE / SUNSET / NOON / MIDNIGHT
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('sunriseProvider')]
	public function getSunrise(int $year, int $month, int $day, array $location, ?string $expected): void
	{
		$this->assertInstant($expected, $this->cal($year, $month, $day, $location)->getSunrise());
	}

	#[Test]
	#[DataProvider('sunsetProvider')]
	public function getSunset(int $year, int $month, int $day, array $location, ?string $expected): void
	{
		$this->assertInstant($expected, $this->cal($year, $month, $day, $location)->getSunset());
	}

	#[Test]
	#[DataProvider('sunTransitProvider')]
	public function getSunTransit(int $year, int $month, int $day, array $location, ?string $expected): void
	{
		$this->assertInstant($expected, $this->cal($year, $month, $day, $location)->getSunTransit());
	}

	#[Test]
	#[DataProvider('solarMidnightProvider')]
	public function getSolarMidnight(int $year, int $month, int $day, array $location, ?string $expected): void
	{
		$this->assertInstant($expected, $this->cal($year, $month, $day, $location)->getSolarMidnight());
	}

	/*
	|--------------------------------------------------------------------------
	| SEA LEVEL / TWILIGHTS (NJ 2017-10-17)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('njSurfaceProvider')]
	public function njSurface(string $method, string $expected): void
	{
		$this->assertInstant($expected, $this->cal(2017, 10, 17, self::NJ)->$method());
	}

	/*
	|--------------------------------------------------------------------------
	| TIME AT AZIMUTH
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('timeAtAzimuthProvider')]
	public function getTimeAtAzimuth90Or270(float $azimuth, string $expected): void
	{
		$this->assertInstant($expected, $this->cal(2017, 10, 17, self::NJ)->getTimeAtAzimuth90Or270($azimuth));
	}

	/*
	|--------------------------------------------------------------------------
	| LOCAL MEAN TIME
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function getLocalMeanTime(): void
	{
		$this->assertInstant('2017-10-17T16:56:57.605832Z', $this->cal(2017, 10, 17, self::NJ)->getLocalMeanTime(12));
	}

	/*
	|--------------------------------------------------------------------------
	| SOLAR DIP FROM OFFSET
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function getSunriseSolarDipFromOffset(): void
	{
		$actual = $this->cal(2017, 10, 17, self::NJ)->getSunriseSolarDipFromOffset(72);
		$this->assertEqualsWithDelta(14.50320, $actual, 0.001);
	}

	#[Test]
	public function getSunsetSolarDipFromOffset(): void
	{
		$actual = $this->cal(2017, 10, 17, self::NJ)->getSunsetSolarDipFromOffset(72);
		$this->assertEqualsWithDelta(14.52060, $actual, 0.001);
	}

	/*
	|--------------------------------------------------------------------------
	| CALCULATOR SWAP
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function sunTimesCalculatorProducesDifferentSunset(): void
	{
		$noaa = $this->cal(2017, 10, 17, self::NJ)->getSunset();
		$sunTimes = $this->cal(2017, 10, 17, self::NJ, new SunTimesCalculator())->getSunset();

		$this->assertInstant('2017-10-17T22:15:24.498724123Z', $sunTimes);
		$this->assertNotEquals($noaa->getPreciseTimestamp(), $sunTimes->getPreciseTimestamp());
	}

	#[Test]
	public function sunTimesCalculatorReturnsNullAtArcticNoonAndMidnight(): void
	{
		$calendar = $this->cal(2017, 6, 21, self::CONGER, new SunTimesCalculator());

		$this->assertNull($calendar->getSunrise());
		$this->assertNull($calendar->getSunTransit());
		$this->assertNull($calendar->getSolarMidnight());
	}

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTION / STATE
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function defaultsToNoaaCalculator(): void
	{
		$this->assertInstanceOf(NoaaCalculator::class, (new AstronomicalCalendar())->getAstronomicalCalculator());
	}

	#[Test]
	public function setDateRejectsPartialDate(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		(new AstronomicalCalendar())->setDate(2017, 10);
	}

	#[Test]
	public function copyReturnsIndependentClone(): void
	{
		$calendar = $this->cal(2017, 10, 17, self::NJ);
		$copy = $calendar->copy();

		$this->assertNotSame($calendar, $copy);
		$this->assertTrue($calendar->equals($copy));

		$copy->setDate(2020, 1, 1);
		$this->assertEquals('2017-10-17', $calendar->getDate()->format('Y-m-d'));
		$this->assertEquals('2020-01-01', $copy->getDate()->format('Y-m-d'));
		$this->assertFalse($calendar->equals($copy));
	}

	#[Test]
	public function equalsComparesDateLocationAndCalculator(): void
	{
		$a = $this->cal(2017, 10, 17, self::NJ);
		$b = $this->cal(2017, 10, 17, self::NJ);
		$this->assertTrue($a->equals($b));

		$c = $this->cal(2017, 10, 17, self::JLM);
		$this->assertFalse($a->equals($c));
	}
}
