<?php

/**
 * Zmanim PHP API
 * Copyright (C) 2019 Zachary Weixelbaum
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
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calendar\ComplexZmanimCalendar;
use PhpZmanim\Calendar\ZmanimCalendar;
use PhpZmanim\Geo\GeoLocation;
use PhpZmanim\Zmanim;

class ZmanimCalendarTest extends TestCase {

	protected $geo;
	protected $zenith;

	protected function setUp(): void {
		parent::setUp();

		/*
		 * Setup some basic data for our tests
		 */

		$lakewood = [
			'Lakewood, NJ',
			40.0721087,
			-74.2400243,
			15,
			'America/New_York',
		];

		$this->geo = new GeoLocation($lakewood[0], $lakewood[1], $lakewood[2], $lakewood[3], $lakewood[4]);
	}

	/** 
	 * @test
	 */
	public function testTzais() {
		$zmanimCalendar = new ZmanimCalendar($this->geo, 2017, 10, 17);

		$tzais = $zmanimCalendar->getTzais();
		$this->assertEquals($tzais->format('Y-m-d\TH:i:sP'), "2017-10-17T18:54:29-04:00");
	}

	/** 
	 * @test
	 */
	public function testTzaisWithCustomDegreeOffset() {
		$czc = new ComplexZmanimCalendar($this->geo, 2017, 10, 17);

		$tzais = $czc->getTzais19Point8Degrees();
		$this->assertEquals($tzais->format('Y-m-d\TH:i:sP'), "2017-10-17T19:53:34-04:00");
	}

	/** 
	 * @test
	 */
	public function useWrapperClass() {
		$zmanim = Zmanim::create(2017, 10, 17, 'Lakewood, NJ', 40.0721087, -74.2400243, 15, 'America/New_York');

		$tzais = $zmanim->getTzais19Point8Degrees();
		$this->assertEquals($tzais->format('Y-m-d\TH:i:sP'), "2017-10-17T19:53:34-04:00");
	}

	/** 
	 * @test
	 */
	public function testBasicTimes() {
		$zmanim = Zmanim::create(2019, 2, 22, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');

		$zmanim->setUseElevation(false);

		$this->assertEquals($zmanim->getAlos96()->format('Y-m-d\TH:i:sP'), "2019-02-22T05:04:50-05:00");
		$this->assertEquals($zmanim->getAlos72()->format('Y-m-d\TH:i:sP'), "2019-02-22T05:28:50-05:00");
		$this->assertEquals($zmanim->getCandleLighting()->format('Y-m-d\TH:i:sP'), "2019-02-22T17:22:38-05:00");
		$this->assertEquals($zmanim->getTzais72()->format('Y-m-d\TH:i:sP'), "2019-02-22T18:52:38-05:00");
	}

	/** 
	 * @test
	 */
	public function testBaalHatanya() {
		$zmanim = Zmanim::create(2019, 2, 18, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');

		$this->assertEquals($zmanim->getMinchaGedolaBaalHatanya()->format('Y-m-d\TH:i:sP'), "2019-02-18T12:38:34-05:00");
		$this->assertEquals($zmanim->getPlagHaminchaBaalHatanya()->format('Y-m-d\TH:i:sP'), "2019-02-18T16:31:32-05:00");
		$this->assertEquals($zmanim->getTzaisBaalHatanya()->format('Y-m-d\TH:i:sP'), "2019-02-18T18:03:40-05:00");
	}

	/** 
	 * @test
	 */
	public function testChangingDate() {
		$zmanim = Zmanim::create(2019, 2, 18, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');

		$this->assertEquals($zmanim->getAlos72()->format('Y-m-d\TH:i:sP'), "2019-02-18T05:33:13-05:00");

		$zmanim->addDays(3);
		$this->assertEquals($zmanim->getAlos72()->format('Y-m-d\TH:i:sP'), "2019-02-21T05:29:09-05:00");

		$zmanim->subDays(3);
		$this->assertEquals($zmanim->getAlos72()->format('Y-m-d\TH:i:sP'), "2019-02-18T05:33:13-05:00");

		$zmanim->setDate(2017, 10, 17);
		$this->assertEquals($zmanim->getTzais19Point8Degrees()->format('Y-m-d\TH:i:sP'), "2017-10-17T19:53:34-04:00");
	}

	/** 
	 * @test
	 */
	public function testGetZmanHelper() {
		$zmanim = Zmanim::create(2019, 2, 18, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');

		$this->assertEquals($zmanim->get("Alos72")->format('Y-m-d\TH:i:sP'), "2019-02-18T05:33:13-05:00");
		$this->assertNull($zmanim->get("InvalidName"));
	}
}