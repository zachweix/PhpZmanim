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
use PhpZmanim\JewishDate;

class JewishDateTest extends TestCase
{
	/*
	|--------------------------------------------------------------------------
	| YEAR LENGTH (kviah)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function daysInMonthsInHaserYear(): void
	{
		$this->assertHaser(5773);
		$this->assertHaser(5777);
		$this->assertHaser(5781);

		$this->assertHaserLeap(5784);
		$this->assertHaserLeap(5790);
		$this->assertHaserLeap(5793);
	}

	#[Test]
	public function daysInMonthsInQesidrahYear(): void
	{
		$this->assertQesidrah(5769);
		$this->assertQesidrah(5772);
		$this->assertQesidrah(5778);
		$this->assertQesidrah(5786);
		$this->assertQesidrah(5789);
		$this->assertQesidrah(5792);

		$this->assertQesidrahLeap(5782);
	}

	#[Test]
	public function daysInMonthsInShalemYear(): void
	{
		$this->assertShalem(5770);
		$this->assertShalem(5780);
		$this->assertShalem(5783);
		$this->assertShalem(5785);
		$this->assertShalem(5788);
		$this->assertShalem(5791);
		$this->assertShalem(5794);

		$this->assertShalemLeap(5771);
		$this->assertShalemLeap(5774);
		$this->assertShalemLeap(5776);
		$this->assertShalemLeap(5779);
		$this->assertShalemLeap(5787);
		$this->assertShalemLeap(5795);
	}

	/*
	|--------------------------------------------------------------------------
	| BASIC CREATION
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function create(): void
	{
		$date = JewishDate::create(5786, 5, 2);
		$this->assertEquals('2 Av, 5786', $date->format()->english()->date());

		$date = JewishDate::createFromDate(2026, 7, 16);
		$this->assertEquals('2 Av, 5786', $date->format()->english()->date());
	}

	/*
	|--------------------------------------------------------------------------
	| GREGORIAN <-> JEWISH CONVERSION AND MANIPULATION
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function gregorianForwardMonthToMonth(): void
	{
		$cal = Carbon::createMidnightDate(2011, 1, 31);

		$hebrewDate = JewishDate::create($cal);
		$this->assertEquals(5771, $hebrewDate->getJewishYear());
		$this->assertEquals(11, $hebrewDate->getJewishMonth());
		$this->assertEquals(26, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->addDays(1);
		$this->assertEquals(2, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(11, $hebrewDate->getJewishMonth());
		$this->assertEquals(27, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 2, 28);
		$hebrewDate->setDate($cal);
		$this->assertEquals(2, $hebrewDate->getGregorianMonth());
		$this->assertEquals(28, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(12, $hebrewDate->getJewishMonth());
		$this->assertEquals(24, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->addDays(1);
		$this->assertEquals(3, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(12, $hebrewDate->getJewishMonth());
		$this->assertEquals(25, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 3, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDays(1);
		$this->assertEquals(4, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(13, $hebrewDate->getJewishMonth());
		$this->assertEquals(26, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->addMonthsGregorian(1);
		$this->assertEquals(5, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(1, $hebrewDate->getJewishMonth());
		$this->assertEquals(27, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 5, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDays(1);
		$this->assertEquals(6, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(2, $hebrewDate->getJewishMonth());
		$this->assertEquals(28, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 6, 30);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDays(1);
		$this->assertEquals(7, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(3, $hebrewDate->getJewishMonth());
		$this->assertEquals(29, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 7, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDays(1);
		$this->assertEquals(8, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5, $hebrewDate->getJewishMonth());
		$this->assertEquals(1, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 8, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDays(1);
		$this->assertEquals(9, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(6, $hebrewDate->getJewishMonth());
		$this->assertEquals(2, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 9, 30);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDays(1);
		$this->assertEquals(10, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(7, $hebrewDate->getJewishMonth());
		$this->assertEquals(3, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 10, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDays(1);
		$this->assertEquals(11, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5772, $hebrewDate->getJewishYear());
		$this->assertEquals(8, $hebrewDate->getJewishMonth());
		$this->assertEquals(4, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 11, 30);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDays(1);
		$this->assertEquals(12, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(9, $hebrewDate->getJewishMonth());
		$this->assertEquals(5, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->addMonthsGregorian(1);
		$this->assertEquals(2012, $hebrewDate->getGregorianYear());
		$this->assertEquals(1, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(10, $hebrewDate->getJewishMonth());
		$this->assertEquals(6, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->addDays(650);
		$this->assertEquals(2013, $hebrewDate->getGregorianYear());
		$this->assertEquals(10, $hebrewDate->getGregorianMonth());
		$this->assertEquals(12, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5774, $hebrewDate->getJewishYear());
		$this->assertEquals(8, $hebrewDate->getJewishMonth());
		$this->assertEquals(8, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->addDays(218);
		$this->assertEquals(2014, $hebrewDate->getGregorianYear());
		$this->assertEquals(5, $hebrewDate->getGregorianMonth());
		$this->assertEquals(18, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5774, $hebrewDate->getJewishYear());
		$this->assertEquals(2, $hebrewDate->getJewishMonth());
		$this->assertEquals(18, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->addMonthsJewish(1);
		$this->assertEquals(2014, $hebrewDate->getGregorianYear());
		$this->assertEquals(6, $hebrewDate->getGregorianMonth());
		$this->assertEquals(16, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5774, $hebrewDate->getJewishYear());
		$this->assertEquals(3, $hebrewDate->getJewishMonth());
		$this->assertEquals(18, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->addMonthsJewish(5);
		$this->assertEquals(2014, $hebrewDate->getGregorianYear());
		$this->assertEquals(11, $hebrewDate->getGregorianMonth());
		$this->assertEquals(11, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5775, $hebrewDate->getJewishYear());
		$this->assertEquals(8, $hebrewDate->getJewishMonth());
		$this->assertEquals(18, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->subMonthsJewish(5);
		$this->assertEquals(2014, $hebrewDate->getGregorianYear());
		$this->assertEquals(6, $hebrewDate->getGregorianMonth());
		$this->assertEquals(16, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5774, $hebrewDate->getJewishYear());
		$this->assertEquals(3, $hebrewDate->getJewishMonth());
		$this->assertEquals(18, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->subMonthsJewish(1);
		$this->assertEquals(2014, $hebrewDate->getGregorianYear());
		$this->assertEquals(5, $hebrewDate->getGregorianMonth());
		$this->assertEquals(18, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5774, $hebrewDate->getJewishYear());
		$this->assertEquals(2, $hebrewDate->getJewishMonth());
		$this->assertEquals(18, $hebrewDate->getJewishDayOfMonth());
	}

	#[Test]
	public function gregorianBackwardMonthToMonth(): void
	{
		$cal = Carbon::createMidnightDate(2011, 1, 1);

		$hebrewDate = JewishDate::create($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(2010, $hebrewDate->getGregorianYear());
		$this->assertEquals(12, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(10, $hebrewDate->getJewishMonth());
		$this->assertEquals(24, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 12, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(11, $hebrewDate->getGregorianMonth());
		$this->assertEquals(30, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(9, $hebrewDate->getJewishMonth());
		$this->assertEquals(23, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 11, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(10, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(8, $hebrewDate->getJewishMonth());
		$this->assertEquals(23, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 10, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(9, $hebrewDate->getGregorianMonth());
		$this->assertEquals(30, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(7, $hebrewDate->getJewishMonth());
		$this->assertEquals(22, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 9, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(8, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5770, $hebrewDate->getJewishYear());
		$this->assertEquals(6, $hebrewDate->getJewishMonth());
		$this->assertEquals(21, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 8, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(7, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5, $hebrewDate->getJewishMonth());
		$this->assertEquals(20, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 7, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(6, $hebrewDate->getGregorianMonth());
		$this->assertEquals(30, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(4, $hebrewDate->getJewishMonth());
		$this->assertEquals(18, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 6, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(5, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(3, $hebrewDate->getJewishMonth());
		$this->assertEquals(18, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 5, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(4, $hebrewDate->getGregorianMonth());
		$this->assertEquals(30, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(2, $hebrewDate->getJewishMonth());
		$this->assertEquals(16, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 4, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(3, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(1, $hebrewDate->getJewishMonth());
		$this->assertEquals(16, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 3, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(2, $hebrewDate->getGregorianMonth());
		$this->assertEquals(28, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(12, $hebrewDate->getJewishMonth());
		$this->assertEquals(14, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 2, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(1);
		$this->assertEquals(1, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(11, $hebrewDate->getJewishMonth());
		$this->assertEquals(16, $hebrewDate->getJewishDayOfMonth());

		/////////
		$cal->setDate(2014, 5, 18);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDays(218);
		$this->assertEquals(2013, $hebrewDate->getGregorianYear());
		$this->assertEquals(10, $hebrewDate->getGregorianMonth());
		$this->assertEquals(12, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5774, $hebrewDate->getJewishYear());
		$this->assertEquals(8, $hebrewDate->getJewishMonth());
		$this->assertEquals(8, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->subDays(653);
		$this->assertEquals(2011, $hebrewDate->getGregorianYear());
		$this->assertEquals(12, $hebrewDate->getGregorianMonth());
		$this->assertEquals(29, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5772, $hebrewDate->getJewishYear());
		$this->assertEquals(10, $hebrewDate->getJewishMonth());
		$this->assertEquals(3, $hebrewDate->getJewishDayOfMonth());
	}

	/*
	|--------------------------------------------------------------------------
	| HOLIDAYS (from JewishCalendar)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function roshHashana(): void
	{
		$date = JewishDate::create();

		$cal = Carbon::createMidnightDate(2023, 9, 16);
		$date->setDate($cal);
		$this->assertTrue($date->isRoshHashana());

		$date->addDays(1);
		$this->assertTrue($date->isRoshHashana());

		$date->addDays(1);
		$this->assertFalse($date->isRoshHashana());
		$this->assertTrue($date->isTaanis());
	}

	#[Test]
	public function purim(): void
	{
		$date = JewishDate::create();

		$date->setJewishDate(5783, JewishDate::ADAR, 14);
		$this->assertTrue($date->isPurim());
		$date->setIsMukafChoma(true);
		$this->assertFalse($date->isPurim());

		$date->addDays(1);
		$this->assertTrue($date->isPurim());
		$date->setIsMukafChoma(false);
		$this->assertFalse($date->isPurim());

		$date->setJewishDate(5784, JewishDate::ADAR, 14);
		$this->assertFalse($date->isPurim());
		$date->setIsMukafChoma(true);
		$this->assertFalse($date->isPurim());

		$date->setJewishDate(5784, JewishDate::ADAR_II, 14);
		$this->assertFalse($date->isPurim());
		$date->setIsMukafChoma(true);
		$this->assertFalse($date->isPurim());
	}

	/*
	|--------------------------------------------------------------------------
	| PARSHAH
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function parshah(): void
	{
		$date = JewishDate::create(Carbon::createMidnightDate(2023, 10, 14));
		$this->assertEquals("Bereshis", $date->getParshah()->english());
		$this->assertEquals("בראשית", $date->getParshah()->hebrew());

		$date = JewishDate::create(Carbon::createMidnightDate(2024, 2, 17));
		$this->assertEquals("Terumah", $date->getParshah()->english());
		$this->assertEquals("תרומה", $date->getParshah()->hebrew());
	}

	/*
	|--------------------------------------------------------------------------
	| DAF YOMI (values verified against current KosherJava)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function dafYomiBavli(): void
	{
		// The 14th Daf Yomi Bavli cycle began 2020-01-05 with Berachos 2; the prior
		// cycle's siyum was 2020-01-04 with Niddah 73.
		$date = JewishDate::create(Carbon::createMidnightDate(2020, 1, 5));
		$daf = $date->getDafYomiBavli();
		$this->assertEquals("Berachos", $daf->getMasechta()->english());
		$this->assertEquals(2, $daf->getDaf());
		$this->assertEquals("Berachos 2", $date->format()->english()->dafYomiBavli());
		$this->assertEquals("ברכות ב׳", $date->format()->hebrew()->dafYomiBavli());

		$date = JewishDate::create(Carbon::createMidnightDate(2020, 1, 4));
		$daf = $date->getDafYomiBavli();
		$this->assertEquals("Niddah", $daf->getMasechta()->english());
		$this->assertEquals(73, $daf->getDaf());

		// A pre-Shekalim-change cycle (before 1975).
		$date = JewishDate::create(Carbon::createMidnightDate(1980, 2, 3));
		$daf = $date->getDafYomiBavli();
		$this->assertEquals("Bava Basra", $daf->getMasechta()->english());
		$this->assertEquals(52, $daf->getDaf());
	}

	#[Test]
	public function dafYomiBavliBeforeCycleThrows(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		JewishDate::create(Carbon::createMidnightDate(1900, 1, 1))->getDafYomiBavli();
	}

	#[Test]
	public function dafYomiYerushalmi(): void
	{
		$date = JewishDate::create(Carbon::createMidnightDate(1980, 2, 3));
		$daf = $date->getDafYomiYerushalmi();
		$this->assertEquals("Berachos", $daf->getMasechta()->english());
		$this->assertEquals(2, $daf->getDaf());
		$this->assertEquals("Berachos 2", $date->format()->english()->dafYomiYerushalmi());
		$this->assertEquals("ברכות ב׳", $date->format()->hebrew()->dafYomiYerushalmi());

		$date = JewishDate::create(Carbon::createMidnightDate(2035, 12, 21));
		$daf = $date->getDafYomiYerushalmi();
		$this->assertEquals("Pe'ah", $daf->getMasechta()->english());
		$this->assertEquals(30, $daf->getDaf());

		// No Daf Yomi Yerushalmi on Yom Kippur or Tisha B'Av.
		$yomKippur = JewishDate::create()->setJewishDate(5784, JewishDate::TISHREI, 10);
		$this->assertNull($yomKippur->getDafYomiYerushalmi());
		$this->assertEquals("No Daf Today", $yomKippur->format()->english()->dafYomiYerushalmi());
		$this->assertEquals("אין דף היום", $yomKippur->format()->hebrew()->dafYomiYerushalmi());
	}

	/*
	|--------------------------------------------------------------------------
	| TEFILA RULES (values verified against current KosherJava)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function tefilaRules(): void
	{
		$date = JewishDate::create(Carbon::createMidnightDate(2023, 8, 21));
		$this->assertTrue($date->isTachanunRecitedShacharis());
		$this->assertTrue($date->isTachanunRecitedMincha());
		$this->assertFalse($date->isVeseinTalUmatarStartDate());
		$this->assertFalse($date->isVeseinTalUmatarStartingTonight());
		$this->assertFalse($date->isVeseinTalUmatarRecited());
		$this->assertTrue($date->isVeseinBerachaRecited());
		$this->assertFalse($date->isMashivHaruachStartDate());
		$this->assertFalse($date->isMashivHaruachEndDate());
		$this->assertFalse($date->isMashivHaruachRecited());
		$this->assertTrue($date->isMoridHatalRecited());
		$this->assertFalse($date->isHallelRecited());
		$this->assertFalse($date->isHallelShalemRecited());
		$this->assertFalse($date->isAlHanissimRecited());
		$this->assertFalse($date->isYaalehVeyavoRecited());
		$this->assertTrue($date->isMizmorLesodaRecited());

		$date = JewishDate::create(Carbon::createMidnightDate(2023, 10, 7));
		$this->assertFalse($date->isTachanunRecitedShacharis());
		$this->assertFalse($date->isTachanunRecitedMincha());
		$this->assertFalse($date->isVeseinTalUmatarStartDate());
		$this->assertFalse($date->isVeseinTalUmatarStartingTonight());
		$this->assertFalse($date->isVeseinTalUmatarRecited());
		$this->assertTrue($date->isVeseinBerachaRecited());
		$this->assertTrue($date->isMashivHaruachStartDate());
		$this->assertFalse($date->isMashivHaruachEndDate());
		$this->assertFalse($date->isMashivHaruachRecited());
		$this->assertTrue($date->isMoridHatalRecited());
		$this->assertTrue($date->isHallelRecited());
		$this->assertTrue($date->isHallelShalemRecited());
		$this->assertFalse($date->isAlHanissimRecited());
		$this->assertTrue($date->isYaalehVeyavoRecited());
		$this->assertFalse($date->isMizmorLesodaRecited());
	}

	/*
	|--------------------------------------------------------------------------
	| YOM TOV / OMER / ROSH CHODESH / FORMATTING (values verified against current KosherJava)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function yomTov(): void
	{
		$this->assertEquals("Pesach", JewishDate::create(Carbon::createMidnightDate(2024, 4, 23))->getYomTov()->english());
		$this->assertEquals("Shavuos", JewishDate::create(Carbon::createMidnightDate(2024, 6, 12))->getYomTov()->english());
		$this->assertEquals("Rosh Hashana", JewishDate::create(Carbon::createMidnightDate(2024, 10, 3))->getYomTov()->english());
		$this->assertEquals("Yom Kippur", JewishDate::create(Carbon::createMidnightDate(2024, 10, 12))->getYomTov()->english());
		$this->assertEquals("Purim", JewishDate::create(Carbon::createMidnightDate(2024, 3, 24))->getYomTov()->english());
		$this->assertEquals("Lag B'Omer", JewishDate::create(Carbon::createMidnightDate(2024, 5, 26))->getYomTov()->english());

		// The second day of Yom Tov is Chol Hamoed only in Israel.
		$secondDay = JewishDate::create(Carbon::createMidnightDate(2024, 4, 24));
		$this->assertEquals("Pesach", $secondDay->getYomTov()->english());
		$secondDay->setInIsrael(true);
		$this->assertEquals("Chol Hamoed Pesach", $secondDay->getYomTov()->english());

		// Chanukah formatting appends the day of Chanukah.
		$chanukah = JewishDate::create(Carbon::createMidnightDate(2024, 12, 29));
		$this->assertEquals(4, $chanukah->getDayOfChanukah());
		$this->assertEquals("Chanukah 4", $chanukah->format()->english()->yomTov());
		$this->assertEquals("ד׳ חנוכה", $chanukah->format()->hebrew()->yomTov());
	}

	#[Test]
	public function dayOfOmer(): void
	{
		$this->assertEquals(1, JewishDate::create(Carbon::createMidnightDate(2024, 4, 24))->getDayOfOmer());
		$this->assertEquals(33, JewishDate::create(Carbon::createMidnightDate(2024, 5, 26))->getDayOfOmer());
		$this->assertEquals(49, JewishDate::create(Carbon::createMidnightDate(2024, 6, 11))->getDayOfOmer());
		$this->assertEquals(-1, JewishDate::create(Carbon::createMidnightDate(2024, 1, 1))->getDayOfOmer());
	}

	#[Test]
	public function roshChodeshRules(): void
	{
		$this->assertTrue(JewishDate::create(Carbon::createMidnightDate(2024, 5, 8))->isRoshChodesh());
		$this->assertTrue(JewishDate::create(Carbon::createMidnightDate(2024, 5, 7))->isErevRoshChodesh());
		$this->assertTrue(JewishDate::create(Carbon::createMidnightDate(2024, 4, 6))->isShabbosMevorchim());
		$this->assertTrue(JewishDate::create(Carbon::createMidnightDate(2024, 7, 6))->isMacharChodesh());
		$this->assertTrue(JewishDate::create(Carbon::createMidnightDate(2024, 4, 8))->isYomKippurKatan());
		$this->assertTrue(JewishDate::create(Carbon::createMidnightDate(2024, 11, 11))->isBeHaB());

		// A plain weekday mid-month is none of the above.
		$plain = JewishDate::create(Carbon::createMidnightDate(2024, 6, 5));
		$this->assertFalse($plain->isRoshChodesh());
		$this->assertFalse($plain->isErevRoshChodesh());
		$this->assertFalse($plain->isShabbosMevorchim());
	}

	#[Test]
	public function specialShabbos(): void
	{
		$date = JewishDate::create(Carbon::createMidnightDate(2024, 3, 9));
		$this->assertEquals("Shekalim", $date->format()->english()->specialShabbos());
		$this->assertEquals("שקלים", $date->format()->hebrew()->specialShabbos());

		$this->assertEquals("Zachor", JewishDate::create(Carbon::createMidnightDate(2024, 3, 23))->format()->english()->specialShabbos());
		$this->assertEquals("Hagadol", JewishDate::create(Carbon::createMidnightDate(2024, 4, 20))->format()->english()->specialShabbos());
		$this->assertEquals("Nachamu", JewishDate::create(Carbon::createMidnightDate(2024, 8, 17))->format()->english()->specialShabbos());

		// Empty on an ordinary Shabbos.
		$this->assertEquals("", JewishDate::create(Carbon::createMidnightDate(2024, 6, 8))->format()->english()->specialShabbos());
	}

	#[Test]
	public function formatMonthDayOfWeekAndKviah(): void
	{
		// 5784 is a leap year, so Adar splits into Adar I and Adar II.
		$adarII = JewishDate::create(Carbon::createMidnightDate(2024, 3, 11));
		$this->assertEquals("Adar II", $adarII->format()->english()->month());
		$this->assertEquals("אדר ב׳", $adarII->format()->hebrew()->month());

		$adarI = JewishDate::create(Carbon::createMidnightDate(2024, 2, 25));
		$this->assertEquals("Adar I", $adarI->format()->english()->month());
		$this->assertEquals("אדר א׳", $adarI->format()->hebrew()->month());

		$date = JewishDate::create(Carbon::createMidnightDate(2024, 4, 23)); // a Tuesday
		$this->assertEquals("Tuesday", $date->format()->english()->dayOfWeek());
		$this->assertEquals("שלישי", $date->format()->hebrew()->dayOfWeek());
		$this->assertEquals("זחג", $date->format()->hebrew()->kviah());
	}

	#[Test]
	public function formatCarbon(): void
	{
		$this->assertEquals("2026-07-16", JewishDate::create(5786, 5, 2)->toCarbon('America/New_York')->toDateString());
	}

	/*
	|--------------------------------------------------------------------------
	| YEAR LENGTH HELPERS
	|--------------------------------------------------------------------------
	*/

	private function assertHaser(int $year): void
	{
		$jewishDate = JewishDate::create();
		$jewishDate->setJewishMonth(1);
		$jewishDate->setJewishYear($year);

		$this->assertFalse($jewishDate->isCheshvanLong());
		$this->assertTrue($jewishDate->isKislevShort());
	}

	private function assertHaserLeap(int $year): void
	{
		$jewishDate = JewishDate::create();
		$jewishDate->setJewishMonth(1);
		$jewishDate->setJewishYear($year);

		$this->assertHaser($year);
		$this->assertTrue($jewishDate->isJewishLeapYear());
	}

	private function assertQesidrah(int $year): void
	{
		$jewishDate = JewishDate::create();
		$jewishDate->setJewishMonth(1);
		$jewishDate->setJewishYear($year);

		$this->assertFalse($jewishDate->isCheshvanLong());
		$this->assertFalse($jewishDate->isKislevShort());
	}

	private function assertQesidrahLeap(int $year): void
	{
		$jewishDate = JewishDate::create();
		$jewishDate->setJewishMonth(1);
		$jewishDate->setJewishYear($year);

		$this->assertQesidrah($year);
		$this->assertTrue($jewishDate->isJewishLeapYear());
	}

	private function assertShalem(int $year): void
	{
		$jewishDate = JewishDate::create();
		$jewishDate->setJewishMonth(1);
		$jewishDate->setJewishYear($year);

		$this->assertTrue($jewishDate->isCheshvanLong());
		$this->assertFalse($jewishDate->isKislevShort());
	}

	private function assertShalemLeap(int $year): void
	{
		$jewishDate = JewishDate::create();
		$jewishDate->setJewishMonth(1);
		$jewishDate->setJewishYear($year);

		$this->assertShalem($year);
		$this->assertTrue($jewishDate->isJewishLeapYear());
	}
}
