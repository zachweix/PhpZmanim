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
use PhpZmanim\Zman;

/**
 * Coverage for the location- and date-gated zmanim that only produce a value under specific
 * conditions: the chametz times (only on Erev Pesach, 14 Nissan), the Kidush Levana / molad times
 * (only within their lunar-month windows), and the polar zmanim (only inside the Arctic circle during
 * polar day, when there is no ordinary sunrise or sunset). ComprehensiveZmanimCalendarTest pins these
 * as null for its temperate ordinary-day fixture; this test verifies the times they return under the
 * conditions they actually apply. Expected values are KosherJava's output (Lakewood / Northern Norway).
 */
class SpecialDayZmanimTest extends TestCase
{
	private const LAKEWOOD = ['lat' => 40.0721087, 'lon' => -74.2400243, 'elev' => 15, 'tz' => 'America/New_York'];
	private const NORWAY = ['lat' => 70.1498248, 'lon' => 9.1456867, 'elev' => 0, 'tz' => 'Europe/Oslo'];

	private function lakewoodFor(int $year, int $month, int $day): Zman
	{
		return Zman::create($year, $month, $day, self::LAKEWOOD['lat'], self::LAKEWOOD['lon'], self::LAKEWOOD['elev'], self::LAKEWOOD['tz']);
	}

	private function norwayFor(int $year, int $month, int $day): Zman
	{
		return Zman::create($year, $month, $day, self::NORWAY['lat'], self::NORWAY['lon'], self::NORWAY['elev'], self::NORWAY['tz']);
	}

	private function assertInstant(string $expectedIso, ?Carbon $actual): void
	{
		$this->assertNotNull($actual);
		$this->assertEqualsWithDelta(
			Carbon::parse($expectedIso, 'UTC')->getPreciseTimestamp() / 1e6,
			$actual->getPreciseTimestamp() / 1e6,
			0.001
		);
	}

	/*
	|--------------------------------------------------------------------------
	| CHAMETZ (only on Erev Pesach, 14 Nissan — 2017-04-10)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function chametzTimesOnErevPesach(): void
	{
		$calendar = $this->lakewoodFor(2017, 4, 10);

		// All ten sof zman achilas/biur chametz opinions must produce a time on Erev Pesach.
		$this->assertNotNull($calendar->getSofZmanAchilasChametzGRA());
		$this->assertNotNull($calendar->getSofZmanAchilasChametzMGA72Minutes());
		$this->assertNotNull($calendar->getSofZmanAchilasChametzMGA72MinutesZmanis());
		$this->assertNotNull($calendar->getSofZmanAchilasChametzMGA16Point1Degrees());
		$this->assertNotNull($calendar->getSofZmanAchilasChametzBaalHatanya());
		$this->assertNotNull($calendar->getSofZmanBiurChametzGRA());
		$this->assertNotNull($calendar->getSofZmanBiurChametzMGA72Minutes());
		$this->assertNotNull($calendar->getSofZmanBiurChametzMGA72MinutesZmanis());
		$this->assertNotNull($calendar->getSofZmanBiurChametzMGA16Point1Degrees());
		$this->assertNotNull($calendar->getSofZmanBiurChametzBaalHatanya());

		// Regression on the GRA opinions; biur (end of the 5th hour) is one shaah zmanis after achilas (end of the 4th).
		$this->assertInstant('2017-04-10T14:47:45.675920561Z', $calendar->getSofZmanAchilasChametzGRA());
		$this->assertInstant('2017-04-10T15:53:07.762314340Z', $calendar->getSofZmanBiurChametzGRA());
	}

	#[Test]
	public function chametzTimesAreNullOnAnOrdinaryDay(): void
	{
		$calendar = $this->lakewoodFor(2017, 10, 17);
		$this->assertNull($calendar->getSofZmanAchilasChametzGRA());
		$this->assertNull($calendar->getSofZmanBiurChametzGRA());
	}

	/*
	|--------------------------------------------------------------------------
	| MOLAD / KIDUSH LEVANA (only within the lunar-month window)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function zmanMoladAndTchilasKidushLevana(): void
	{
		// The molad of Cheshvan 5778 (the moment itself falls on 2017-10-20).
		$this->assertInstant('2017-10-20T09:52:00.170666666Z', $this->lakewoodFor(2017, 10, 20)->getZmanMolad());

		// Earliest Kidush Levana: 3 days and 7 days after the molad.
		$this->assertInstant('2017-10-23T09:52:00.170666666Z', $this->lakewoodFor(2017, 10, 23)->getTchilasZmanKidushLevana3Days());
		$this->assertInstant('2017-10-27T09:52:00.170666666Z', $this->lakewoodFor(2017, 10, 27)->getTchilasZmanKidushLevana7Days());
	}

	#[Test]
	public function sofZmanKidushLevana(): void
	{
		// Latest Kidush Levana for the month whose molad was in late September 2017 (sayable through 2017-10-05).
		$this->assertInstant('2017-10-05T21:07:56.837333333Z', $this->lakewoodFor(2017, 10, 5)->getSofZmanKidushLevana15Days());
		$this->assertInstant('2017-10-05T15:29:58.503333333Z', $this->lakewoodFor(2017, 10, 5)->getSofZmanKidushLevanaBetweenMoldos());
	}

	#[Test]
	public function kidushLevanaIsNullOutsideItsWindow(): void
	{
		// 2017-10-17 is past the previous month's sof zman and before the next month's tchilas zman.
		$calendar = $this->lakewoodFor(2017, 10, 17);
		$this->assertNull($calendar->getZmanMolad());
		$this->assertNull($calendar->getTchilasZmanKidushLevana3Days());
		$this->assertNull($calendar->getSofZmanKidushLevana15Days());
	}

	/*
	|--------------------------------------------------------------------------
	| POLAR ZMANIM (only inside the Arctic circle during polar day)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function polarZmanimDuringPolarDay(): void
	{
		// 2017-06-21 (summer solstice) is inside the polar day at the Norway fixture: no ordinary sunrise or sunset.
		$calendar = $this->norwayFor(2017, 6, 21);

		$this->assertNull($calendar->getSunrise());
		$this->assertNull($calendar->getSunset());

		// Ben Ish Chai: use the sun's crossing of due east (90) / due west (270) as the "sunrise"/"sunset".
		$this->assertInstant('2017-06-21T06:01:13.021342105Z', $calendar->getPolarSunriseBenIshChai());
		$this->assertInstant('2017-06-21T16:49:17.587975180Z', $calendar->getPolarSunsetBenIshChai());
		$this->assertInstant('2017-06-21T15:41:47.112284232Z', $calendar->getPolarPlagHaminchaBenIshChai());

		// Teshuvos Vehanhagos: during polar summer the start of day is chatzos halayla.
		$this->assertInstant('2017-06-21T23:25:21.765329979Z', $calendar->getPolarStartOfDayTeshuvosVehanhagos());
		$this->assertInstant('2017-06-21T20:55:21.765329979Z', $calendar->getPolarPlagHaminchaTeshuvosVehanhagos());
	}

	#[Test]
	public function polarZmanimAreNullWhereSunRisesAndSets(): void
	{
		// At the temperate Lakewood fixture the sun rises and sets normally, so the polar alternatives do not apply.
		$calendar = $this->lakewoodFor(2017, 10, 17);
		$this->assertNull($calendar->getPolarSunriseBenIshChai());
		$this->assertNull($calendar->getPolarSunsetBenIshChai());
		$this->assertNull($calendar->getPolarPlagHaminchaBenIshChai());
		$this->assertNull($calendar->getPolarStartOfDayTeshuvosVehanhagos());
		$this->assertNull($calendar->getPolarPlagHaminchaTeshuvosVehanhagos());
	}
}
