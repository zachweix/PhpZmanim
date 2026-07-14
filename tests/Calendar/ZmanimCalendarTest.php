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
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calendar\ComplexZmanimCalendar;
use PhpZmanim\Calendar\ZmanimCalendar;
use PhpZmanim\GeoLocation;
use PhpZmanim\Zmanim;

class ZmanimCalendarTest extends TestCase
{

	protected $geo;
	protected $zenith;

	protected function setUp(): void
	{
		parent::setUp();

		/*
		 * Setup some basic data for our tests
		 */

		$this->geo = GeoLocation::create(40.0721087, -74.2400243, 15, 'America/New_York', 'Lakewood, NJ');
	}

	#[Test]
	public function tzais(): void
	{
		$zmanimCalendar = new ZmanimCalendar(2017, 10, 17, $this->geo);

		$tzais = $zmanimCalendar->getTzais();
		$this->assertEquals($tzais->format('Y-m-d\TH:i:sP'), "2017-10-17T18:54:29-04:00");
	}

	#[Test]
	public function tzaisWithCustomDegreeOffset(): void
	{
		$czc = new ComplexZmanimCalendar(2017, 10, 17, $this->geo);

		$tzais = $czc->getTzais19Point8Degrees();
		$this->assertEquals($tzais->format('Y-m-d\TH:i:sP'), "2017-10-17T19:53:34-04:00");
	}

	#[Test]
	public function useWrapperClass(): void
	{
		$zmanim = Zmanim::create(2017, 10, 17, 40.0721087, -74.2400243, 15, 'America/New_York');

		$tzais = $zmanim->getTzais19Point8Degrees();
		$this->assertEquals($tzais->format('Y-m-d\TH:i:sP'), "2017-10-17T19:53:34-04:00");
	}

	#[Test]
	public function basicTimes(): void
	{
		$zmanim = Zmanim::create(2019, 2, 22, 40.0721087, -74.2400243, 39.57, 'America/New_York');

		$zmanim->setUseElevation(false);

		$this->assertEquals($zmanim->getAlos96()->format('Y-m-d\TH:i:sP'), "2019-02-22T05:04:50-05:00");
		$this->assertEquals($zmanim->getAlos72()->format('Y-m-d\TH:i:sP'), "2019-02-22T05:28:50-05:00");
		$this->assertEquals($zmanim->getCandleLighting()->format('Y-m-d\TH:i:sP'), "2019-02-22T17:22:38-05:00");
		$this->assertEquals($zmanim->getTzais72()->format('Y-m-d\TH:i:sP'), "2019-02-22T18:52:38-05:00");
	}

	#[Test]
	public function baalHatanya(): void
	{
		$zmanim = Zmanim::create(2019, 2, 18, 40.0721087, -74.2400243, 39.57, 'America/New_York');

		$this->assertEquals($zmanim->getMinchaGedolaBaalHatanya()->format('Y-m-d\TH:i:sP'), "2019-02-18T12:38:34-05:00");
		$this->assertEquals($zmanim->getPlagHaminchaBaalHatanya()->format('Y-m-d\TH:i:sP'), "2019-02-18T16:31:32-05:00");
		$this->assertEquals($zmanim->getTzaisBaalHatanya()->format('Y-m-d\TH:i:sP'), "2019-02-18T18:03:40-05:00");
	}

	#[Test]
	public function changingDate(): void
	{
		$zmanim = Zmanim::create(2019, 2, 18, 40.0721087, -74.2400243, 39.57, 'America/New_York');

		$this->assertEquals($zmanim->getAlos72()->format('Y-m-d\TH:i:sP'), "2019-02-18T05:33:13-05:00");

		$zmanim->addDays(3);
		$this->assertEquals($zmanim->getAlos72()->format('Y-m-d\TH:i:sP'), "2019-02-21T05:29:09-05:00");

		$zmanim->subDays(3);
		$this->assertEquals($zmanim->getAlos72()->format('Y-m-d\TH:i:sP'), "2019-02-18T05:33:13-05:00");

		$zmanim->setDate(2017, 10, 17);
		$this->assertEquals($zmanim->getTzais19Point8Degrees()->format('Y-m-d\TH:i:sP'), "2017-10-17T19:53:34-04:00");
	}

	#[Test]
	public function all(): void
	{
		// Note that some of these may be wrong, I assume they're working, but please test them against another library to confirm

		$zmanim = Zmanim::create(2023, 9, 29, 40.0721087, -74.2400243, 39.57, 'America/New_York');

		$this->assertEquals($zmanim->getSunrise()->format('Y-m-d\TH:i:sP'), "2023-09-29T06:50:03-04:00");
		$this->assertEquals($zmanim->getSeaLevelSunrise()->format('Y-m-d\TH:i:sP'), "2023-09-29T06:51:07-04:00");
		$this->assertEquals($zmanim->getBeginCivilTwilight()->format('Y-m-d\TH:i:sP'), "2023-09-29T06:24:05-04:00");
		$this->assertEquals($zmanim->getBeginNauticalTwilight()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:52:35-04:00");
		$this->assertEquals($zmanim->getBeginAstronomicalTwilight()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:20:44-04:00");
		$this->assertEquals($zmanim->getTzais()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:22:54-04:00");
		$this->assertEquals($zmanim->getAlosHashachar()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:30:52-04:00");
		$this->assertEquals($zmanim->getAlos72()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:38:03-04:00");
		$this->assertEquals($zmanim->getChatzos()->format('Y-m-d\TH:i:sP'), "2023-09-29T12:47:28-04:00");
		$this->assertEquals($zmanim->getChatzosAsHalfDay()->format('Y-m-d\TH:i:sP'), "2023-09-29T12:46:59-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaGRA()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:48:31-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:12:31-04:00");
		$this->assertEquals($zmanim->getTzais72()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:55:54-04:00");
		$this->assertEquals($zmanim->getCandleLighting()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:24:51-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaGRA()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:48:00-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:24:00-04:00");
		$this->assertEquals($zmanim->getMinchaGedola()->format('Y-m-d\TH:i:sP'), "2023-09-29T13:16:43-04:00");
		$this->assertEquals($zmanim->getMinchaKetana()->format('Y-m-d\TH:i:sP'), "2023-09-29T16:15:11-04:00");
		$this->assertEquals($zmanim->getPlagHamincha()->format('Y-m-d\TH:i:sP'), "2023-09-29T17:29:33-04:00");
		$this->assertEquals(round($zmanim->getShaahZmanis19Point8Degrees(), 7), 4558102.2461667);
		$this->assertEquals(round($zmanim->getShaahZmanis18Degrees(), 7), 4461488.6310833);
		$this->assertEquals(round($zmanim->getShaahZmanis26Degrees(), 7), 4897305.4895833);
		$this->assertEquals(round($zmanim->getShaahZmanis16Point1Degrees(), 7), 4360175.11775);
		$this->assertEquals(round($zmanim->getShaahZmanis60Minutes(), 7), 4169282.8666667);
		$this->assertEquals(round($zmanim->getShaahZmanis72Minutes(), 7), 4289282.8666667);
		$this->assertEquals(round($zmanim->getShaahZmanis72MinutesZmanis(), 7), 4283139.44);
		$this->assertEquals(round($zmanim->getShaahZmanis90Minutes(), 7), 4469282.8666667);
		$this->assertEquals(round($zmanim->getShaahZmanis90MinutesZmanis(), 7), 4461603.5833333);
		$this->assertEquals(round($zmanim->getShaahZmanis96MinutesZmanis(), 7), 4521091.6311667);
		$this->assertEquals(round($zmanim->getShaahZmanisAteretTorah(), 7), 4126211.1533333);
		$this->assertEquals(round($zmanim->getShaahZmanisAlos16Point1ToTzais3Point8(), 7), 4037361.2094167);
		$this->assertEquals(round($zmanim->getShaahZmanisAlos16Point1ToTzais3Point7(), 7), 4034750.58175);
		$this->assertEquals(round($zmanim->getShaahZmanis96Minutes(), 7), 4529282.8666667);
		$this->assertEquals(round($zmanim->getShaahZmanis120Minutes(), 7), 4769282.8666667);
		$this->assertEquals(round($zmanim->getShaahZmanis120MinutesZmanis(), 7), 4759043.82225);
		$this->assertEquals($zmanim->getPlagHamincha120MinutesZmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:03:44-04:00");
		$this->assertEquals($zmanim->getPlagHamincha120Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:04:33-04:00");
		$this->assertEquals($zmanim->getAlos60()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:50:03-04:00");
		$this->assertEquals($zmanim->getAlos72Zmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:38:40-04:00");
		$this->assertEquals($zmanim->getAlos96()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:14:03-04:00");
		$this->assertEquals($zmanim->getAlos90Zmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:20:49-04:00");
		$this->assertEquals($zmanim->getAlos96Zmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:14:52-04:00");
		$this->assertEquals($zmanim->getAlos90()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:20:03-04:00");
		$this->assertEquals($zmanim->getAlos120()->format('Y-m-d\TH:i:sP'), "2023-09-29T04:50:03-04:00");
		$this->assertEquals($zmanim->getAlos120Zmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T04:51:04-04:00");
		$this->assertEquals($zmanim->getAlos26Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T04:37:04-04:00");
		$this->assertEquals($zmanim->getAlos18Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:20:44-04:00");
		$this->assertEquals($zmanim->getAlos19Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:15:22-04:00");
		$this->assertEquals($zmanim->getAlos19Point8Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:11:03-04:00");
		$this->assertEquals($zmanim->getAlos16Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:30:52-04:00");
		$this->assertEquals($zmanim->getMisheyakir11Point5Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:55:13-04:00");
		$this->assertEquals($zmanim->getMisheyakir11Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:57:51-04:00");
		$this->assertEquals($zmanim->getMisheyakir10Point2Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T06:02:04-04:00");
		$this->assertEquals($zmanim->getMisheyakir7Point65Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T06:15:26-04:00");
		$this->assertEquals($zmanim->getMisheyakir9Point5Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T06:05:44-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA19Point8Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T08:58:57-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA16Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:08:53-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA18Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:03:48-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA72Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:12:31-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA72MinutesZmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:12:49-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA90Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:03:31-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA90MinutesZmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:03:54-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA96Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:00:31-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA96MinutesZmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:00:55-04:00");
		$this->assertEquals($zmanim->getSofZmanShma3HoursBeforeChatzos()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:47:28-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA120Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T08:48:31-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaAlos16Point1ToSunset()->format('Y-m-d\TH:i:sP'), "2023-09-29T08:49:08-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaAlos16Point1ToTzaisGeonim7Point083Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T08:57:02-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaKolEliyahu()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:53:30-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA19Point8Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:14:55-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA16Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:21:33-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA18Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:18:10-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA72Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:24:00-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA72MinutesZmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:24:12-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA90Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:18:00-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA90MinutesZmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:18:16-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA96Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:16:00-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA96MinutesZmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:16:17-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaMGA120Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:08:00-04:00");
		$this->assertEquals($zmanim->getSofZmanTfila2HoursBeforeChatzos()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:47:28-04:00");
		$this->assertEquals($zmanim->getMinchaGedola30Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T13:17:28-04:00");
		$this->assertEquals($zmanim->getMinchaGedola72Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T13:22:43-04:00");
		$this->assertEquals($zmanim->getMinchaGedola16Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T13:23:14-04:00");
		$this->assertEquals($zmanim->getMinchaGedolaAhavatShalom()->format('Y-m-d\TH:i:sP'), "2023-09-29T13:21:06-04:00");
		$this->assertEquals($zmanim->getMinchaGedolaGreaterThan30()->format('Y-m-d\TH:i:sP'), "2023-09-29T13:17:28-04:00");
		$this->assertEquals($zmanim->getMinchaKetana16Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T17:01:14-04:00");
		$this->assertEquals($zmanim->getMinchaKetanaAhavatShalom()->format('Y-m-d\TH:i:sP'), "2023-09-29T16:10:07-04:00");
		$this->assertEquals($zmanim->getMinchaKetana72Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T16:57:11-04:00");
		$this->assertEquals($zmanim->getPlagHamincha60Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:17:03-04:00");
		$this->assertEquals($zmanim->getPlagHamincha72Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:26:33-04:00");
		$this->assertEquals($zmanim->getPlagHamincha90Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:40:48-04:00");
		$this->assertEquals($zmanim->getPlagHamincha96Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:45:33-04:00");
		$this->assertEquals($zmanim->getPlagHamincha96MinutesZmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:44:54-04:00");
		$this->assertEquals($zmanim->getPlagHamincha90MinutesZmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:40:11-04:00");
		$this->assertEquals($zmanim->getPlagHamincha72MinutesZmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:26:04-04:00");
		$this->assertEquals($zmanim->getPlagHamincha16Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:32:04-04:00");
		$this->assertEquals($zmanim->getPlagHamincha19Point8Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:47:43-04:00");
		$this->assertEquals($zmanim->getPlagHamincha26Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:14:30-04:00");
		$this->assertEquals($zmanim->getPlagHamincha18Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:40:05-04:00");
		$this->assertEquals($zmanim->getPlagAlosToSunset()->format('Y-m-d\TH:i:sP'), "2023-09-29T17:21:18-04:00");
		$this->assertEquals($zmanim->getPlagAlos16Point1ToTzaisGeonim7Point083Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T17:49:36-04:00");
		$this->assertEquals($zmanim->getPlagAhavatShalom()->format('Y-m-d\TH:i:sP'), "2023-09-29T17:34:14-04:00");
		$this->assertEquals($zmanim->getBainHashmashosRT13Point24Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:47:47-04:00");
		$this->assertEquals($zmanim->getBainHashmashosRT58Point5Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:42:24-04:00");
		$this->assertEquals($zmanim->getBainHashmashosRT13Point5MinutesBefore7Point083Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:02:00-04:00");
		$this->assertEquals($zmanim->getBainHashmashosRT2Stars()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:11:24-04:00");
		$this->assertEquals($zmanim->getBainHashmashosYereim18Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:25:54-04:00");
		$this->assertEquals($zmanim->getBainHashmashosYereim3Point05Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:22:31-04:00");
		$this->assertEquals($zmanim->getBainHashmashosYereim16Point875Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:27:02-04:00");
		$this->assertEquals($zmanim->getBainHashmashosYereim2Point8Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:23:50-04:00");
		$this->assertEquals($zmanim->getBainHashmashosYereim13Point5Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:30:24-04:00");
		$this->assertEquals($zmanim->getBainHashmashosYereim2Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:27:30-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim3Point7Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:57:49-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim3Point8Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:58:21-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim5Point95Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:09:34-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim3Point65Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:57:34-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim3Point676Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T18:57:42-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim4Point61Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:02:35-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim4Point37Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:01:19-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim5Point88Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:09:12-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim4Point8Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:03:34-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim6Point45Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:12:11-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim7Point083Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:15:30-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim7Point67Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:18:34-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim8Point5Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:22:54-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim9Point3Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:27:05-04:00");
		$this->assertEquals($zmanim->getTzaisGeonim9Point75Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:29:27-04:00");
		$this->assertEquals($zmanim->getTzais60()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:43:54-04:00");
		$this->assertEquals($zmanim->getTzaisAteretTorah()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:23:54-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaAteretTorah()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:04:59-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilahAteretTorah()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:13:45-04:00");
		$this->assertEquals($zmanim->getMinchaGedolaAteretTorah()->format('Y-m-d\TH:i:sP'), "2023-09-29T13:05:40-04:00");
		$this->assertEquals($zmanim->getMinchaKetanaAteretTorah()->format('Y-m-d\TH:i:sP'), "2023-09-29T16:31:59-04:00");
		$this->assertEquals($zmanim->getPlagHaminchaAteretTorah()->format('Y-m-d\TH:i:sP'), "2023-09-29T17:57:57-04:00");
		$this->assertEquals($zmanim->getTzais72Zmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:55:18-04:00");
		$this->assertEquals($zmanim->getTzais90Zmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T20:13:08-04:00");
		$this->assertEquals($zmanim->getTzais96Zmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T20:19:05-04:00");
		$this->assertEquals($zmanim->getTzais90()->format('Y-m-d\TH:i:sP'), "2023-09-29T20:13:54-04:00");
		$this->assertEquals($zmanim->getTzais120()->format('Y-m-d\TH:i:sP'), "2023-09-29T20:43:54-04:00");
		$this->assertEquals($zmanim->getTzais120Zmanis()->format('Y-m-d\TH:i:sP'), "2023-09-29T20:42:53-04:00");
		$this->assertEquals($zmanim->getTzais16Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T20:02:55-04:00");
		$this->assertEquals($zmanim->getTzais26Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T20:56:31-04:00");
		$this->assertEquals($zmanim->getTzais18Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T20:13:02-04:00");
		$this->assertEquals($zmanim->getTzais19Point8Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T20:22:40-04:00");
		$this->assertEquals($zmanim->getTzais96()->format('Y-m-d\TH:i:sP'), "2023-09-29T20:19:54-04:00");
		$this->assertEquals($zmanim->getFixedLocalChatzos()->format('Y-m-d\TH:i:sP'), "2023-09-29T12:56:57-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaFixedLocal()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:56:57-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaFixedLocal()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:56:57-04:00");
		$this->assertEquals($zmanim->getSofZmanKidushLevanaBetweenMoldos()->format('Y-m-d\TH:i:sP'), "2023-09-29T23:50:05+02:00");
		$this->assertEquals($zmanim->getSofZmanKidushLevana15Days()->format('Y-m-d\TH:i:sP'), "2023-09-30T05:28:03+02:00");

		$zmanim = Zmanim::create(2023, 9, 17, 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$this->assertEquals($zmanim->getTchilasZmanKidushLevana3Days()->format('Y-m-d\TH:i:sP'), "2023-09-18T05:28:03+02:00");

		$zmanim = Zmanim::create(2023, 9, 14, 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$this->assertEquals($zmanim->getZmanMolad()->format('Y-m-d\TH:i:sP'), "2023-09-14T23:28:03-04:00");

		$zmanim = Zmanim::create(2023, 9, 21, 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$this->assertEquals($zmanim->getTchilasZmanKidushLevana7Days()->format('Y-m-d\TH:i:sP'), "2023-09-22T05:28:03+02:00");

		$zmanim = Zmanim::create(2023, 9, 29, 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$this->assertEquals($zmanim->getSofZmanAchilasChametzGRA()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:48:00-04:00");
		$this->assertEquals($zmanim->getSofZmanAchilasChametzMGA72Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:24:00-04:00");
		$this->assertEquals($zmanim->getSofZmanAchilasChametzMGA16Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:21:33-04:00");
		$this->assertEquals($zmanim->getSofZmanBiurChametzGRA()->format('Y-m-d\TH:i:sP'), "2023-09-29T11:47:29-04:00");
		$this->assertEquals($zmanim->getSofZmanBiurChametzMGA72Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T11:35:29-04:00");
		$this->assertEquals($zmanim->getSofZmanBiurChametzMGA16Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T11:34:13-04:00");
		$this->assertEquals($zmanim->getShaahZmanisBaalHatanya(), 3597915.74075);
		$this->assertEquals($zmanim->getAlosBaalHatanya()->format('Y-m-d\TH:i:sP'), "2023-09-29T05:26:37-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaBaalHatanya()->format('Y-m-d\TH:i:sP'), "2023-09-29T09:47:05-04:00");
		$this->assertEquals($zmanim->getSofZmanTfilaBaalHatanya()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:47:03-04:00");
		$this->assertEquals($zmanim->getSofZmanAchilasChametzBaalHatanya()->format('Y-m-d\TH:i:sP'), "2023-09-29T10:47:03-04:00");
		$this->assertEquals($zmanim->getSofZmanBiurChametzBaalHatanya()->format('Y-m-d\TH:i:sP'), "2023-09-29T11:47:01-04:00");
		$this->assertEquals($zmanim->getMinchaGedolaBaalHatanya()->format('Y-m-d\TH:i:sP'), "2023-09-29T13:16:58-04:00");
		$this->assertEquals($zmanim->getMinchaGedolaBaalHatanyaGreaterThan30()->format('Y-m-d\TH:i:sP'), "2023-09-29T13:17:28-04:00");
		$this->assertEquals($zmanim->getMinchaKetanaBaalHatanya()->format('Y-m-d\TH:i:sP'), "2023-09-29T16:16:51-04:00");
		$this->assertEquals($zmanim->getPlagHaminchaBaalHatanya()->format('Y-m-d\TH:i:sP'), "2023-09-29T17:31:49-04:00");
		$this->assertEquals($zmanim->getTzaisBaalHatanya()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:09:50-04:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA18DegreesToFixedLocalChatzos()->format('Y-m-d\TH:i:sP'), "2024-03-05T14:12:51-05:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA16Point1DegreesToFixedLocalChatzos()->format('Y-m-d\TH:i:sP'), "2024-03-02T01:49:32-05:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA90MinutesToFixedLocalChatzos()->format('Y-m-d\TH:i:sP'), "2024-03-05T19:50:26-05:00");
		$this->assertEquals($zmanim->getSofZmanShmaMGA72MinutesToFixedLocalChatzos()->format('Y-m-d\TH:i:sP'), "2024-02-28T14:08:26-05:00");
		$this->assertEquals($zmanim->getSofZmanShmaGRASunriseToFixedLocalChatzos()->format('Y-m-d\TH:i:sP'), "2024-02-03T15:20:26-05:00");
		$this->assertEquals($zmanim->getSofZmanTfilaGRASunriseToFixedLocalChatzos()->format('Y-m-d\TH:i:sP'), "2024-03-17T03:30:33-04:00");
		$this->assertEquals($zmanim->getMinchaGedolaGRAFixedLocalChatzos30Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T13:26:57-04:00");
		$this->assertEquals($zmanim->getMinchaKetanaGRAFixedLocalChatzosToSunset()->format('Y-m-d\TH:i:sP'), "2024-02-17T01:07:51-05:00");
		$this->assertEquals($zmanim->getPlagHaminchaGRAFixedLocalChatzosToSunset()->format('Y-m-d\TH:i:sP'), "2024-04-07T06:50:18-04:00");
		$this->assertEquals($zmanim->getTzais50()->format('Y-m-d\TH:i:sP'), "2023-09-29T19:33:54-04:00");
		$this->assertEquals($zmanim->getSamuchLeMinchaKetanaGRA()->format('Y-m-d\TH:i:sP'), "2023-09-29T15:45:27-04:00");
		$this->assertEquals($zmanim->getSamuchLeMinchaKetana16Point1Degrees()->format('Y-m-d\TH:i:sP'), "2023-09-29T16:24:54-04:00");
		$this->assertEquals($zmanim->getSamuchLeMinchaKetana72Minutes()->format('Y-m-d\TH:i:sP'), "2023-09-29T16:21:27-04:00");
	}

	#[Test]
	public function isAssurBemlacha(): void
	{
		// Test Friday evening (Erev Shabbos)
		$zmanim = Zmanim::create(2024, 11, 8, 40.0721087, -74.2400243, 39.57, 'America/New_York');
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
		$zmanimShabbos = Zmanim::create(2024, 11, 9, 40.0721087, -74.2400243, 39.57, 'America/New_York');
		$sunsetShabbos = $zmanimShabbos->getSunset();
		$tzaisShabbos = $zmanimShabbos->getTzais();

		// During Shabbos day (before tzais) - should be Assur
		$middayShabbos = $tzaisShabbos->copy()->subHours(6);
		$this->assertTrue($zmanimShabbos->isAssurBemlacha($middayShabbos, $tzaisShabbos, false));

		// After tzais on Shabbos - should not be Assur (Shabbos ends)
		$afterTzaisShabbos = $tzaisShabbos->copy()->addMinutes(10);
		$this->assertFalse($zmanimShabbos->isAssurBemlacha($afterTzaisShabbos, $tzaisShabbos, false));

		// Test regular weekday (Thursday)
		$zmanimWeekday = Zmanim::create(2024, 11, 7, 40.0721087, -74.2400243, 39.57, 'America/New_York');
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
