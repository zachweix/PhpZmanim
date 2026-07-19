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
use PhpZmanim\Torah\MasechtaYerushalmi;
use PhpZmanim\Torah\YomTov;

/**
 * Coverage for the formatter options array accepted by hebrew() / english().
 *
 * Every expected string is ground truth generated from the current KosherJava
 * HebrewDateFormatter, driving the equivalent setter for each option:
 *   useGershGershayim   -> setUseGershGershayim()
 *   useFinalFormLetters -> setUseFinalFormLetters()
 *   useLongHebrewYears  -> setUseLongHebrewYears()
 *   omerPrefix          -> setHebrewOmerPrefix()
 *   months              -> setTransliteratedMonthList() / setHebrewMonthList()
 *   shabbos             -> setTransliteratedShabbosDayOfWeek()
 *   names               -> setTransliteratedHolidayList() / setTransliteratedParshiosList()
 * compared against Java's format(), formatOmer(), formatDayOfWeek(), formatYomTov(),
 * formatRoshChodesh() and formatDafYomi*().
 *
 * The options array is a PHP-side replacement for Java's stateful setters; the rendered
 * output is what is being pinned here, and it matches Java value for value.
 */
class FormatterOptionsTest extends TestCase
{
	private const CUSTOM_MONTHS = ['Nisan', 'Iyyar', 'Sivan', 'Tammuz', 'Av', 'Elul', 'Tishri', 'Heshvan',
		'Kislev', 'Tevet', 'Shevat', 'Adar', 'Adar II', 'Adar I'];

	/*
	|--------------------------------------------------------------------------
	| DEFAULTS — passing no options must not change anything
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function defaultsAreUnchangedByAnEmptyOptionsArray(): void
	{
		$date = JewishDate::create(5784, 7, 15);

		$this->assertSame($date->format()->hebrew()->date(), $date->format()->hebrew([])->date());
		$this->assertSame($date->format()->english()->date(), $date->format()->english([])->date());

		$this->assertSame('ט״ו תשרי תשפ״ד', $date->format()->hebrew()->date());
		$this->assertSame('15 Tishrei, 5784', $date->format()->english()->date());
	}

	/*
	|--------------------------------------------------------------------------
	| HEBREW NUMBER OPTIONS, EXERCISED THROUGH THE YEAR
	|--------------------------------------------------------------------------
	| The year is where useGershGershayim / useFinalFormLetters / useLongHebrewYears
	| all become visible. 5780/5740/5750/5790/5720 are the years whose final letter
	| has a final form; 5000 exercises Java's "alafim" branch.
	*/

	#[Test]
	#[DataProvider('yearProvider')]
	public function hebrewYearOptions(int $year, string $default, string $noGersh, string $finalForm, string $longYears): void
	{
		$date = JewishDate::create($year, 7, 1);

		$this->assertSame($default, $date->format()->hebrew()->date());
		$this->assertSame($noGersh, $date->format()->hebrew(['useGershGershayim' => false])->date());
		$this->assertSame($finalForm, $date->format()->hebrew(['useFinalFormLetters' => true])->date());
		$this->assertSame($longYears, $date->format()->hebrew(['useLongHebrewYears' => true])->date());
	}

	public static function yearProvider(): array
	{
		return [
			//                      year    default              noGersh             finalForm            longYears
			'5784 (ends dalet)' => [5784, 'א׳ תשרי תשפ״ד', 'א תשרי תשפד', 'א׳ תשרי תשפ״ד', 'א׳ תשרי ה׳ תשפ״ד'],
			'5780 (pe -> fe)'   => [5780, 'א׳ תשרי תש״פ',  'א תשרי תשפ',  'א׳ תשרי תש״ף',  'א׳ תשרי ה׳ תש״פ'],
			'5740 (mem sofit)'  => [5740, 'א׳ תשרי תש״מ',  'א תשרי תשמ',  'א׳ תשרי תש״ם',  'א׳ תשרי ה׳ תש״מ'],
			'5750 (nun sofit)'  => [5750, 'א׳ תשרי תש״נ',  'א תשרי תשנ',  'א׳ תשרי תש״ן',  'א׳ תשרי ה׳ תש״נ'],
			'5790 (tsadi sofit)' => [5790, 'א׳ תשרי תש״צ', 'א תשרי תשצ',  'א׳ תשרי תש״ץ',  'א׳ תשרי ה׳ תש״צ'],
			'5720 (kaf sofit)'  => [5720, 'א׳ תשרי תש״כ',  'א תשרי תשכ',  'א׳ תשרי תש״ך',  'א׳ תשרי ה׳ תש״כ'],
			'5788 (two letters)' => [5788, 'א׳ תשרי תשפ״ח', 'א תשרי תשפח', 'א׳ תשרי תשפ״ח', 'א׳ תשרי ה׳ תשפ״ח'],
			'5792 (two letters)' => [5792, 'א׳ תשרי תשצ״ב', 'א תשרי תשצב', 'א׳ תשרי תשצ״ב', 'א׳ תשרי ה׳ תשצ״ב'],
			'5000 (alafim)'     => [5000, 'א׳ תשרי ה׳ אלפים', 'א תשרי ה אלפים', 'א׳ תשרי ה׳ אלפים', 'א׳ תשרי ה׳ אלפים'],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| FINAL FORM LETTERS DO NOT APPLY TO THE DAY OF MONTH
	|--------------------------------------------------------------------------
	| Java only substitutes a final form when the number ends in 0 AND is not a
	| "single digit number". Every day of month falls in the single-digit bucket,
	| so day 20 stays כ׳ and never becomes ך׳.
	*/

	#[Test]
	#[DataProvider('dayProvider')]
	public function finalFormLettersNeverAffectTheDayOfMonth(int $day, string $expected): void
	{
		$date = JewishDate::create(5784, 7, $day);

		$this->assertSame($expected, $date->format()->hebrew()->date());
		$this->assertSame($expected, $date->format()->hebrew(['useFinalFormLetters' => true])->date());
	}

	public static function dayProvider(): array
	{
		return [
			'1'  => [1,  'א׳ תשרי תשפ״ד'],
			'10' => [10, 'י׳ תשרי תשפ״ד'],
			'15' => [15, 'ט״ו תשרי תשפ״ד'],
			'16' => [16, 'ט״ז תשרי תשפ״ד'],
			'20' => [20, 'כ׳ תשרי תשפ״ד'],
			'21' => [21, 'כ״א תשרי תשפ״ד'],
			'30' => [30, 'ל׳ תשרי תשפ״ד'],
		];
	}

	#[Test]
	public function combinedHebrewOptions(): void
	{
		$date = JewishDate::create(5784, 7, 20);

		$this->assertSame('כ תשרי תשפד', $date->format()->hebrew([
			'useFinalFormLetters' => true,
			'useGershGershayim' => false,
		])->date());

		$this->assertSame('כ׳ תשרי ה׳ תשפ״ד', $date->format()->hebrew([
			'useLongHebrewYears' => true,
			'useFinalFormLetters' => true,
		])->date());
	}

	/*
	|--------------------------------------------------------------------------
	| MONTH, DAY OF WEEK AND OMER OPTIONS
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function customMonthList(): void
	{
		$date = JewishDate::create(5784, 7, 15);

		$this->assertSame('15 Tishrei, 5784', $date->format()->english()->date());
		$this->assertSame('15 Tishri, 5784', $date->format()->english(['months' => self::CUSTOM_MONTHS])->date());
	}

	#[Test]
	public function customMonthListPropagatesThroughRoshChodesh(): void
	{
		// roshChodesh() builds a second formatter internally, so this pins option propagation.
		$date = JewishDate::create(5784, 8, 1);

		$this->assertSame('Rosh Chodesh Cheshvan', $date->format()->english()->roshChodesh());
		$this->assertSame('Rosh Chodesh Heshvan', $date->format()->english(['months' => self::CUSTOM_MONTHS])->roshChodesh());
	}

	#[Test]
	public function shabbosOption(): void
	{
		$shabbos = JewishDate::create(5784, 7, 15);

		$this->assertSame('Shabbos', $shabbos->format()->english()->dayOfWeek());
		$this->assertSame('Shabbat', $shabbos->format()->english(['shabbos' => 'Shabbat'])->dayOfWeek());
	}

	#[Test]
	public function hebrewDaysOfWeekOption(): void
	{
		$shabbos = JewishDate::create(5784, 7, 15);
		$sunday = JewishDate::create(5784, 7, 16);

		$this->assertSame('שבת', $shabbos->format()->hebrew()->dayOfWeek());
		$this->assertSame('ראשון', $sunday->format()->hebrew()->dayOfWeek());

		$short = ['א׳', 'ב׳', 'ג׳', 'ד׳', 'ה׳', 'ו׳', 'שבת'];
		$this->assertSame('א׳', $sunday->format()->hebrew(['daysOfWeek' => $short])->dayOfWeek());
	}

	#[Test]
	public function omerPrefixOption(): void
	{
		$date = JewishDate::create(5784, 1, 21);

		$this->assertSame('ו׳ בעומר', $date->format()->hebrew()->omer());
		$this->assertSame('ו׳ לעומר', $date->format()->hebrew(['omerPrefix' => 'ל'])->omer());
	}

	/*
	|--------------------------------------------------------------------------
	| NAME OVERRIDES
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function yomTovNameOverride(): void
	{
		$succos = JewishDate::create(5784, 7, 15);

		$this->assertSame('Succos', $succos->format()->english()->yomTov());
		$this->assertSame('Sukkot', $succos->format()->english([
			'names' => [YomTov::class => ['SUCCOS' => 'Sukkot']],
		])->yomTov());
	}

	#[Test]
	public function nameOverridesAreScopedToTheirEnum(): void
	{
		// On this date the Yerushalmi daf is Shabbos while the Bavli daf is Bava Metzia.
		// MasechtaBavli and MasechtaYerushalmi both have a SHABBOS case, so a Bavli-keyed
		// override must not leak into the Yerushalmi output.
		$date = JewishDate::createFromDate(2024, 3, 10);

		$this->assertSame('Bava Metzia 11', $date->format()->english()->dafYomiBavli());
		$this->assertSame('Shabbos 88', $date->format()->english()->dafYomiYerushalmi());

		$yerushalmiOnly = ['names' => [MasechtaYerushalmi::class => ['SHABBOS' => 'Shabbat Yerushalmi']]];
		$bavliOnly = ['names' => [MasechtaBavli::class => ['SHABBOS' => 'Shabbos Bavli']]];

		$this->assertSame('Shabbat Yerushalmi 88', $date->format()->english($yerushalmiOnly)->dafYomiYerushalmi());
		$this->assertSame('Shabbos 88', $date->format()->english($bavliOnly)->dafYomiYerushalmi());
	}

	#[Test]
	public function unlistedNamesKeepTheirDefaults(): void
	{
		$succos = JewishDate::create(5784, 7, 15);

		$this->assertSame('Pesach', JewishDate::create(5784, 1, 15)->format()->english([
			'names' => [YomTov::class => ['SUCCOS' => 'Sukkot']],
		])->yomTov());
		$this->assertSame('Sukkot', $succos->format()->english([
			'names' => [YomTov::class => ['SUCCOS' => 'Sukkot']],
		])->yomTov());
	}

	/*
	|--------------------------------------------------------------------------
	| VALIDATION — a typo must fail loudly rather than be ignored
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function unknownOptionKeyThrows(): void
	{
		$date = JewishDate::create(5784, 7, 15);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Unknown formatter option: useGershGershyim');

		$date->format()->hebrew(['useGershGershyim' => false]);
	}

	#[Test]
	public function hebrewOnlyOptionIsRejectedByEnglish(): void
	{
		$date = JewishDate::create(5784, 7, 15);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Unknown formatter option: useGershGershayim');

		$date->format()->english(['useGershGershayim' => false]);
	}

	#[Test]
	public function wrongSizedMonthListThrows(): void
	{
		$date = JewishDate::create(5784, 7, 15);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('must be an array of exactly 14 entries');

		$date->format()->english(['months' => ['too', 'few']]);
	}

	#[Test]
	public function wrongSizedDaysOfWeekListThrows(): void
	{
		$date = JewishDate::create(5784, 7, 15);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('must be an array of exactly 7 entries');

		$date->format()->hebrew(['daysOfWeek' => ['only', 'two']]);
	}

	#[Test]
	public function namesKeyedBySomethingOtherThanAnEnumThrows(): void
	{
		$date = JewishDate::create(5784, 7, 15);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('must be keyed by enum class name');

		$date->format()->english(['names' => ['NotAnEnum' => ['X' => 'y']]]);
	}

	#[Test]
	public function unknownEnumCaseInNamesThrows(): void
	{
		$date = JewishDate::create(5784, 7, 15);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('NOT_A_CASE');

		$date->format()->english(['names' => [YomTov::class => ['NOT_A_CASE' => 'y']]]);
	}

	/*
	|--------------------------------------------------------------------------
	| IMMUTABILITY — options never leak onto the JewishDate or another formatter
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function optionsDoNotLeakBetweenFormatters(): void
	{
		$date = JewishDate::create(5784, 7, 15);

		$customized = $date->format()->hebrew(['useGershGershayim' => false]);
		$this->assertSame('טו תשרי תשפד', $customized->date());

		// a fresh formatter off the same JewishDate is unaffected
		$this->assertSame('ט״ו תשרי תשפ״ד', $date->format()->hebrew()->date());
		$this->assertSame('15 Tishrei, 5784', $date->format()->english()->date());
	}
}
