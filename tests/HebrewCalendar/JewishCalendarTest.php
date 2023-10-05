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
use PhpZmanim\HebrewCalendar\HebrewDateFormatter;
use PhpZmanim\HebrewCalendar\JewishCalendar;
use PhpZmanim\HebrewCalendar\JewishDate;
use PhpZmanim\HebrewCalendar\TefilaRules;
use PhpZmanim\Zmanim;

class JewishCalendarTest extends TestCase {

	/** @test */
	public function testRoshHashana() {
		$date = new JewishCalendar();

		$cal = Carbon::createMidnightDate(2023, 9, 16);
		$date->setDate($cal);
		$this->assertTrue($date->isRoshHashana());

		$date->addDays(1);
		$this->assertTrue($date->isRoshHashana());

		$date->addDays(1);
		$this->assertFalse($date->isRoshHashana());
		$this->assertTrue($date->isTaanis());
	}

	/** @test */
	public function testDafYomi() {
		$date = new JewishCalendar(Carbon::createMidnightDate(2023, 9, 16));

		$daf = $date->getDafYomiBavli();
		$formatter = HebrewDateFormatter::create();

		$this->assertEquals("Kiddushin 34", $formatter->formatDafYomiBavli($daf));
		$this->assertEquals("\u05E7\u05D9\u05D3\u05D5\u05E9\u05D9\u05DF \u05DC\u05F4\u05D3", $formatter->setHebrewFormat(true)->formatDafYomiBavli($daf));

		$daf = $date->getDafYomiYerushalmi();
		$formatter = Zmanim::format();

		$this->assertEquals("Ma'aser Sheni 7", $formatter->formatDafYomiYerushalmi($daf));
		$this->assertEquals("\u05de\u05e2\u05e9\u05e8 \u05e9\u05e0\u05d9 \u05D6\u05F3", $formatter->setHebrewFormat(true)->formatDafYomiYerushalmi($daf));
	}

	/** @test */
	public function testParsha() {
		$date = new JewishCalendar(Carbon::createMidnightDate(2023, 10, 14));

		$this->assertEquals("Bereshis", HebrewDateFormatter::create()->formatParsha($date));
		$this->assertEquals("\u05D1\u05E8\u05D0\u05E9\u05D9\u05EA", HebrewDateFormatter::create()->setHebrewFormat(true)->formatParsha($date));

		$date = Zmanim::jewishCalendar(Carbon::createMidnightDate(2024, 2, 17));

		$this->assertEquals("Terumah", HebrewDateFormatter::create()->formatParsha($date));
		$this->assertEquals("\u05EA\u05E8\u05D5\u05DE\u05D4", HebrewDateFormatter::create()->setHebrewFormat(true)->formatParsha($date));
	}

	/** @test */
	public function testPurim() {
		$date = new JewishCalendar();

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

	/** @test */
	public function testTefilaRules() {
		$date = new JewishCalendar(Carbon::createMidnightDate(2023, 8, 21));

		$this->assertTrue(TefilaRules::isTachanunRecitedShacharis($date));
		$this->assertTrue(TefilaRules::isTachanunRecitedMincha($date));
		$this->assertFalse(TefilaRules::isVeseinTalUmatarStartDate($date));
		$this->assertFalse(TefilaRules::isVeseinTalUmatarStartingTonight($date));
		$this->assertFalse(TefilaRules::isVeseinTalUmatarRecited($date));
		$this->assertTrue(TefilaRules::isVeseinBerachaRecited($date));
		$this->assertFalse(TefilaRules::isMashivHaruachStartDate($date));
		$this->assertFalse(TefilaRules::isMashivHaruachEndDate($date));
		$this->assertFalse(TefilaRules::isMashivHaruachRecited($date));
		$this->assertTrue(TefilaRules::isMoridHatalRecited($date));
		$this->assertFalse(TefilaRules::isHallelRecited($date));
		$this->assertFalse(TefilaRules::isHallelShalemRecited($date));
		$this->assertFalse(TefilaRules::isAlHanissimRecited($date));
		$this->assertFalse(TefilaRules::isYaalehVeyavoRecited($date));
		$this->assertTrue(TefilaRules::isMizmorLesodaRecited($date));


		$date = new JewishCalendar(Carbon::createMidnightDate(2023, 10, 7));

		$this->assertFalse(TefilaRules::isTachanunRecitedShacharis($date));
		$this->assertFalse(TefilaRules::isTachanunRecitedMincha($date));
		$this->assertFalse(TefilaRules::isVeseinTalUmatarStartDate($date));
		$this->assertFalse(TefilaRules::isVeseinTalUmatarStartingTonight($date));
		$this->assertFalse(TefilaRules::isVeseinTalUmatarRecited($date));
		$this->assertTrue(TefilaRules::isVeseinBerachaRecited($date));
		$this->assertTrue(TefilaRules::isMashivHaruachStartDate($date));
		$this->assertFalse(TefilaRules::isMashivHaruachEndDate($date));
		$this->assertFalse(TefilaRules::isMashivHaruachRecited($date));
		$this->assertTrue(TefilaRules::isMoridHatalRecited($date));
		$this->assertTrue(TefilaRules::isHallelRecited($date));
		$this->assertTrue(TefilaRules::isHallelShalemRecited($date));
		$this->assertFalse(TefilaRules::isAlHanissimRecited($date));
		$this->assertTrue(TefilaRules::isYaalehVeyavoRecited($date));
		$this->assertFalse(TefilaRules::isMizmorLesodaRecited($date));
	}
}