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
		$startOfDay = $zmanim->get("SunriseOffsetByDegrees", 90);
		$endOfDay = $zmanim->get("SunsetOffsetByDegrees", 90);

		$this->assertEquals($zmanim->get("Alos72")->format('Y-m-d\TH:i:sP'), "2019-02-18T05:33:13-05:00");
		$this->assertEquals($zmanim->alos72->format('Y-m-d\TH:i:sP'), "2019-02-18T05:33:13-05:00");
		$this->assertEquals($zmanim->get("SofZmanShma", $startOfDay, $endOfDay)->format('Y-m-d\TH:i:sP'), "2019-02-18T09:28:11-05:00");
		// $this->assertEquals($zmanim->invalidName, null);

		$czc = new ComplexZmanimCalendar($this->geo, 2017, 10, 17);
		$this->assertEquals($czc->tzais19Point8Degrees->format('Y-m-d\TH:i:sP'), "2017-10-17T19:53:34-04:00");

		$this->expectException(\Exception::class);
		$zmanim->get("InvalidName");
	}

	/**
	 * @test
	 */
	public function testAll() {
		// Note that some of these may be wrong, I assume they're working, but please test them against another library to confirm

		$zmanim = Zmanim::create(2023, 9, 29, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');

		$this->assertEquals($zmanim->sunrise->format('Y-m-d\TH:i:sP'), "2023-09-29T06:50:03-04:00");
		$this->assertEquals($zmanim->seaLevelSunrise->format('Y-m-d\TH:i:sP'), "2023-09-29T06:51:07-04:00");
		$this->assertEquals($zmanim->beginCivilTwilight->format('Y-m-d\TH:i:sP'), "2023-09-29T06:24:05-04:00");
		$this->assertEquals($zmanim->beginNauticalTwilight->format('Y-m-d\TH:i:sP'), "2023-09-29T05:52:35-04:00");
		$this->assertEquals($zmanim->beginAstronomicalTwilight->format('Y-m-d\TH:i:sP'), "2023-09-29T05:20:44-04:00");
		$this->assertEquals($zmanim->tzais->format('Y-m-d\TH:i:sP'), "2023-09-29T19:22:54-04:00");
		$this->assertEquals($zmanim->alosHashachar->format('Y-m-d\TH:i:sP'), "2023-09-29T05:30:52-04:00");
		$this->assertEquals($zmanim->alos72->format('Y-m-d\TH:i:sP'), "2023-09-29T05:38:03-04:00");
		$this->assertEquals($zmanim->chatzos->format('Y-m-d\TH:i:sP'), "2023-09-29T12:47:28-04:00");
		$this->assertEquals($zmanim->chatzosAsHalfDay->format('Y-m-d\TH:i:sP'), "2023-09-29T12:46:59-04:00");
		$this->assertEquals($zmanim->sofZmanShmaGRA->format('Y-m-d\TH:i:sP'), "2023-09-29T09:48:31-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA->format('Y-m-d\TH:i:sP'), "2023-09-29T09:12:31-04:00");
		$this->assertEquals($zmanim->tzais72->format('Y-m-d\TH:i:sP'), "2023-09-29T19:55:54-04:00");
		$this->assertEquals($zmanim->candleLighting->format('Y-m-d\TH:i:sP'), "2023-09-29T18:24:51-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaGRA->format('Y-m-d\TH:i:sP'), "2023-09-29T10:48:00-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA->format('Y-m-d\TH:i:sP'), "2023-09-29T10:24:00-04:00");
		$this->assertEquals($zmanim->minchaGedola->format('Y-m-d\TH:i:sP'), "2023-09-29T13:16:43-04:00");
		$this->assertEquals($zmanim->minchaKetana->format('Y-m-d\TH:i:sP'), "2023-09-29T16:15:11-04:00");
		$this->assertEquals($zmanim->plagHamincha->format('Y-m-d\TH:i:sP'), "2023-09-29T17:29:33-04:00");
		$this->assertEquals(round($zmanim->shaahZmanis19Point8Degrees, 7), 4558102.2461667);
		$this->assertEquals(round($zmanim->shaahZmanis18Degrees, 7), 4461488.6310833);
		$this->assertEquals(round($zmanim->shaahZmanis26Degrees, 7), 4897305.4895833);
		$this->assertEquals(round($zmanim->shaahZmanis16Point1Degrees, 7), 4360175.11775);
		$this->assertEquals(round($zmanim->shaahZmanis60Minutes, 7), 4169282.8666667);
		$this->assertEquals(round($zmanim->shaahZmanis72Minutes, 7), 4289282.8666667);
		$this->assertEquals(round($zmanim->shaahZmanis72MinutesZmanis, 7), 4283139.44);
		$this->assertEquals(round($zmanim->shaahZmanis90Minutes, 7), 4469282.8666667);
		$this->assertEquals(round($zmanim->shaahZmanis90MinutesZmanis, 7), 4461603.5833333);
		$this->assertEquals(round($zmanim->shaahZmanis96MinutesZmanis, 7), 4521091.6311667);
		$this->assertEquals(round($zmanim->shaahZmanisAteretTorah, 7), 4126211.1533333);
		$this->assertEquals(round($zmanim->shaahZmanisAlos16Point1ToTzais3Point8, 7), 4037361.2094167);
		$this->assertEquals(round($zmanim->shaahZmanisAlos16Point1ToTzais3Point7, 7), 4034750.58175);
		$this->assertEquals(round($zmanim->shaahZmanis96Minutes, 7), 4529282.8666667);
		$this->assertEquals(round($zmanim->shaahZmanis120Minutes, 7), 4769282.8666667);
		$this->assertEquals(round($zmanim->shaahZmanis120MinutesZmanis, 7), 4759043.82225);
		$this->assertEquals($zmanim->plagHamincha120MinutesZmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T19:03:44-04:00");
		$this->assertEquals($zmanim->plagHamincha120Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T19:04:33-04:00");
		$this->assertEquals($zmanim->alos60->format('Y-m-d\TH:i:sP'), "2023-09-29T05:50:03-04:00");
		$this->assertEquals($zmanim->alos72Zmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T05:38:40-04:00");
		$this->assertEquals($zmanim->alos96->format('Y-m-d\TH:i:sP'), "2023-09-29T05:14:03-04:00");
		$this->assertEquals($zmanim->alos90Zmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T05:20:49-04:00");
		$this->assertEquals($zmanim->alos96Zmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T05:14:52-04:00");
		$this->assertEquals($zmanim->alos90->format('Y-m-d\TH:i:sP'), "2023-09-29T05:20:03-04:00");
		$this->assertEquals($zmanim->alos120->format('Y-m-d\TH:i:sP'), "2023-09-29T04:50:03-04:00");
		$this->assertEquals($zmanim->alos120Zmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T04:51:04-04:00");
		$this->assertEquals($zmanim->alos26Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T04:37:04-04:00");
		$this->assertEquals($zmanim->alos18Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T05:20:44-04:00");
		$this->assertEquals($zmanim->alos19Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T05:15:22-04:00");
		$this->assertEquals($zmanim->alos19Point8Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T05:11:03-04:00");
		$this->assertEquals($zmanim->alos16Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T05:30:52-04:00");
		$this->assertEquals($zmanim->misheyakir11Point5Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T05:55:13-04:00");
		$this->assertEquals($zmanim->misheyakir11Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T05:57:51-04:00");
		$this->assertEquals($zmanim->misheyakir10Point2Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T06:02:04-04:00");
		$this->assertEquals($zmanim->misheyakir7Point65Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T06:15:26-04:00");
		$this->assertEquals($zmanim->misheyakir9Point5Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T06:05:44-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA19Point8Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T08:58:57-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA16Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T09:08:53-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA18Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T09:03:48-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA72Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T09:12:31-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA72MinutesZmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T09:12:49-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA90Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T09:03:31-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA90MinutesZmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T09:03:54-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA96Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T09:00:31-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA96MinutesZmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T09:00:55-04:00");
		$this->assertEquals($zmanim->sofZmanShma3HoursBeforeChatzos->format('Y-m-d\TH:i:sP'), "2023-09-29T09:47:28-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA120Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T08:48:31-04:00");
		$this->assertEquals($zmanim->sofZmanShmaAlos16Point1ToSunset->format('Y-m-d\TH:i:sP'), "2023-09-29T08:49:08-04:00");
		$this->assertEquals($zmanim->sofZmanShmaAlos16Point1ToTzaisGeonim7Point083Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T08:57:02-04:00");
		$this->assertEquals($zmanim->sofZmanShmaKolEliyahu->format('Y-m-d\TH:i:sP'), "2023-09-29T09:53:30-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA19Point8Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T10:14:55-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA16Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T10:21:33-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA18Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T10:18:10-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA72Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T10:24:00-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA72MinutesZmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T10:24:12-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA90Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T10:18:00-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA90MinutesZmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T10:18:16-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA96Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T10:16:00-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA96MinutesZmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T10:16:17-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaMGA120Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T10:08:00-04:00");
		$this->assertEquals($zmanim->sofZmanTfila2HoursBeforeChatzos->format('Y-m-d\TH:i:sP'), "2023-09-29T10:47:28-04:00");
		$this->assertEquals($zmanim->minchaGedola30Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T13:17:28-04:00");
		$this->assertEquals($zmanim->minchaGedola72Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T13:22:43-04:00");
		$this->assertEquals($zmanim->minchaGedola16Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T13:23:14-04:00");
		$this->assertEquals($zmanim->minchaGedolaAhavatShalom->format('Y-m-d\TH:i:sP'), "2023-09-29T13:21:06-04:00");
		$this->assertEquals($zmanim->minchaGedolaGreaterThan30->format('Y-m-d\TH:i:sP'), "2023-09-29T13:17:28-04:00");
		$this->assertEquals($zmanim->minchaKetana16Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T17:01:14-04:00");
		$this->assertEquals($zmanim->minchaKetanaAhavatShalom->format('Y-m-d\TH:i:sP'), "2023-09-29T16:10:07-04:00");
		$this->assertEquals($zmanim->minchaKetana72Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T16:57:11-04:00");
		$this->assertEquals($zmanim->plagHamincha60Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T18:17:03-04:00");
		$this->assertEquals($zmanim->plagHamincha72Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T18:26:33-04:00");
		$this->assertEquals($zmanim->plagHamincha90Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T18:40:48-04:00");
		$this->assertEquals($zmanim->plagHamincha96Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T18:45:33-04:00");
		$this->assertEquals($zmanim->plagHamincha96MinutesZmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T18:44:54-04:00");
		$this->assertEquals($zmanim->plagHamincha90MinutesZmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T18:40:11-04:00");
		$this->assertEquals($zmanim->plagHamincha72MinutesZmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T18:26:04-04:00");
		$this->assertEquals($zmanim->plagHamincha16Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T18:32:04-04:00");
		$this->assertEquals($zmanim->plagHamincha19Point8Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T18:47:43-04:00");
		$this->assertEquals($zmanim->plagHamincha26Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:14:30-04:00");
		$this->assertEquals($zmanim->plagHamincha18Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T18:40:05-04:00");
		$this->assertEquals($zmanim->plagAlosToSunset->format('Y-m-d\TH:i:sP'), "2023-09-29T17:21:18-04:00");
		$this->assertEquals($zmanim->plagAlos16Point1ToTzaisGeonim7Point083Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T17:49:36-04:00");
		$this->assertEquals($zmanim->plagAhavatShalom->format('Y-m-d\TH:i:sP'), "2023-09-29T17:34:14-04:00");
		$this->assertEquals($zmanim->bainHashmashosRT13Point24Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:47:47-04:00");
		$this->assertEquals($zmanim->bainHashmashosRT58Point5Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T19:42:24-04:00");
		$this->assertEquals($zmanim->bainHashmashosRT13Point5MinutesBefore7Point083Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:02:00-04:00");
		$this->assertEquals($zmanim->bainHashmashosRT2Stars->format('Y-m-d\TH:i:sP'), "2023-09-29T19:11:24-04:00");
		$this->assertEquals($zmanim->bainHashmashosYereim18Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T18:25:54-04:00");
		$this->assertEquals($zmanim->bainHashmashosYereim3Point05Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T18:22:31-04:00");
		$this->assertEquals($zmanim->bainHashmashosYereim16Point875Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T18:27:02-04:00");
		$this->assertEquals($zmanim->bainHashmashosYereim2Point8Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T18:23:50-04:00");
		$this->assertEquals($zmanim->bainHashmashosYereim13Point5Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T18:30:24-04:00");
		$this->assertEquals($zmanim->bainHashmashosYereim2Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T18:27:30-04:00");
		$this->assertEquals($zmanim->tzaisGeonim3Point7Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T18:57:49-04:00");
		$this->assertEquals($zmanim->tzaisGeonim3Point8Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T18:58:21-04:00");
		$this->assertEquals($zmanim->tzaisGeonim5Point95Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:09:34-04:00");
		$this->assertEquals($zmanim->tzaisGeonim3Point65Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T18:57:34-04:00");
		$this->assertEquals($zmanim->tzaisGeonim3Point676Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T18:57:42-04:00");
		$this->assertEquals($zmanim->tzaisGeonim4Point61Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:02:35-04:00");
		$this->assertEquals($zmanim->tzaisGeonim4Point37Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:01:19-04:00");
		$this->assertEquals($zmanim->tzaisGeonim5Point88Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:09:12-04:00");
		$this->assertEquals($zmanim->tzaisGeonim4Point8Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:03:34-04:00");
		$this->assertEquals($zmanim->tzaisGeonim6Point45Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:12:11-04:00");
		$this->assertEquals($zmanim->tzaisGeonim7Point083Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:15:30-04:00");
		$this->assertEquals($zmanim->tzaisGeonim7Point67Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:18:34-04:00");
		$this->assertEquals($zmanim->tzaisGeonim8Point5Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:22:54-04:00");
		$this->assertEquals($zmanim->tzaisGeonim9Point3Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:27:05-04:00");
		$this->assertEquals($zmanim->tzaisGeonim9Point75Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T19:29:27-04:00");
		$this->assertEquals($zmanim->tzais60->format('Y-m-d\TH:i:sP'), "2023-09-29T19:43:54-04:00");
		$this->assertEquals($zmanim->tzaisAteretTorah->format('Y-m-d\TH:i:sP'), "2023-09-29T19:23:54-04:00");
		$this->assertEquals($zmanim->sofZmanShmaAteretTorah->format('Y-m-d\TH:i:sP'), "2023-09-29T09:04:59-04:00");
		$this->assertEquals($zmanim->sofZmanTfilahAteretTorah->format('Y-m-d\TH:i:sP'), "2023-09-29T10:13:45-04:00");
		$this->assertEquals($zmanim->minchaGedolaAteretTorah->format('Y-m-d\TH:i:sP'), "2023-09-29T13:05:40-04:00");
		$this->assertEquals($zmanim->minchaKetanaAteretTorah->format('Y-m-d\TH:i:sP'), "2023-09-29T16:31:59-04:00");
		$this->assertEquals($zmanim->plagHaminchaAteretTorah->format('Y-m-d\TH:i:sP'), "2023-09-29T17:57:57-04:00");
		$this->assertEquals($zmanim->tzais72Zmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T19:55:18-04:00");
		$this->assertEquals($zmanim->tzais90Zmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T20:13:08-04:00");
		$this->assertEquals($zmanim->tzais96Zmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T20:19:05-04:00");
		$this->assertEquals($zmanim->tzais90->format('Y-m-d\TH:i:sP'), "2023-09-29T20:13:54-04:00");
		$this->assertEquals($zmanim->tzais120->format('Y-m-d\TH:i:sP'), "2023-09-29T20:43:54-04:00");
		$this->assertEquals($zmanim->tzais120Zmanis->format('Y-m-d\TH:i:sP'), "2023-09-29T20:42:53-04:00");
		$this->assertEquals($zmanim->tzais16Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T20:02:55-04:00");
		$this->assertEquals($zmanim->tzais26Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T20:56:31-04:00");
		$this->assertEquals($zmanim->tzais18Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T20:13:02-04:00");
		$this->assertEquals($zmanim->tzais19Point8Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T20:22:40-04:00");
		$this->assertEquals($zmanim->tzais96->format('Y-m-d\TH:i:sP'), "2023-09-29T20:19:54-04:00");
		$this->assertEquals($zmanim->fixedLocalChatzos->format('Y-m-d\TH:i:sP'), "2023-09-29T12:56:57-04:00");
		$this->assertEquals($zmanim->sofZmanShmaFixedLocal->format('Y-m-d\TH:i:sP'), "2023-09-29T09:56:57-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaFixedLocal->format('Y-m-d\TH:i:sP'), "2023-09-29T10:56:57-04:00");
		$this->assertEquals($zmanim->sofZmanKidushLevanaBetweenMoldos->format('Y-m-d\TH:i:sP'), "2023-09-29T23:50:05+02:00");
		$this->assertEquals($zmanim->sofZmanKidushLevana15Days->format('Y-m-d\TH:i:sP'), "2023-09-30T05:28:03+02:00");

		$zmanim = Zmanim::create(2023, 9, 17, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$this->assertEquals($zmanim->tchilasZmanKidushLevana3Days->format('Y-m-d\TH:i:sP'), "2023-09-18T05:28:03+02:00");

		$zmanim = Zmanim::create(2023, 9, 14, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$this->assertEquals($zmanim->zmanMolad->format('Y-m-d\TH:i:sP'), "2023-09-14T23:28:03-04:00");

		$zmanim = Zmanim::create(2023, 9, 21, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$this->assertEquals($zmanim->tchilasZmanKidushLevana7Days->format('Y-m-d\TH:i:sP'), "2023-09-22T05:28:03+02:00");

		$zmanim = Zmanim::create(2023, 9, 29, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$this->assertEquals($zmanim->sofZmanAchilasChametzGRA->format('Y-m-d\TH:i:sP'), "2023-09-29T10:48:00-04:00");
		$this->assertEquals($zmanim->sofZmanAchilasChametzMGA72Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T10:24:00-04:00");
		$this->assertEquals($zmanim->sofZmanAchilasChametzMGA16Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T10:21:33-04:00");
		$this->assertEquals($zmanim->sofZmanBiurChametzGRA->format('Y-m-d\TH:i:sP'), "2023-09-29T11:47:29-04:00");
		$this->assertEquals($zmanim->sofZmanBiurChametzMGA72Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T11:35:29-04:00");
		$this->assertEquals($zmanim->sofZmanBiurChametzMGA16Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T11:34:13-04:00");
		$this->assertEquals($zmanim->shaahZmanisBaalHatanya, 3597915.74075);
		$this->assertEquals($zmanim->alosBaalHatanya->format('Y-m-d\TH:i:sP'), "2023-09-29T05:26:37-04:00");
		$this->assertEquals($zmanim->sofZmanShmaBaalHatanya->format('Y-m-d\TH:i:sP'), "2023-09-29T09:47:05-04:00");
		$this->assertEquals($zmanim->sofZmanTfilaBaalHatanya->format('Y-m-d\TH:i:sP'), "2023-09-29T10:47:03-04:00");
		$this->assertEquals($zmanim->sofZmanAchilasChametzBaalHatanya->format('Y-m-d\TH:i:sP'), "2023-09-29T10:47:03-04:00");
		$this->assertEquals($zmanim->sofZmanBiurChametzBaalHatanya->format('Y-m-d\TH:i:sP'), "2023-09-29T11:47:01-04:00");
		$this->assertEquals($zmanim->minchaGedolaBaalHatanya->format('Y-m-d\TH:i:sP'), "2023-09-29T13:16:58-04:00");
		$this->assertEquals($zmanim->minchaGedolaBaalHatanyaGreaterThan30->format('Y-m-d\TH:i:sP'), "2023-09-29T13:17:28-04:00");
		$this->assertEquals($zmanim->minchaKetanaBaalHatanya->format('Y-m-d\TH:i:sP'), "2023-09-29T16:16:51-04:00");
		$this->assertEquals($zmanim->plagHaminchaBaalHatanya->format('Y-m-d\TH:i:sP'), "2023-09-29T17:31:49-04:00");
		$this->assertEquals($zmanim->tzaisBaalHatanya->format('Y-m-d\TH:i:sP'), "2023-09-29T19:09:50-04:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA18DegreesToFixedLocalChatzos->format('Y-m-d\TH:i:sP'), "2024-03-05T14:12:51-05:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA16Point1DegreesToFixedLocalChatzos->format('Y-m-d\TH:i:sP'), "2024-03-02T01:49:32-05:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA90MinutesToFixedLocalChatzos->format('Y-m-d\TH:i:sP'), "2024-03-05T19:50:26-05:00");
		$this->assertEquals($zmanim->sofZmanShmaMGA72MinutesToFixedLocalChatzos->format('Y-m-d\TH:i:sP'), "2024-02-28T14:08:26-05:00");
		$this->assertEquals($zmanim->sofZmanShmaGRASunriseToFixedLocalChatzos->format('Y-m-d\TH:i:sP'), "2024-02-03T15:20:26-05:00");
		$this->assertEquals($zmanim->sofZmanTfilaGRASunriseToFixedLocalChatzos->format('Y-m-d\TH:i:sP'), "2024-03-17T03:30:33-04:00");
		$this->assertEquals($zmanim->minchaGedolaGRAFixedLocalChatzos30Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T13:26:57-04:00");
		$this->assertEquals($zmanim->minchaKetanaGRAFixedLocalChatzosToSunset->format('Y-m-d\TH:i:sP'), "2024-02-17T01:07:51-05:00");
		$this->assertEquals($zmanim->plagHaminchaGRAFixedLocalChatzosToSunset->format('Y-m-d\TH:i:sP'), "2024-04-07T06:50:18-04:00");
		$this->assertEquals($zmanim->tzais50->format('Y-m-d\TH:i:sP'), "2023-09-29T19:33:54-04:00");
		$this->assertEquals($zmanim->samuchLeMinchaKetanaGRA->format('Y-m-d\TH:i:sP'), "2023-09-29T15:45:27-04:00");
		$this->assertEquals($zmanim->samuchLeMinchaKetana16Point1Degrees->format('Y-m-d\TH:i:sP'), "2023-09-29T16:24:54-04:00");
		$this->assertEquals($zmanim->samuchLeMinchaKetana72Minutes->format('Y-m-d\TH:i:sP'), "2023-09-29T16:21:27-04:00");
	}

	/**
	 * @test
	 */
	public function testIsAssurBemlacha() {
		// Test Friday evening (Erev Shabbos)
		$zmanim = Zmanim::create(2024, 11, 8, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$sunset = $zmanim->getSunset();
		$tzais = $zmanim->getTzais();

		// Before sunset on Friday - should not be Assur
		$beforeSunset = $sunset->copy()->subMinutes(30);
		$this->assertFalse($zmanim->isAssurBemlacha($beforeSunset, $tzais, false));

		// After sunset on Friday - should be Assur (Shabbos begins)
		$afterSunset = $sunset->copy()->addMinutes(10);
		$this->assertTrue($zmanim->isAssurBemlacha($afterSunset, $tzais, false));

		// After tzais on Friday - should still be Assur (during Shabbos)
		$afterTzais = $tzais->copy()->addMinutes(10);
		$this->assertTrue($zmanim->isAssurBemlacha($afterTzais, $tzais, false));

		// Test Shabbos day
		$zmanimShabbos = Zmanim::create(2024, 11, 9, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$sunsetShabbos = $zmanimShabbos->getSunset();
		$tzaisShabbos = $zmanimShabbos->getTzais();

		// During Shabbos day (before tzais) - should be Assur
		$middayShabbos = $tzaisShabbos->copy()->subHours(6);
		$this->assertTrue($zmanimShabbos->isAssurBemlacha($middayShabbos, $tzaisShabbos, false));

		// After tzais on Shabbos - should not be Assur (Shabbos ends)
		$afterTzaisShabbos = $tzaisShabbos->copy()->addMinutes(10);
		$this->assertFalse($zmanimShabbos->isAssurBemlacha($afterTzaisShabbos, $tzaisShabbos, false));

		// Test regular weekday (Thursday)
		$zmanimWeekday = Zmanim::create(2024, 11, 7, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$sunsetWeekday = $zmanimWeekday->getSunset();
		$tzaisWeekday = $zmanimWeekday->getTzais();

		// Before tzais on weekday - should not be Assur
		$beforeTzaisWeekday = $tzaisWeekday->copy()->subMinutes(30);
		$this->assertFalse($zmanimWeekday->isAssurBemlacha($beforeTzaisWeekday, $tzaisWeekday, false));

		// After tzais on weekday - should not be Assur
		$afterTzaisWeekday = $tzaisWeekday->copy()->addMinutes(10);
		$this->assertFalse($zmanimWeekday->isAssurBemlacha($afterTzaisWeekday, $tzaisWeekday, false));

		// Test Israel vs Diaspora (should behave the same for Shabbos)
		$this->assertTrue($zmanim->isAssurBemlacha($afterSunset, $tzais, true));
		$this->assertTrue($zmanim->isAssurBemlacha($afterSunset, $tzais, false));
	}
}
