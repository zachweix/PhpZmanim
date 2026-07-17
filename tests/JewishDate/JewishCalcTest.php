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
 * Coverage for the Jewish-calendar math on PhpZmanim\JewishDate: the JewishCalc year/month
 * predicates and the Manipulation add/sub methods. Year/month/compareTo expectations are ground
 * truth from the current KosherJava (isJewishLeapYear/getDaysInJewishYear/getDaysInJewishMonth/
 * isCheshvanLong/isKislevShort/getCheshvanKislevKviah/getDaysSinceStartOfJewishYear/compareTo).
 *
 * The add/sub methods are Carbon-idiomatic PHP additions with no direct Java twin, so each result
 * is pinned to its resulting Jewish date AND to the Gregorian date KosherJava derives for that
 * Jewish date (the shared, authoritative conversion), plus pure add/sub round-trip invariants.
 */
class JewishCalcTest extends TestCase
{
	private const CHASERIM = 0;
	private const KESIDRAN = 1;
	private const SHELAIMIM = 2;

	private function assertJewish(int $jy, int $jm, int $jd, JewishDate $date): void
	{
		$this->assertSame($jy, $date->getJewishYear(), 'jewishYear');
		$this->assertSame($jm, $date->getJewishMonth(), 'jewishMonth');
		$this->assertSame($jd, $date->getJewishDayOfMonth(), 'jewishDay');
	}

	private function assertGregorian(int $gy, int $gmo, int $gd, JewishDate $date): void
	{
		$this->assertSame($gy, $date->getGregorianYear(), 'gregorianYear');
		$this->assertSame($gmo, $date->getGregorianMonth(), 'gregorianMonth');
		$this->assertSame($gd, $date->getGregorianDayOfMonth(), 'gregorianDay');
	}

	/*
	|--------------------------------------------------------------------------
	| YEAR PREDICATES (all six year types: leap/non-leap x chaser/kesidran/shalem)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('yearProvider')]
	public function yearPredicates(int $year, bool $leap, int $daysInYear, bool $cheshvanLong, bool $kislevShort, int $kviah): void
	{
		$date = JewishDate::create($year, JewishDate::TISHREI, 1);

		$this->assertSame($leap, $date->isJewishLeapYear());
		$this->assertSame($daysInYear, $date->getDaysInJewishYear());
		$this->assertSame($cheshvanLong, $date->isCheshvanLong());
		$this->assertSame($kislevShort, $date->isKislevShort());
		$this->assertSame($kviah, $date->getCheshvanKislevKviah());
	}

	public static function yearProvider(): array
	{
		return [
			//                       year  leap   days  cheshvanLong kislevShort kviah
			'5780 nonleap shalem'  => [5780, false, 355, true,  false, self::SHELAIMIM],
			'5781 nonleap chaser'  => [5781, false, 353, false, true,  self::CHASERIM],
			'5782 leap kesidran'   => [5782, true,  384, false, false, self::KESIDRAN],
			'5783 nonleap shalem'  => [5783, false, 355, true,  false, self::SHELAIMIM],
			'5784 leap chaser'     => [5784, true,  383, false, true,  self::CHASERIM],
			'5785 nonleap shalem'  => [5785, false, 355, true,  false, self::SHELAIMIM],
			'5786 nonleap kesidran' => [5786, false, 354, false, false, self::KESIDRAN],
			'5787 leap shalem'     => [5787, true,  385, true,  false, self::SHELAIMIM],
			'5789 nonleap kesidran' => [5789, false, 354, false, false, self::KESIDRAN],
			'5790 leap chaser'     => [5790, true,  383, false, true,  self::CHASERIM],
			'5795 leap shalem'     => [5795, true,  385, true,  false, self::SHELAIMIM],
			'5000 leap chaser'     => [5000, true,  383, false, true,  self::CHASERIM],
			'6000 nonleap chaser'  => [6000, false, 353, false, true,  self::CHASERIM],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| MONTH LENGTHS
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('monthLengthProvider')]
	public function daysInJewishMonth(int $year, int $month, int $expected): void
	{
		$this->assertSame($expected, JewishDate::create($year, $month, 1)->getDaysInJewishMonth());
	}

	public static function monthLengthProvider(): array
	{
		return [
			// 5784 (leap, chaser: Cheshvan 29, Kislev 29)
			'5784 Tishrei'  => [5784, 7,  30], '5784 Cheshvan' => [5784, 8,  29], '5784 Kislev' => [5784, 9,  29],
			'5784 Teves'    => [5784, 10, 29], '5784 Shvat'    => [5784, 11, 30], '5784 Adar I' => [5784, 12, 30],
			'5784 Adar II'  => [5784, 13, 29], '5784 Nissan'   => [5784, 1,  30], '5784 Iyar'   => [5784, 2,  29],
			'5784 Sivan'    => [5784, 3,  30], '5784 Tammuz'   => [5784, 4,  29], '5784 Av'     => [5784, 5,  30],
			'5784 Elul'     => [5784, 6,  29],
			// 5785 (non-leap, shalem: Cheshvan 30, Kislev 30, single Adar 29)
			'5785 Tishrei'  => [5785, 7,  30], '5785 Cheshvan' => [5785, 8,  30], '5785 Kislev' => [5785, 9,  30],
			'5785 Teves'    => [5785, 10, 29], '5785 Shvat'    => [5785, 11, 30], '5785 Adar'   => [5785, 12, 29],
			'5785 Nissan'   => [5785, 1,  30], '5785 Iyar'     => [5785, 2,  29], '5785 Sivan'  => [5785, 3,  30],
			'5785 Tammuz'   => [5785, 4,  29], '5785 Av'       => [5785, 5,  30], '5785 Elul'   => [5785, 6,  29],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| getDaysSinceStartOfJewishYear()
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('daysSinceProvider')]
	public function daysSinceStartOfJewishYear(int $jy, int $jm, int $jd, int $expected): void
	{
		$this->assertSame($expected, JewishDate::create($jy, $jm, $jd)->getDaysSinceStartOfJewishYear());
	}

	public static function daysSinceProvider(): array
	{
		return [
			'1 Tishrei'          => [5784, 7, 1,  1],
			'30 Tishrei'         => [5784, 7, 30, 30],
			'1 Cheshvan'         => [5784, 8, 1,  31],
			'1 Nissan'           => [5784, 1, 1,  207],
			'29 Elul (leap end)' => [5784, 6, 29, 383],
			'29 Elul (nonleap)'  => [5785, 6, 29, 355],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| compareTo()
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('compareProvider')]
	public function compareTo(array $a, array $b, int $expected): void
	{
		$this->assertSame($expected, JewishDate::create(...$a)->compareTo(JewishDate::create(...$b)));
	}

	public static function compareProvider(): array
	{
		return [
			'earlier'          => [[5784, 7, 15], [5784, 7, 16], -1],
			'later'            => [[5784, 7, 16], [5784, 7, 15], 1],
			'equal'            => [[5784, 7, 15], [5784, 7, 15], 0],
			'across new year'  => [[5784, 13, 1], [5785, 1, 1], -1],
			'across old year'  => [[5784, 7, 1], [5783, 7, 1], 1],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| formatJewish()
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function formatJewish(): void
	{
		$this->assertSame('5784-07-15', JewishDate::create(5784, 7, 15)->formatJewish());
		$this->assertSame('5786-01-01', JewishDate::create(5786, 1, 1)->formatJewish());
		$this->assertSame('5784-13-09', JewishDate::create(5784, 13, 9)->formatJewish());
	}

	/*
	|--------------------------------------------------------------------------
	| addDays() / subDays()
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('addDaysProvider')]
	public function addDays(int $jy, int $jm, int $jd, int $n, int $eJy, int $eJm, int $eJd, int $eGy, int $eGmo, int $eGd): void
	{
		$date = JewishDate::create($jy, $jm, $jd)->addDays($n);

		$this->assertJewish($eJy, $eJm, $eJd, $date);
		$this->assertGregorian($eGy, $eGmo, $eGd, $date);
	}

	public static function addDaysProvider(): array
	{
		return [
			//                        start        n    -> endJewish       endGregorian
			'+30 days'          => [5784, 7,  15, 30,  5784, 8,  15, 2023, 10, 30],
			'-30 days'          => [5784, 7,  15, -30, 5783, 6,  14, 2023, 8,  31],
			'+400 days'         => [5784, 1,  1,  400, 5785, 2,  16, 2025, 5,  14],
			'Adar I 30 +1 day'  => [5784, 12, 30, 1,   5784, 13, 1,  2024, 3,  11],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| addMonthsJewish() / subMonthsJewish()  (negative amount delegates to the inverse)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('addMonthsJewishProvider')]
	public function addMonthsJewish(int $jy, int $jm, int $jd, int $n, int $eJy, int $eJm, int $eJd, int $eGy, int $eGmo, int $eGd): void
	{
		$date = JewishDate::create($jy, $jm, $jd)->addMonthsJewish($n);

		$this->assertJewish($eJy, $eJm, $eJd, $date);
		$this->assertGregorian($eGy, $eGmo, $eGd, $date);
	}

	public static function addMonthsJewishProvider(): array
	{
		return [
			'Tishrei +1'                => [5784, 7,  15, 1,  5784, 8,  15, 2023, 10, 30],
			'Elul +1 (year rolls)'      => [5784, 6,  20, 1,  5785, 7,  20, 2024, 10, 22],
			'Adar I +1 -> Adar II'      => [5784, 12, 15, 1,  5784, 13, 15, 2024, 3,  25],
			'Adar(nonleap) +1 -> Nissan' => [5785, 12, 15, 1, 5785, 1,  15, 2025, 4,  13],
			'Adar II +1 -> Nissan'      => [5784, 13, 15, 1,  5784, 1,  15, 2024, 4,  23],
			'+13 (leap-year full loop)' => [5784, 7,  15, 13, 5785, 7,  15, 2024, 10, 17],
			'Tishrei 30 +1 (day clamp)' => [5784, 7,  30, 1,  5784, 8,  29, 2023, 11, 13],
			'negative delegates to sub' => [5785, 7,  15, -1, 5784, 6,  15, 2024, 9,  18],
		];
	}

	#[Test]
	#[DataProvider('subMonthsJewishProvider')]
	public function subMonthsJewish(int $jy, int $jm, int $jd, int $n, int $eJy, int $eJm, int $eJd, int $eGy, int $eGmo, int $eGd): void
	{
		$date = JewishDate::create($jy, $jm, $jd)->subMonthsJewish($n);

		$this->assertJewish($eJy, $eJm, $eJd, $date);
		$this->assertGregorian($eGy, $eGmo, $eGd, $date);
	}

	public static function subMonthsJewishProvider(): array
	{
		return [
			'Tishrei -1 (year rolls back)' => [5785, 7, 15, 1, 5784, 6,  15, 2024, 9, 18],
			'Nissan -1 -> Adar II'         => [5784, 1, 15, 1, 5784, 13, 15, 2024, 3, 25],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| addYearsJewish() / subYearsJewish()  (useAdarAlephForLeapYear flag)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('addYearsJewishProvider')]
	public function addYearsJewish(int $jy, int $jm, int $jd, int $n, bool $adarAleph, int $eJy, int $eJm, int $eJd, int $eGy, int $eGmo, int $eGd): void
	{
		$date = JewishDate::create($jy, $jm, $jd)->addYearsJewish($n, $adarAleph);

		$this->assertJewish($eJy, $eJm, $eJd, $date);
		$this->assertGregorian($eGy, $eGmo, $eGd, $date);
	}

	public static function addYearsJewishProvider(): array
	{
		return [
			'simple +1'                        => [5784, 7,  15, 1, false, 5785, 7,  15, 2024, 10, 17],
			'Adar -> leap yr, default Adar II' => [5785, 12, 15, 2, false, 5787, 13, 15, 2027, 3,  24],
			'Adar -> leap yr, force Adar I'    => [5785, 12, 15, 2, true,  5787, 12, 15, 2027, 2,  22],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| Gregorian manipulation (Carbon-idiomatic; day <= 28 avoids overflow ambiguity)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function addMonthsGregorian(): void
	{
		$date = JewishDate::createFromDate(2024, 1, 15)->addMonthsGregorian(3);
		$this->assertGregorian(2024, 4, 15, $date);
		$this->assertJewish(5784, 1, 7, $date);
	}

	#[Test]
	public function subMonthsGregorian(): void
	{
		$date = JewishDate::createFromDate(2024, 1, 15)->subMonthsGregorian(5);
		$this->assertGregorian(2023, 8, 15, $date);
		$this->assertJewish(5783, 5, 28, $date);
	}

	#[Test]
	public function addYearsGregorian(): void
	{
		$date = JewishDate::createFromDate(2024, 1, 15)->addYearsGregorian(2);
		$this->assertGregorian(2026, 1, 15, $date);
		$this->assertJewish(5786, 10, 26, $date);
	}

	/*
	|--------------------------------------------------------------------------
	| Round-trip invariants (a manipulation and its inverse return to the start)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function roundTripInvariants(): void
	{
		$date = JewishDate::create(5784, 7, 15)->addDays(100)->subDays(100);
		$this->assertJewish(5784, 7, 15, $date);

		$date = JewishDate::create(5784, 8, 15)->addMonthsJewish(5)->subMonthsJewish(5);
		$this->assertJewish(5784, 8, 15, $date);

		$date = JewishDate::create(5784, 1, 10)->addYearsJewish(7)->subYearsJewish(7);
		$this->assertJewish(5784, 1, 10, $date);
	}

	/*
	|--------------------------------------------------------------------------
	| resetDate() sets to today's Gregorian date
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function resetDate(): void
	{
		$now = Carbon::now();
		$expected = JewishDate::createFromDate($now->year, $now->month, $now->day);

		$date = JewishDate::create(5700, 1, 1)->resetDate();

		$this->assertSame(0, $expected->compareTo($date));
	}
}
