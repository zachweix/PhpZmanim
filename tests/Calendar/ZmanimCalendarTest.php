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
use PhpZmanim\Calendar\ZmanimCalendar;
use PhpZmanim\GeoLocation;

/**
 * Behavioral coverage for ZmanimCalendar, mirroring KosherJava's ZmanimCalendarTest. Every zero-argument
 * zman getter is pinned against KosherJava's own output for the fixture (Lakewood, NJ on 2017-10-17), and
 * the elevation toggle is verified to actually move the sunrise-based zmanim.
 */
class ZmanimCalendarTest extends TestCase
{
	/*
	|--------------------------------------------------------------------------
	| LOCATIONS
	|--------------------------------------------------------------------------
	| Lakewood, NJ - KosherJava's canonical reference location (TestLocations.lakewood()).
	*/

	private const NJ = ['lat' => 40.0721087, 'lon' => -74.2400243, 'elev' => 15, 'tz' => 'America/New_York'];

	/*
	|--------------------------------------------------------------------------
	| HELPERS
	|--------------------------------------------------------------------------
	*/

	private function fixtureCalendar(): ZmanimCalendar
	{
		return $this->calendarFor(2017, 10, 17);
	}

	private function calendarFor(int $year, int $month, int $day): ZmanimCalendar
	{
		$geo = GeoLocation::create(self::NJ['lat'], self::NJ['lon'], self::NJ['elev'], self::NJ['tz']);

		return new ZmanimCalendar($year, $month, $day, $geo);
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
	| Expected values are KosherJava ground truth (default NOAA calculator, sea level)
	| for Lakewood, NJ on 2017-10-17, taken from KosherJava's ZmanimCalendarTest.
	*/

	public static function zmanimProvider(): array
	{
		return [
			'alos 16.1 degrees' => ['getAlosHashachar', '2017-10-17T09:49:30.219906135Z'],
			'alos 72' => ['getAlos72', '2017-10-17T09:57:51.403184642Z'],
			'sof zman shma GRA' => ['getSofZmanShmaGRA', '2017-10-17T13:55:53.352746510Z'],
			'sof zman shma MGA 72' => ['getSofZmanShmaMGA', '2017-10-17T13:19:53.352746510Z'],
			'sof zman tfila GRA' => ['getSofZmanTfilaGRA', '2017-10-17T14:51:14.002600466Z'],
			'sof zman tfila MGA 72' => ['getSofZmanTfilaMGA', '2017-10-17T14:27:14.002600466Z'],
			'chatzos hayom' => ['getChatzos', '2017-10-17T16:42:12.781249470Z'],
			'chatzos as half day' => ['getChatzosAsHalfDay', '2017-10-17T16:41:55.302308378Z'],
			'chatzos halayla' => ['getChatzosHalayla', '2017-10-18T04:42:06.833038724Z'],
			'mincha gedola GRA' => ['getMinchaGedolaGRA', '2017-10-17T17:09:35.627235356Z'],
			'mincha ketana GRA' => ['getMinchaKetanaGRA', '2017-10-17T19:55:37.576797224Z'],
			'plag hamincha GRA' => ['getPlagHaminchaGRA', '2017-10-17T21:04:48.389114669Z'],
			'candle lighting' => ['getCandleLighting', '2017-10-17T21:55:59.201432122Z'],
			'tzais 72' => ['getTzais72', '2017-10-17T23:25:59.201432122Z'],
			'tzais geonim 8.5 degrees' => ['getTzais', '2017-10-17T22:54:29.772724455Z'],
		];
	}

	public static function shaahZmanisProvider(): array
	{
		// KosherJava returns a Duration; the PHP port returns the length in milliseconds.
		// GRA: PT55M20.649853956S, MGA 72: PT1H7M20.649853956S.
		return [
			'shaah zmanis GRA' => ['getShaahZmanisGra', 3320649.853956],
			'shaah zmanis MGA 72' => ['getShaahZmanisMGA', 4040649.853956],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| ZMANIM (Lakewood, NJ 2017-10-17)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('zmanimProvider')]
	public function zmanim(string $method, ?string $expected): void
	{
		$this->assertInstant($expected, $this->fixtureCalendar()->$method());
	}

	#[Test]
	#[DataProvider('shaahZmanisProvider')]
	public function shaahZmanis(string $method, float $expectedMillis): void
	{
		$this->assertEqualsWithDelta($expectedMillis, $this->fixtureCalendar()->$method(), 1);
	}

	#[Test]
	public function chametzZmanimAreNullWhenNotErevPesach(): void
	{
		$calendar = $this->fixtureCalendar();
		$sunrise = $calendar->getSeaLevelSunrise();
		$sunset = $calendar->getSeaLevelSunset();

		$this->assertNull($calendar->getSofZmanBiurChametz($sunrise, $sunset, true));
		$this->assertNull($calendar->getSofZmanAchilasChametz($sunrise, $sunset, true));
	}

	/*
	|--------------------------------------------------------------------------
	| ELEVATION TOGGLE
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function elevationToggleAffectsZmanim(): void
	{
		$calendar = $this->fixtureCalendar();
		$default = $calendar->getUseElevation();
		$atDefault = $calendar->getSofZmanShmaGRA();

		$calendar->setUseElevation(!$default);
		$toggled = $calendar->getSofZmanShmaGRA();

		$this->assertNotEquals($atDefault->getPreciseTimestamp(), $toggled->getPreciseTimestamp());
	}

	/*
	|--------------------------------------------------------------------------
	| ASSUR BEMELACHA
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function isAssurBemlacha(): void
	{
		// Erev Shabbos (Friday)
		$erevShabbos = $this->calendarFor(2024, 11, 8);
		$sunset = $erevShabbos->getSunset();
		$tzais = $erevShabbos->getTzais();

		// Before sunset on Friday - not yet assur
		$this->assertFalse($erevShabbos->isAssurBemlacha($sunset->copy()->subMinutes(30), $tzais, false));
		// After sunset on Friday - assur (Shabbos begins)
		$afterSunset = $sunset->copy()->addMinutes(10);
		$this->assertTrue($erevShabbos->isAssurBemlacha($afterSunset, $tzais, false));
		// After tzais on Friday - still assur (during Shabbos)
		$this->assertTrue($erevShabbos->isAssurBemlacha($tzais->copy()->addMinutes(10), $tzais, false));

		// Shabbos day
		$shabbos = $this->calendarFor(2024, 11, 9);
		$tzaisShabbos = $shabbos->getTzais();
		// During Shabbos day (before tzais) - assur
		$this->assertTrue($shabbos->isAssurBemlacha($tzaisShabbos->copy()->subHours(6), $tzaisShabbos, false));
		// After tzais on Shabbos - not assur (Shabbos ends)
		$this->assertFalse($shabbos->isAssurBemlacha($tzaisShabbos->copy()->addMinutes(10), $tzaisShabbos, false));

		// Regular weekday (Thursday) - never assur
		$weekday = $this->calendarFor(2024, 11, 7);
		$tzaisWeekday = $weekday->getTzais();
		$this->assertFalse($weekday->isAssurBemlacha($tzaisWeekday->copy()->subMinutes(30), $tzaisWeekday, false));
		$this->assertFalse($weekday->isAssurBemlacha($tzaisWeekday->copy()->addMinutes(10), $tzaisWeekday, false));

		// Shabbos behaves the same in Israel and the Diaspora
		$this->assertTrue($erevShabbos->isAssurBemlacha($afterSunset, $tzais, true));
		$this->assertTrue($erevShabbos->isAssurBemlacha($afterSunset, $tzais, false));
	}
}
