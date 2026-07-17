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
use PhpZmanim\Zman;

/**
 * Coverage for the parameterized / overloaded base zman methods on PhpZmanim\Zman that take explicit
 * day-boundary Carbons, degrees, or hours - the generic entry points that the zero-argument named
 * variants (getSofZmanShmaGRA, etc.) delegate to. The zero-arg regression net in
 * ComprehensiveZmanimCalendarTest does not exercise these directly.
 *
 * Each method is driven with the calendar's own sunrise/sunset (and alos72/tzais72) as boundaries so
 * the inputs match on both sides, and pinned to KosherJava's ZmanimCalendar output for the same
 * fixture (Lakewood, NJ 2017-10-17, NOAA). Instants are compared as UTC to the millisecond.
 */
class ParameterizedZmanimTest extends TestCase
{
	private const LAKEWOOD = ['lat' => 40.0721087, 'lon' => -74.2400243, 'elev' => 15, 'tz' => 'America/New_York'];

	private function fixtureCalendar(): Zman
	{
		return Zman::create(2017, 10, 17, self::LAKEWOOD['lat'], self::LAKEWOOD['lon'], self::LAKEWOOD['elev'], self::LAKEWOOD['tz']);
	}

	private function assertInstant(string $expectedIso, ?Carbon $actual): void
	{
		$this->assertNotNull($actual);
		$this->assertEqualsWithDelta(
			Carbon::parse($expectedIso, 'UTC')->getPreciseTimestamp() / 1e6,
			$actual->getPreciseTimestamp() / 1e6,
			0.002
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Boundaries = sunrise / sunset
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function zmanimWithSunriseSunsetBoundaries(): void
	{
		$cal = $this->fixtureCalendar();
		$sunrise = $cal->getSunrise();
		$sunset = $cal->getSunset();
		$chatzos = $cal->getChatzos($sunrise, $sunset);

		$this->assertInstant('2017-10-17T13:55:33.427Z', $cal->getSofZmanShma($sunrise, $sunset, false));
		$this->assertInstant('2017-10-17T14:51:00.712Z', $cal->getSofZmanTfila($sunrise, $sunset, false));
		$this->assertInstant('2017-10-17T17:09:38.925Z', $cal->getMinchaGedola($sunrise, $sunset, false));
		$this->assertInstant('2017-10-17T17:09:38.925Z', $cal->getMinchaGedola($sunrise, $sunset, true));
		$this->assertInstant('2017-10-17T19:56:00.781Z', $cal->getMinchaKetana($sunrise, $sunset, false));
		$this->assertInstant('2017-10-17T21:05:19.888Z', $cal->getPlagHamincha($sunrise, $sunset, false));
		$this->assertInstant('2017-10-17T19:28:17.139Z', $cal->getSamuchLeMinchaKetana($sunrise, $sunset, false));
		$this->assertInstant('2017-10-17T16:41:55.283Z', $chatzos);
		$this->assertInstant('2017-10-17T15:46:27.998Z', $cal->getShaahZmanisBasedZman($sunrise, $sunset, 5.0));
		// 3 half-day hours from sunrise to chatzos == sof zman shma
		$this->assertInstant('2017-10-17T13:55:33.427Z', $cal->getHalfDayBasedZman($sunrise, $chatzos, 3.0));
	}

	/*
	|--------------------------------------------------------------------------
	| Boundaries = alos72 / tzais72 (MGA-style day)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function zmanimWithAlosTzaisBoundaries(): void
	{
		$cal = $this->fixtureCalendar();
		$alos = $cal->getAlos72Minutes();
		$tzais = $cal->getTzais72Minutes();

		$this->assertInstant('2017-10-17T13:19:53.352Z', $cal->getSofZmanShma($alos, $tzais, false));
		$this->assertInstant('2017-10-17T22:01:48.389Z', $cal->getPlagHamincha($alos, $tzais, false));
	}

	/*
	|--------------------------------------------------------------------------
	| getSunriseOffsetByDegrees() / getSunsetOffsetByDegrees()
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function degreeOffsets(): void
	{
		$cal = $this->fixtureCalendar();

		$this->assertInstant('2017-10-17T10:42:27.439Z', $cal->getSunriseOffsetByDegrees(96.0));
		$this->assertInstant('2017-10-17T22:41:21.435Z', $cal->getSunsetOffsetByDegrees(96.0));
		$this->assertInstant('2017-10-17T09:49:30.219Z', $cal->getSunriseOffsetByDegrees(106.1));
		$this->assertInstant('2017-10-17T23:44:09.707Z', $cal->getSunsetOffsetByDegrees(108.0));
	}

	/*
	|--------------------------------------------------------------------------
	| Float-returning helpers: getPercentOfShaahZmanisFromDegrees / getHalfDayBasedShaahZmanis
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function shaahZmanisFloats(): void
	{
		$cal = $this->fixtureCalendar();
		$sunrise = $cal->getSunrise();
		$sunset = $cal->getSunset();
		$chatzos = $cal->getChatzos($sunrise, $sunset);

		$this->assertEqualsWithDelta(1.451879674756432, $cal->getPercentOfShaahZmanisFromDegrees(16.1, false), 1e-6);
		$this->assertEqualsWithDelta(1.4499869729313524, $cal->getPercentOfShaahZmanisFromDegrees(16.1, true), 1e-6);

		// KosherJava Duration PT55M27.285256552S, expressed by the PHP port in milliseconds.
		$this->assertEqualsWithDelta(3327285.256552, $cal->getHalfDayBasedShaahZmanis($sunrise, $chatzos), 1.0);
	}
}
