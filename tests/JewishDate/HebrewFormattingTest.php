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

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PhpZmanim\JewishDate;
use PhpZmanim\Torah\MasechtaBavli;
use PhpZmanim\Torah\Parshah;
use PhpZmanim\Torah\YomTov;

/**
 * End-to-end spot checks of the Hebrew output, written as literal Hebrew rather than
 * escape sequences. Every expected string is ground truth from the current KosherJava
 * (HebrewDateFormatter.format/formatMonth/formatDayOfWeek/formatYomTov/formatParsha/
 * formatSpecialParsha/formatOmer/formatDafYomiBavli/getFormattedKviah).
 *
 * These are deliberately real-world sanity checks: if the Hebrew letter tables were ever
 * corrupted, these fail with a readable diff rather than a wall of byte escapes.
 */
class HebrewFormattingTest extends TestCase
{
	/*
	|--------------------------------------------------------------------------
	| A KNOWN DAY, SPELLED OUT
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function nineteenJuly2026IsTheFifthOfAv5786(): void
	{
		$date = JewishDate::createFromDate(2026, 7, 19);

		$this->assertSame(5786, $date->getJewishYear());
		$this->assertSame(5, $date->getJewishMonth());     // Av
		$this->assertSame(5, $date->getJewishDayOfMonth());

		$this->assertSame('5 Av, 5786', $date->format()->english()->date());
		$this->assertSame('ה׳ אב תשפ״ו', $date->format()->hebrew()->date());

		$this->assertSame('אב', $date->format()->hebrew()->month());
		$this->assertSame('ראשון', $date->format()->hebrew()->dayOfWeek());
		$this->assertSame('Sunday', $date->format()->english()->dayOfWeek());

		// a Sunday, so no parsha of its own; the coming Shabbos is Vaeschanan
		$this->assertSame('', $date->format()->hebrew()->parshah());
		$this->assertSame('ואתחנן', $date->getUpcomingParshah()->hebrew());

		$this->assertSame('חולין פ׳', $date->format()->hebrew()->dafYomiBavli());
		$this->assertSame('גכה', $date->format()->hebrew()->kviah());
	}

	/*
	|--------------------------------------------------------------------------
	| A SPREAD OF REAL DATES
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('dateProvider')]
	public function hebrewDateRendering(
		int $gy, int $gmo, int $gd,
		int $jy, int $jm, int $jd,
		string $hebrew, string $english, string $month, string $dayOfWeek, string $daf
	): void
	{
		$date = JewishDate::createFromDate($gy, $gmo, $gd);

		$this->assertSame($jy, $date->getJewishYear());
		$this->assertSame($jm, $date->getJewishMonth());
		$this->assertSame($jd, $date->getJewishDayOfMonth());

		$this->assertSame($hebrew, $date->format()->hebrew()->date());
		$this->assertSame($english, $date->format()->english()->date());
		$this->assertSame($month, $date->format()->hebrew()->month());
		$this->assertSame($dayOfWeek, $date->format()->hebrew()->dayOfWeek());
		$this->assertSame($daf, $date->format()->hebrew()->dafYomiBavli());
	}

	public static function dateProvider(): array
	{
		return [
			//                              gy    gmo gd    jy    jm  jd  hebrew                 english             month     dayOfWeek  daf
			'5 Av 5786 (today)'        => [2026, 7,  19,  5786, 5,  5,  'ה׳ אב תשפ״ו',      '5 Av, 5786',      'אב',    'ראשון',  'חולין פ׳'],
			'11 Av 5786 (Shabbos)'     => [2026, 7,  25,  5786, 5,  11, 'י״א אב תשפ״ו',     '11 Av, 5786',     'אב',    'שבת',    'חולין פ״ו'],
			'Rosh Hashana 5787'        => [2026, 9,  12,  5787, 7,  1,  'א׳ תשרי תשפ״ז',    '1 Tishrei, 5787', 'תשרי',  'שבת',    'חולין קל״ה'],
			'Yom Kippur 5786'          => [2025, 10, 2,   5786, 7,  10, 'י׳ תשרי תשפ״ו',    '10 Tishrei, 5786','תשרי',  'חמישי',  'זבחים י״ח'],
			'Pesach 5786'              => [2026, 4,  2,   5786, 1,  15, 'ט״ו ניסן תשפ״ו',   '15 Nissan, 5786', 'ניסן',  'חמישי',  'מנחות פ״א'],
			'Purim 5784 (leap, Adar II)' => [2024, 3, 24, 5784, 13, 14, 'י״ד אדר ב׳ תשפ״ד', '14 Adar II, 5784','אדר ב׳','ראשון',  'בבא מציעא כ״ה'],
			'Chanukah 5787'            => [2026, 12, 6,   5787, 9,  26, 'כ״ו כסלו תשפ״ז',   '26 Kislev, 5787', 'כסלו',  'ראשון',  'ערכין י״ט'],
			'11 Adar 5786 (non-leap)'  => [2026, 2,  28,  5786, 12, 11, 'י״א אדר תשפ״ו',    '11 Adar, 5786',   'אדר',   'שבת',    'מנחות מ״ח'],
			'Lag BaOmer 5786'          => [2026, 5,  5,   5786, 2,  18, 'י״ח אייר תשפ״ו',   '18 Iyar, 5786',   'אייר',  'שלישי',  'חולין ה׳'],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| YOM TOV, PARSHA, SPECIAL SHABBOS, OMER
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('yomTovProvider')]
	public function hebrewYomTov(int $gy, int $gmo, int $gd, string $expected): void
	{
		$this->assertSame($expected, JewishDate::createFromDate($gy, $gmo, $gd)->format()->hebrew()->yomTov());
	}

	public static function yomTovProvider(): array
	{
		return [
			'Rosh Hashana'      => [2026, 9,  12, 'ראש השנה'],
			'Yom Kippur'        => [2025, 10, 2,  'יום כיפור'],
			'Pesach'            => [2026, 4,  2,  'פסח'],
			'Chol Hamoed Pesach' => [2024, 4, 27, 'חול המועד פסח'],
			'Purim'             => [2024, 3,  24, 'פורים'],
			'Chanukah day 2'    => [2026, 12, 6,  'ב׳ חנוכה'],
			'Lag BaOmer'        => [2026, 5,  5,  'ל״ג בעומר'],
			'ordinary weekday'  => [2026, 7,  19, ''],
		];
	}

	#[Test]
	public function hebrewParshaAndSpecialShabbos(): void
	{
		$this->assertSame('ואתחנן', JewishDate::createFromDate(2026, 7, 25)->format()->hebrew()->parshah());
		$this->assertSame('תצוה', JewishDate::createFromDate(2026, 2, 28)->format()->hebrew()->parshah());
		$this->assertSame('זכור', JewishDate::createFromDate(2026, 2, 28)->format()->hebrew()->specialShabbos());
	}

	#[Test]
	public function hebrewOmer(): void
	{
		$this->assertSame('ד׳ בעומר', JewishDate::createFromDate(2024, 4, 27)->format()->hebrew()->omer());
		$this->assertSame('ל״ג בעומר', JewishDate::createFromDate(2026, 5, 5)->format()->hebrew()->omer());
		$this->assertSame('', JewishDate::createFromDate(2026, 7, 19)->format()->hebrew()->omer());
	}

	/*
	|--------------------------------------------------------------------------
	| THE LETTER TABLES THEMSELVES
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('monthProvider')]
	public function hebrewMonthNames(int $year, int $month, string $expected): void
	{
		$this->assertSame($expected, JewishDate::create($year, $month, 1)->format()->hebrew()->month());
	}

	public static function monthProvider(): array
	{
		$rows = [];
		$leap = ['ניסן', 'אייר', 'סיון', 'תמוז', 'אב', 'אלול', 'תשרי', 'חשון', 'כסלו',
			'טבת', 'שבט', 'אדר א׳', 'אדר ב׳'];
		foreach ($leap as $i => $name) {
			$rows["5784 leap month " . ($i + 1)] = [5784, $i + 1, $name];
		}
		// a common year has a plain Adar in slot 12 and no slot 13
		$rows['5785 common Adar'] = [5785, 12, 'אדר'];

		return $rows;
	}

	#[Test]
	#[DataProvider('dayOfWeekProvider')]
	public function hebrewDayOfWeekNames(int $gy, int $gmo, int $gd, string $expected): void
	{
		$this->assertSame($expected, JewishDate::createFromDate($gy, $gmo, $gd)->format()->hebrew()->dayOfWeek());
	}

	public static function dayOfWeekProvider(): array
	{
		// 2026-07-19 is a Sunday; walk a full week from there
		return [
			'Sunday'    => [2026, 7, 19, 'ראשון'],
			'Monday'    => [2026, 7, 20, 'שני'],
			'Tuesday'   => [2026, 7, 21, 'שלישי'],
			'Wednesday' => [2026, 7, 22, 'רביעי'],
			'Thursday'  => [2026, 7, 23, 'חמישי'],
			'Friday'    => [2026, 7, 24, 'ששי'],
			'Shabbos'   => [2026, 7, 25, 'שבת'],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| ENUM NAMES
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function enumsRenderHebrew(): void
	{
		$this->assertSame('בראשית', Parshah::BERESHIS->hebrew());
		$this->assertSame('ואתחנן', Parshah::VAESCHANAN->hebrew());
		$this->assertSame('נח', Parshah::NOACH->hebrew());

		$this->assertSame('סוכות', YomTov::SUCCOS->hebrew());
		$this->assertSame('ראש השנה', YomTov::ROSH_HASHANA->hebrew());

		$this->assertSame('ברכות', MasechtaBavli::BERACHOS->hebrew());
		$this->assertSame('בבא מציעא', MasechtaBavli::BAVA_METZIA->hebrew());
	}

	#[Test]
	public function everyEnumCaseHasNonEmptyHebrewExceptNone(): void
	{
		foreach ([Parshah::cases(), YomTov::cases(), MasechtaBavli::cases()] as $cases) {
			foreach ($cases as $case) {
				if ($case->name === 'NONE') {
					continue;
				}
				$hebrew = $case->hebrew();
				$this->assertNotSame('', $hebrew, "{$case->name} has empty Hebrew");
				$this->assertMatchesRegularExpression('/^[\x{0590}-\x{05FF}\s\x{05F3}\x{05F4}]+$/u', $hebrew,
					"{$case->name} contains non-Hebrew characters: {$hebrew}");
			}
		}
	}
}
