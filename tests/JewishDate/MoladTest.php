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
use PhpZmanim\JewishDate;

/**
 * Coverage for the molad / Kiddush Levana / tekufa surface of PhpZmanim\JewishDate
 * (Java's JewishCalendar + JewishDate). Every expected value is ground truth generated
 * from the current KosherJava: JewishCalendar.getMolad()/getMoladAsInstant()/
 * getTchilasZmanKidushLevana{3,7}Days()/getSofZmanKidushLevana{BetweenMoldos,15Days}()/
 * getTekufaAsInstant(boolean)/getTekufasTishreiElapsedDays()/isBirkasHachamah().
 *
 * Java renders these as java.time.Instant; the PHP port returns Carbon, so each is compared
 * as its UTC ISO-8601 timestamp with millisecond resolution (matching the repo's other Instant
 * assertions and avoiding PHP's negative epoch-timestamp quirks for pre-1970 dates).
 */
class MoladTest extends TestCase
{
	/*
	|--------------------------------------------------------------------------
	| HELPERS
	|--------------------------------------------------------------------------
	*/

	private function assertInstant(string $expectedIso, ?Carbon $actual): void
	{
		$this->assertNotNull($actual);
		$this->assertSame($expectedIso, $actual->copy()->utc()->format('Y-m-d\TH:i:s.v\Z'));
	}

	/*
	|--------------------------------------------------------------------------
	| MOLAD (getMolad + the four molad-based Kiddush Levana times)
	|--------------------------------------------------------------------------
	| The molad depends only on the Jewish year + month, so the fixture day is 1.
	*/

	#[Test]
	#[DataProvider('moladProvider')]
	public function molad(
		int $jy, int $jm,
		int $gy, int $gmo, int $gd, int $h, int $min, int $c,
		string $molad, string $t3, string $t7, string $sofBet, string $sof15
	): void
	{
		$date = JewishDate::create($jy, $jm, 1);

		$moladDate = $date->getMolad();
		$this->assertSame($gy, $moladDate->getGregorianYear());
		$this->assertSame($gmo, $moladDate->getGregorianMonth());
		$this->assertSame($gd, $moladDate->getGregorianDayOfMonth());
		$this->assertSame($h, $moladDate->getMoladHours());
		$this->assertSame($min, $moladDate->getMoladMinutes());
		$this->assertSame($c, $moladDate->getMoladChalakim());

		$this->assertInstant($molad, $date->getMoladAsCarbon());
		$this->assertInstant($t3, $date->getTchilasZmanKidushLevana3Days());
		$this->assertInstant($t7, $date->getTchilasZmanKidushLevana7Days());
		$this->assertInstant($sofBet, $date->getSofZmanKidushLevanaBetweenMoldos());
		$this->assertInstant($sof15, $date->getSofZmanKidushLevana15Days());
	}

	public static function moladProvider(): array
	{
		return [
			//                         jy    jm  gy    gmo gd  h   min c   molad (getMoladAsCarbon)     t3 (3 days)                  t7 (7 days)                  sofBet (between moldos)      sof15 (15 days)
			'Nissan 5784'         => [5784,  1,  2024, 4,  8,  22, 57, 7,  '2024-04-08T20:36:26.837Z',  '2024-04-11T20:36:26.837Z',  '2024-04-15T20:36:26.837Z',  '2024-04-23T14:58:28.503Z',  '2024-04-23T20:36:26.837Z'],
			'Tishrei 5784'        => [5784,  7,  2023, 9,  15, 5,  49, 0,  '2023-09-15T03:28:03.504Z',  '2023-09-18T03:28:03.504Z',  '2023-09-22T03:28:03.504Z',  '2023-09-29T21:50:05.170Z',  '2023-09-30T03:28:03.504Z'],
			'Adar II 5784 (leap)' => [5784, 13,  2024, 3,  10, 10, 13, 6,  '2024-03-10T07:52:23.504Z',  '2024-03-13T07:52:23.504Z',  '2024-03-17T07:52:23.504Z',  '2024-03-25T02:14:25.170Z',  '2024-03-25T07:52:23.504Z'],
			'Tishrei 5785'        => [5785,  7,  2024, 10, 3,  3,  21, 13, '2024-10-03T01:00:46.837Z',  '2024-10-06T01:00:46.837Z',  '2024-10-10T01:00:46.837Z',  '2024-10-17T19:22:48.503Z',  '2024-10-18T01:00:46.837Z'],
			'Adar 5785 (nonleap)' => [5785, 12,  2025, 2,  27, 19, 2,  0,  '2025-02-27T16:41:03.504Z',  '2025-03-02T16:41:03.504Z',  '2025-03-06T16:41:03.504Z',  '2025-03-14T11:03:05.170Z',  '2025-03-14T16:41:03.504Z'],
			'Av 5786'             => [5786,  5,  2026, 7,  14, 19, 30, 17, '2026-07-14T17:10:00.170Z',  '2026-07-17T17:10:00.170Z',  '2026-07-21T17:10:00.170Z',  '2026-07-29T11:32:01.836Z',  '2026-07-29T17:10:00.170Z'],
			'Nissan 5700 (<1970)' => [5700,  1,  1940, 4,  7,  16, 3,  12, '1940-04-07T13:42:43.504Z',  '1940-04-10T13:42:43.504Z',  '1940-04-14T13:42:43.504Z',  '1940-04-22T08:04:45.170Z',  '1940-04-22T13:42:43.504Z'],
			'Nissan 5800'         => [5800,  1,  2040, 3,  13, 11, 36, 6,  '2040-03-13T09:15:23.504Z',  '2040-03-16T09:15:23.504Z',  '2040-03-20T09:15:23.504Z',  '2040-03-28T03:37:25.170Z',  '2040-03-28T09:15:23.504Z'],
			'Tishrei 5000 (<1970)' => [5000, 7,  1239, 9,  7,  1,  42, 5,  '1239-09-06T23:21:20.170Z',  '1239-09-09T23:21:20.170Z',  '1239-09-13T23:21:20.170Z',  '1239-09-21T17:43:21.836Z',  '1239-09-21T23:21:20.170Z'],
			'Tishrei 6000'        => [6000,  7,  2239, 9,  28, 23, 45, 8,  '2239-09-28T21:24:30.170Z',  '2239-10-01T21:24:30.170Z',  '2239-10-05T21:24:30.170Z',  '2239-10-13T15:46:31.836Z',  '2239-10-13T21:24:30.170Z'],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| TEKUFA — days that HAVE a season change (getTekufaAsCarbon returns a value)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('tekufaValueProvider')]
	public function tekufaValue(int $gy, int $gmo, int $gd, string $falseIso, string $trueIso, int $tishreiElapsed): void
	{
		$date = JewishDate::createFromDate($gy, $gmo, $gd);

		$this->assertInstant($falseIso, $date->getTekufaAsCarbon(false));
		$this->assertInstant($trueIso, $date->getTekufaAsCarbon(true));
		$this->assertSame($tishreiElapsed, $date->getTekufasTishreiElapsedDays());
	}

	public static function tekufaValueProvider(): array
	{
		return [
			//                          gy    gmo gd  getTekufaAsCarbon(false)     getTekufaAsCarbon(true)      tishreiElapsed
			'Tekufas Teves 5784'   => [2024, 1,  7,  '2024-01-07T02:30:00.000Z',  '2024-01-07T02:09:03.504Z',  79],
			'Tekufas Nissan 5784'  => [2024, 4,  7,  '2024-04-07T10:00:00.000Z',  '2024-04-07T09:39:03.504Z',  170],
			'Tekufas Tishrei 5785' => [2024, 10, 7,  '2024-10-07T01:00:00.000Z',  '2024-10-07T00:39:03.504Z',  -12],
			'Tekufas Teves 5785'   => [2025, 1,  6,  '2025-01-06T08:30:00.000Z',  '2025-01-06T08:09:03.504Z',  79],
			'Tekufas Tammuz 5785'  => [2025, 7,  8,  '2025-07-07T23:30:00.000Z',  '2025-07-07T23:09:03.504Z',  262],
			'Tekufas Nissan 5786'  => [2026, 4,  8,  '2026-04-07T22:00:00.000Z',  '2026-04-07T21:39:03.504Z',  171],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| TEKUFA — ordinary days with no season change (getTekufaAsCarbon is null)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('tekufaNullProvider')]
	public function tekufaNull(int $gy, int $gmo, int $gd, int $tishreiElapsed): void
	{
		$date = JewishDate::createFromDate($gy, $gmo, $gd);

		$this->assertNull($date->getTekufaAsCarbon(false));
		$this->assertNull($date->getTekufaAsCarbon(true));
		$this->assertSame($tishreiElapsed, $date->getTekufasTishreiElapsedDays());
	}

	public static function tekufaNullProvider(): array
	{
		return [
			'2025-01-01' => [2025, 1,  1,  74],
			'2025-06-15' => [2025, 6,  15, 239],
			'2024-08-20' => [2024, 8,  20, 305],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| TEKUFA — accepted divergence from Java
	|--------------------------------------------------------------------------
	| On these season-change days the Hebrew hour is < 6, so the LMT offset makes Java's
	| LocalTime.of() hour negative and current KosherJava THROWS a DateTimeException. The
	| PHP port deliberately anchors to gregorian midnight + addHours()/addMinutes() offset,
	| which rolls back into the previous day instead of crashing, so it returns a Carbon.
	| There is therefore no Java ground-truth value to pin these to; we only assert the port
	| does not crash and yields a time.
	*/

	#[Test]
	#[DataProvider('tekufaJavaThrowsProvider')]
	public function tekufaWhereJavaThrowsStillReturnsCarbon(int $gy, int $gmo, int $gd): void
	{
		$date = JewishDate::createFromDate($gy, $gmo, $gd);

		$this->assertInstanceOf(Carbon::class, $date->getTekufaAsCarbon(false));
		$this->assertInstanceOf(Carbon::class, $date->getTekufaAsCarbon(true));
	}

	public static function tekufaJavaThrowsProvider(): array
	{
		return [
			'Tekufas Tammuz 5784' => [2024, 7, 8],
			'Tekufas Nissan 5785' => [2025, 4, 8],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| BIRKAS HACHAMAH (once every 28 years)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('birkasHachamahProvider')]
	public function birkasHachamah(int $gy, int $gmo, int $gd, bool $expected): void
	{
		$date = JewishDate::createFromDate($gy, $gmo, $gd);

		$this->assertSame($expected, $date->isBirkasHachamah());
	}

	public static function birkasHachamahProvider(): array
	{
		return [
			'5769 - the day'      => [2009, 4, 8,  true],
			'5797 - the day'      => [2037, 4, 8,  true],
			'5769 - day before'   => [2009, 4, 7,  false],
			'5769 - day after'    => [2009, 4, 9,  false],
			'ordinary 2025-01-01' => [2025, 1, 1,  false],
			'ordinary 2000-01-01' => [2000, 1, 1,  false],
		];
	}
}
