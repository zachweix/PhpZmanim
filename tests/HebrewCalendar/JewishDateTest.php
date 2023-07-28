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
use PhpZmanim\HebrewCalendar\JewishDate;

class JewishDateTest extends TestCase {

	/** @test */
	public function testDaysInMonth() {
		$hebrewDate = new JewishDate();

		$cal = Carbon::createMidnightDate(2011, 1, 1);
		$hebrewDate->setDate($cal);

		$this->assertDaysInMonth(false, $hebrewDate);
	}

	/** @test */
	public function testDaysInMonthLeapYear() {
		$hebrewDate = new JewishDate();

		$cal = Carbon::createMidnightDate(2012, 1, 1);
		$hebrewDate->setDate($cal);

		$this->assertDaysInMonth(true, $hebrewDate);
	}

	/** @test */
	public function testDaysInMonth100Year() {
		$hebrewDate = new JewishDate();

		$cal = Carbon::createMidnightDate(2100, 1, 1);
		$hebrewDate->setDate($cal);

		$this->assertDaysInMonth(false, $hebrewDate);
	}

	/** @test */
	public function testDaysInMonth400Year() {
		$hebrewDate = new JewishDate();

		$cal = Carbon::createMidnightDate(2000, 1, 1);
		$hebrewDate->setDate($cal);

		$this->assertDaysInMonth(true, $hebrewDate);
	}

	/** @test */
	public function daysInMonthsInHaserYear() {
		$this->assertHaser(5773);
		$this->assertHaser(5777);
		$this->assertHaser(5781);

		$this->assertHaserLeap(5784);
		$this->assertHaserLeap(5790);
		$this->assertHaserLeap(5793);
	}

	/** @test */
	public function daysInMonthsInQesidrahYear() {
		$this->assertQesidrah(5769);
		$this->assertQesidrah(5772);
		$this->assertQesidrah(5778);
		$this->assertQesidrah(5786);
		$this->assertQesidrah(5789);
		$this->assertQesidrah(5792);

		$this->assertQesidrahLeap(5782);
	}

	/** @test */
	public function daysInMonthsInShalemYear() {
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

	/** @test */
	public function gregorianForwardMonthToMonth() {
		$cal = Carbon::createMidnightDate(2011, 1, 31);

		$hebrewDate = new JewishDate($cal);
		$this->assertEquals(5771, $hebrewDate->getJewishYear());
		$this->assertEquals(11, $hebrewDate->getJewishMonth());
		$this->assertEquals(26, $hebrewDate->getJewishDayOfMonth());

		$hebrewDate->addDay();
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

		$hebrewDate->addDay();
		$this->assertEquals(3, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(12, $hebrewDate->getJewishMonth());
		$this->assertEquals(25, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 3, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDay();
		$this->assertEquals(4, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(13, $hebrewDate->getJewishMonth());
		$this->assertEquals(26, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 4, 30);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDay();
		$this->assertEquals(5, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(1, $hebrewDate->getJewishMonth());
		$this->assertEquals(27, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 5, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDay();
		$this->assertEquals(6, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(2, $hebrewDate->getJewishMonth());
		$this->assertEquals(28, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 6, 30);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDay();
		$this->assertEquals(7, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(3, $hebrewDate->getJewishMonth());
		$this->assertEquals(29, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 7, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDay();
		$this->assertEquals(8, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5, $hebrewDate->getJewishMonth());
		$this->assertEquals(1, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 8, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDay();
		$this->assertEquals(9, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(6, $hebrewDate->getJewishMonth());
		$this->assertEquals(2, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 9, 30);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDay();
		$this->assertEquals(10, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(7, $hebrewDate->getJewishMonth());
		$this->assertEquals(3, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 10, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDay();
		$this->assertEquals(11, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5772, $hebrewDate->getJewishYear());
		$this->assertEquals(8, $hebrewDate->getJewishMonth());
		$this->assertEquals(4, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 11, 30);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDay();
		$this->assertEquals(12, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(9, $hebrewDate->getJewishMonth());
		$this->assertEquals(5, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2011, 12, 31);
		$hebrewDate->setDate($cal);
		$hebrewDate->addDay();
		$this->assertEquals(2012, $hebrewDate->getGregorianYear());
		$this->assertEquals(1, $hebrewDate->getGregorianMonth());
		$this->assertEquals(1, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(10, $hebrewDate->getJewishMonth());
		$this->assertEquals(6, $hebrewDate->getJewishDayOfMonth());
	}


	/** @test */
	public function gregorianBackwardMonthToMonth() {
		$cal = Carbon::createMidnightDate(2011, 1, 1);

		$hebrewDate = new JewishDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(2010, $hebrewDate->getGregorianYear());
		$this->assertEquals(12, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(10, $hebrewDate->getJewishMonth());
		$this->assertEquals(24, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 12, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(11, $hebrewDate->getGregorianMonth());
		$this->assertEquals(30, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(9, $hebrewDate->getJewishMonth());
		$this->assertEquals(23, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 11, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(10, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(8, $hebrewDate->getJewishMonth());
		$this->assertEquals(23, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 10, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(9, $hebrewDate->getGregorianMonth());
		$this->assertEquals(30, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(7, $hebrewDate->getJewishMonth());
		$this->assertEquals(22, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 9, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(8, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5770, $hebrewDate->getJewishYear());
		$this->assertEquals(6, $hebrewDate->getJewishMonth());
		$this->assertEquals(21, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 8, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(7, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(5, $hebrewDate->getJewishMonth());
		$this->assertEquals(20, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 7, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(6, $hebrewDate->getGregorianMonth());
		$this->assertEquals(30, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(4, $hebrewDate->getJewishMonth());
		$this->assertEquals(18, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 6, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(5, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(3, $hebrewDate->getJewishMonth());
		$this->assertEquals(18, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 5, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(4, $hebrewDate->getGregorianMonth());
		$this->assertEquals(30, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(2, $hebrewDate->getJewishMonth());
		$this->assertEquals(16, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 4, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(3, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(1, $hebrewDate->getJewishMonth());
		$this->assertEquals(16, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 3, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(2, $hebrewDate->getGregorianMonth());
		$this->assertEquals(28, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(12, $hebrewDate->getJewishMonth());
		$this->assertEquals(14, $hebrewDate->getJewishDayOfMonth());

		$cal->setDate(2010, 2, 1);
		$hebrewDate->setDate($cal);
		$hebrewDate->subDay();
		$this->assertEquals(1, $hebrewDate->getGregorianMonth());
		$this->assertEquals(31, $hebrewDate->getGregorianDayOfMonth());
		$this->assertEquals(11, $hebrewDate->getJewishMonth());
		$this->assertEquals(16, $hebrewDate->getJewishDayOfMonth());

	}

	private function assertDaysInMonth($febIsLeap, $hebrewDate) {
		$this->assertEquals(31, JewishDate::getLastDayOfGregorianMonth(1, $hebrewDate->getGregorianYear()));
		$this->assertEquals($febIsLeap ? 29 : 28, JewishDate::getLastDayOfGregorianMonth(2, $hebrewDate->getGregorianYear()));
		$this->assertEquals(31, JewishDate::getLastDayOfGregorianMonth(3, $hebrewDate->getGregorianYear()));
		$this->assertEquals(30, JewishDate::getLastDayOfGregorianMonth(4, $hebrewDate->getGregorianYear()));
		$this->assertEquals(31, JewishDate::getLastDayOfGregorianMonth(5, $hebrewDate->getGregorianYear()));
		$this->assertEquals(30, JewishDate::getLastDayOfGregorianMonth(6, $hebrewDate->getGregorianYear()));
		$this->assertEquals(31, JewishDate::getLastDayOfGregorianMonth(7, $hebrewDate->getGregorianYear()));
		$this->assertEquals(31, JewishDate::getLastDayOfGregorianMonth(8, $hebrewDate->getGregorianYear()));
		$this->assertEquals(30, JewishDate::getLastDayOfGregorianMonth(9, $hebrewDate->getGregorianYear()));
		$this->assertEquals(31, JewishDate::getLastDayOfGregorianMonth(10, $hebrewDate->getGregorianYear()));
		$this->assertEquals(30, JewishDate::getLastDayOfGregorianMonth(11, $hebrewDate->getGregorianYear()));
		$this->assertEquals(31, JewishDate::getLastDayOfGregorianMonth(12, $hebrewDate->getGregorianYear()));
	}

	private function assertHaser($year) {
		$jewishDate = new JewishDate();
		$jewishDate->setJewishYear($year);

		$this->assertFalse(JewishDate::isCheshvanLong($jewishDate->getJewishYear()));
		$this->assertTrue(JewishDate::isKislevShort($jewishDate->getJewishYear()));
	}


	private function assertHaserLeap($year) {
		$jewishDate = new JewishDate();
		$jewishDate->setJewishYear($year);

		$this->assertHaser($year);
		$this->assertTrue(JewishDate::isJewishLeapYear($jewishDate->getJewishYear()));
	}


	private function assertQesidrah($year) {
		$jewishDate = new JewishDate();
		$jewishDate->setJewishYear($year);

		$this->assertFalse(JewishDate::isCheshvanLong($jewishDate->getJewishYear()));
		$this->assertFalse(JewishDate::isKislevShort($jewishDate->getJewishYear()));
	}


	private function assertQesidrahLeap($year) {
		$jewishDate = new JewishDate();
		$jewishDate->setJewishYear($year);

		$this->assertQesidrah($year);
		$this->assertTrue(JewishDate::isJewishLeapYear($jewishDate->getJewishYear()));
	}


	private function assertShalem($year) {
		$jewishDate = new JewishDate();
		$jewishDate->setJewishYear($year);

		$this->assertTrue(JewishDate::isCheshvanLong($jewishDate->getJewishYear()));
		$this->assertFalse(JewishDate::isKislevShort($jewishDate->getJewishYear()));
	}


	private function assertShalemLeap($year) {
		$jewishDate = new JewishDate();
		$jewishDate->setJewishYear($year);

		$this->assertShalem($year);
		$this->assertTrue(JewishDate::isJewishLeapYear($jewishDate->getJewishYear()));
	}
}