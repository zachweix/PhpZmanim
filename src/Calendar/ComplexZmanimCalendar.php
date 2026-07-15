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

namespace PhpZmanim\Calendar;

use Carbon\Carbon;
use PhpZmanim\GeoLocation;
use PhpZmanim\HebrewCalendar\JewishCalendar;

/**
 * See https://github.com/KosherJava/zmanim/blob/master/src/net/sourceforge/zmanim/ComplexZmanimCalendar.java
 * for more detailed explanations regarding the methods and variables on this page.
 */
class ComplexZmanimCalendar extends ZmanimCalendar
{
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private int $ateretTorahSunsetOffset = 40;

	const ZENITH_3_POINT_7 =   AstronomicalCalendar::GEOMETRIC_ZENITH + 3.7;
	const ZENITH_3_POINT_8 =   AstronomicalCalendar::GEOMETRIC_ZENITH + 3.8;
	const ZENITH_5_POINT_95 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 5.95;
	const ZENITH_7_POINT_083 = AstronomicalCalendar::GEOMETRIC_ZENITH + 7 + (5.0 / 60);
	const ZENITH_10_POINT_2 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 10.2;
	const ZENITH_11_DEGREES =  AstronomicalCalendar::GEOMETRIC_ZENITH + 11;
	const ZENITH_11_POINT_5 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 11.5;
	const ZENITH_13_POINT_24 = AstronomicalCalendar::GEOMETRIC_ZENITH + 13.24;
	const ZENITH_19_DEGREES =  AstronomicalCalendar::GEOMETRIC_ZENITH + 19;
	const ZENITH_19_POINT_8 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 19.8;
	const ZENITH_26_DEGREES =  AstronomicalCalendar::GEOMETRIC_ZENITH + 26.0;
	const ZENITH_4_POINT_37 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 4.37;
	const ZENITH_4_POINT_61 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 4.61;
	const ZENITH_4_POINT_8 =   AstronomicalCalendar::GEOMETRIC_ZENITH + 4.8;
	const ZENITH_3_POINT_65 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 3.65;
	const ZENITH_3_POINT_676 = AstronomicalCalendar::GEOMETRIC_ZENITH + 3.676;
	const ZENITH_5_POINT_88 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 5.88;
	const ZENITH_1_POINT_583 = AstronomicalCalendar::GEOMETRIC_ZENITH + 1.583;
	const ZENITH_16_POINT_9 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 16.9;
	const ZENITH_6_DEGREES =   AstronomicalCalendar::GEOMETRIC_ZENITH + 6;
	const ZENITH_6_POINT_45 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 6.45;
	const ZENITH_7_POINT_65 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 7.65;
	const ZENITH_7_POINT_67 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 7.67;
	const ZENITH_9_POINT_3 =   AstronomicalCalendar::GEOMETRIC_ZENITH + 9.3;
	const ZENITH_9_POINT_5 =   AstronomicalCalendar::GEOMETRIC_ZENITH + 9.5;
	const ZENITH_9_POINT_75 =  AstronomicalCalendar::GEOMETRIC_ZENITH + 9.75;

	const ZENITH_MINUS_2_POINT_1  = AstronomicalCalendar::GEOMETRIC_ZENITH - 2.1;
	const ZENITH_MINUS_2_POINT_8  = AstronomicalCalendar::GEOMETRIC_ZENITH - 2.8;
	const ZENITH_MINUS_3_POINT_05 = AstronomicalCalendar::GEOMETRIC_ZENITH - 3.05;

	/*
	|--------------------------------------------------------------------------
	| SHAAH ZMANIS
	|--------------------------------------------------------------------------
	*/

	public function getShaahZmanis19Point8Degrees(): float
	{
		return $this->getTemporalHour($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees());
	}

	public function getShaahZmanis18Degrees(): float
	{
		return $this->getTemporalHour($this->getAlos18Degrees(), $this->getTzais18Degrees());
	}

	public function getShaahZmanis26Degrees(): float
	{
		return $this->getTemporalHour($this->getAlos26Degrees(), $this->getTzais26Degrees());
	}

	public function getShaahZmanis16Point1Degrees(): float
	{
		return $this->getTemporalHour($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getShaahZmanis60Minutes(): float
	{
		return $this->getTemporalHour($this->getAlos60(), $this->getTzais60());
	}

	public function getShaahZmanis72Minutes(): float
	{
		return $this->getShaahZmanisMGA();
	}

	public function getShaahZmanis72MinutesZmanis(): float
	{
		return $this->getTemporalHour($this->getAlos72Zmanis(), $this->getTzais72Zmanis());
	}

	public function getShaahZmanis90Minutes(): float
	{
		return $this->getTemporalHour($this->getAlos90(), $this->getTzais90());
	}

	public function getShaahZmanis90MinutesZmanis(): float
	{
		return $this->getTemporalHour($this->getAlos90Zmanis(), $this->getTzais90Zmanis());
	}

	public function getShaahZmanis96MinutesZmanis(): float
	{
		return $this->getTemporalHour($this->getAlos96Zmanis(), $this->getTzais96Zmanis());
	}

	public function getShaahZmanisAteretTorah(): float
	{
		return $this->getTemporalHour($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getShaahZmanisAlos16Point1ToTzais3Point8(): float
	{
		return $this->getTemporalHour($this->getAlos16Point1Degrees(), $this->getTzaisGeonim3Point8Degrees());
	}

	public function getShaahZmanisAlos16Point1ToTzais3Point7(): float
	{
		return $this->getTemporalHour($this->getAlos16Point1Degrees(), $this->getTzaisGeonim3Point7Degrees());
	}

	public function getShaahZmanis96Minutes(): float
	{
		return $this->getTemporalHour($this->getAlos96(), $this->getTzais96());
	}

	public function getShaahZmanis120Minutes(): float
	{
		return $this->getTemporalHour($this->getAlos120(), $this->getTzais120());
	}

	public function getShaahZmanis120MinutesZmanis(): float
	{
		return $this->getTemporalHour($this->getAlos120Zmanis(), $this->getTzais120Zmanis());
	}

	/*
	|--------------------------------------------------------------------------
	| PLAG HAMINCHA
	|--------------------------------------------------------------------------
	*/

	public function getPlagHamincha120MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos120Zmanis(), $this->getTzais120Zmanis());
	}

	public function getPlagHamincha120Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos120(), $this->getTzais120());
	}

	/*
	|--------------------------------------------------------------------------
	| ALOS
	|--------------------------------------------------------------------------
	*/

	public function getAlos60(): Carbon|null
	{
		return $this->getTimeOffset($this->getSunrise(), -60 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getAlos72Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-1.2);
	}

	public function getAlos96(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -96 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getAlos90Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-1.5);
	}

	public function getAlos96Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-1.6);
	}

	public function getAlos90(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -90 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getAlos120(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -120 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getAlos120Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-2.0);
	}

	public function getAlos26Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_26_DEGREES);
	}

	public function getAlos18Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(AstronomicalCalendar::ASTRONOMICAL_ZENITH);
	}

	public function getAlos19Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_19_DEGREES);
	}

	public function getAlos19Point8Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_19_POINT_8);
	}

	public function getAlos16Point1Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(ZmanimCalendar::ZENITH_16_POINT_1);
	}

	/*
	|--------------------------------------------------------------------------
	| MISHEYAKIR
	|--------------------------------------------------------------------------
	*/

	public function getMisheyakir11Point5Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_11_POINT_5);
	}

	public function getMisheyakir11Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_11_DEGREES);
	}

	public function getMisheyakir10Point2Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_10_POINT_2);
	}

	public function getMisheyakir7Point65Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_7_POINT_65);
	}

	public function getMisheyakir9Point5Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_9_POINT_5);
	}

	/*
	|--------------------------------------------------------------------------
	| SOF ZMAN SHMA
	|--------------------------------------------------------------------------
	*/

	public function getSofZmanShmaMGA19Point8Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees());
	}

	public function getSofZmanShmaMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getSofZmanShmaMGA18Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos18Degrees(), $this->getTzais18Degrees());
	}

	public function getSofZmanShmaMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanShmaMGA();
	}

	public function getSofZmanShmaMGA72MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos72Zmanis(), $this->getTzais72Zmanis());
	}

	public function getSofZmanShmaMGA90Minutes(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos90(), $this->getTzais90());
	}

	public function getSofZmanShmaMGA90MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos90Zmanis(), $this->getTzais90Zmanis());
	}

	public function getSofZmanShmaMGA96Minutes(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos96(), $this->getTzais96());
	}

	public function getSofZmanShmaMGA96MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos96Zmanis(), $this->getTzais96Zmanis());
	}

	public function getSofZmanShma3HoursBeforeChatzos(): Carbon|null
	{
		return $this->getTimeOffset($this->getChatzos(), -180 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getSofZmanShmaMGA120Minutes(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos120(), $this->getTzais120());
	}

	public function getSofZmanShmaAlos16Point1ToSunset(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getElevationAdjustedSunset());
	}

	public function getSofZmanShmaAlos16Point1ToTzaisGeonim7Point083Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getTzaisGeonim7Point083Degrees());
	}

	public function getSofZmanShmaKolEliyahu(): Carbon|null
	{
		$chatzos = $this->getFixedLocalChatzos();
		if ($chatzos == null || $this->getSunrise() == null) {
			return null;
		}
		// Carbon v2 returns diff always as positive and as an int. In order to make v3 match that, we have this here. Once we no longer support v2 we can "clean this up"
		$diff = ((int) $chatzos->diffInMilliseconds($this->getElevationAdjustedSunrise(), true)) / 2;
		return $this->getTimeOffset($chatzos, -$diff);
	}

	/*
	|--------------------------------------------------------------------------
	| SOF ZMAN TFILA
	|--------------------------------------------------------------------------
	*/

	public function getSofZmanTfilaMGA19Point8Degrees(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees());
	}

	public function getSofZmanTfilaMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getSofZmanTfilaMGA18Degrees(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos18Degrees(), $this->getTzais18Degrees());
	}

	public function getSofZmanTfilaMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanTfilaMGA();
	}

	public function getSofZmanTfilaMGA72MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos72Zmanis(), $this->getTzais72Zmanis());
	}

	public function getSofZmanTfilaMGA90Minutes(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos90(), $this->getTzais90());
	}

	public function getSofZmanTfilaMGA90MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos90Zmanis(), $this->getTzais90Zmanis());
	}

	public function getSofZmanTfilaMGA96Minutes(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos96(), $this->getTzais96());
	}

	public function getSofZmanTfilaMGA96MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos96Zmanis(), $this->getTzais96Zmanis());
	}

	public function getSofZmanTfilaMGA120Minutes(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos120(), $this->getTzais120());
	}

	public function getSofZmanTfila2HoursBeforeChatzos(): Carbon|null
	{
		return $this->getTimeOffset($this->getChatzos(), -120 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	/*
	|--------------------------------------------------------------------------
	| MIMCHA GEDOLA
	|--------------------------------------------------------------------------
	*/

	public function getMinchaGedola30Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getChatzos(), AstronomicalCalendar::MINUTE_MILLIS * 30);
	}

	public function getMinchaGedola72Minutes(): Carbon|null
	{
		if ($this->isUseAstronomicalChatzosForOtherZmanim()) {
			return $this->getHalfDayBasedZman($this->getChatzos(), $this->getTzais72(), 0.5);
		}

		return $this->getMinchaGedola($this->getAlos72(), $this->getTzais72());
	}

	public function getMinchaGedola16Point1Degrees(): Carbon|null
	{
		if ($this->isUseAstronomicalChatzosForOtherZmanim()) {
			return $this->getHalfDayBasedZman($this->getChatzos(), $this->getTzais16Point1Degrees(), 0.5);
		}

		return $this->getMinchaGedola($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getMinchaGedolaAhavatShalom(): Carbon|null
	{
		if ($this->getMinchaGedola30Minutes() == null || $this->getMinchaGedola() == null) {
			return null;
		} else {
			$fixed = $this->getMinchaGedola30Minutes();
			$ahavat_shalom = $this->getTimeOffset($this->getChatzos(), $this->getShaahZmanisAlos16Point1ToTzais3Point7() / 2);
			return $fixed->gt($ahavat_shalom) ? $fixed : $ahavat_shalom;
		}
	}

	public function getMinchaGedolaGreaterThan30(): Carbon|null
	{
		$fixed = $this->getMinchaGedola30Minutes();
		$gra = $this->getMinchaGedola();
		if ($fixed == null || $gra == null) {
			return null;
		} else {
			return $fixed->gt($gra) ? $fixed : $gra;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| MIMCHA KETANA
	|--------------------------------------------------------------------------
	*/

	public function getMinchaKetana16Point1Degrees(): Carbon|null
	{
		return $this->getMinchaKetana($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getMinchaKetanaAhavatShalom(): Carbon|null
	{
		return $this->getTimeOffset($this->getTzaisGeonim3Point8Degrees(), -$this->getShaahZmanisAlos16Point1ToTzais3Point8() * 2.5);
	}

	public function getMinchaKetana72Minutes(): Carbon|null
	{
		return $this->getMinchaKetana($this->getAlos72(), $this->getTzais72());
	}

	/*
	|--------------------------------------------------------------------------
	| PLAG HAMINCHA
	|--------------------------------------------------------------------------
	*/

	public function getPlagHamincha60Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos60(), $this->getTzais60());
	}

	public function getPlagHamincha72Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos72(), $this->getTzais72());
	}

	public function getPlagHamincha90Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos90(), $this->getTzais90());
	}

	public function getPlagHamincha96Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos96(), $this->getTzais96());
	}

	public function getPlagHamincha96MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos96Zmanis(), $this->getTzais96Zmanis());
	}

	public function getPlagHamincha90MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos90Zmanis(), $this->getTzais90Zmanis());
	}

	public function getPlagHamincha72MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos72Zmanis(), $this->getTzais72Zmanis());
	}

	public function getPlagHamincha16Point1Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getPlagHamincha19Point8Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees());
	}

	public function getPlagHamincha26Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos26Degrees(), $this->getTzais26Degrees());
	}

	public function getPlagHamincha18Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos18Degrees(), $this->getTzais18Degrees());
	}

	public function getPlagAlosToSunset(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getElevationAdjustedSunset());
	}

	public function getPlagAlos16Point1ToTzaisGeonim7Point083Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getTzaisGeonim7Point083Degrees());
	}

	public function getPlagAhavatShalom(): Carbon|null
	{
		return $this->getTimeOffset($this->getTzaisGeonim3Point8Degrees(), -$this->getShaahZmanisAlos16Point1ToTzais3Point8() * 1.25);
	}

	/*
	|--------------------------------------------------------------------------
	| BAIN HASHMASHOS
	|--------------------------------------------------------------------------
	*/

	public function getBainHashmashosRT13Point24Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_13_POINT_24);
	}

	public function getBainHashmashosRT58Point5Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 58.5 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getBainHashmashosRT13Point5MinutesBefore7Point083Degrees(): Carbon|null
	{
		return $this->getTimeOffset($this->getSunsetOffsetByDegrees(self::ZENITH_7_POINT_083), -13.5 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getBainHashmashosRT2Stars(): Carbon|null
	{
		$alos19Point8 = $this->getAlos19Point8Degrees();
		$sunrise = $this->getElevationAdjustedSunrise();
		if ($alos19Point8 == null || $sunrise == null) {
			return null;
		}

		$alos19Point8 = $alos19Point8->getPreciseTimestamp() / 1000;
		$sunrise = $sunrise->getPreciseTimestamp() / 1000;

		return $this->getTimeOffset($this->getElevationAdjustedSunset(), ($sunrise - $alos19Point8) * (5 / 18));
	}

	public function getBainHashmashosYereim18Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), -18 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getBainHashmashosYereim3Point05Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_MINUS_3_POINT_05);
	}

	public function getBainHashmashosYereim16Point875Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), -16.875 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getBainHashmashosYereim2Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_MINUS_2_POINT_8);
	}

	public function getBainHashmashosYereim13Point5Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), -13.5 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getBainHashmashosYereim2Point1Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_MINUS_2_POINT_1);
	}

	/*
	|--------------------------------------------------------------------------
	| TZAIS
	|--------------------------------------------------------------------------
	*/

	public function getTzaisGeonim3Point7Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_3_POINT_7);
	}

	public function getTzaisGeonim3Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_3_POINT_8);
	}

	public function getTzaisGeonim5Point95Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_5_POINT_95);
	}

	public function getTzaisGeonim3Point65Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_3_POINT_65);
	}

	public function getTzaisGeonim3Point676Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_3_POINT_676);
	}

	public function getTzaisGeonim4Point61Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_4_POINT_61);
	}

	public function getTzaisGeonim4Point37Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_4_POINT_37);
	}

	public function getTzaisGeonim5Point88Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_5_POINT_88);
	}

	public function getTzaisGeonim4Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_4_POINT_8);
	}

	public function getTzaisGeonim6Point45Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_6_POINT_45);
	}

	public function getTzaisGeonim7Point083Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_7_POINT_083);
	}

	public function getTzaisGeonim7Point67Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_7_POINT_67);
	}

	public function getTzaisGeonim8Point5Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(ZmanimCalendar::ZENITH_8_POINT_5);
	}

	public function getTzaisGeonim9Point3Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_9_POINT_3);
	}

	public function getTzaisGeonim9Point75Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_9_POINT_75);
	}

	public function getTzais60(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 60 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	/*
	|--------------------------------------------------------------------------
	| ATERET TORAH CALCULATIONS
	|--------------------------------------------------------------------------
	*/

	public function getTzaisAteretTorah(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), $this->getAteretTorahSunsetOffset() * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getAteretTorahSunsetOffset(): int
	{
		return $this->ateretTorahSunsetOffset;
	}

	public function setAteretTorahSunsetOffset(int $ateretTorahSunsetOffset): self
	{
		$this->ateretTorahSunsetOffset = $ateretTorahSunsetOffset;

		return $this;
	}

	public function getSofZmanShmaAteretTorah(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getSofZmanTfilahAteretTorah(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getMinchaGedolaAteretTorah(): Carbon|null
	{
		return $this->getMinchaGedola($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getMinchaKetanaAteretTorah(): Carbon|null
	{
		return $this->getMinchaKetana($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getPlagHaminchaAteretTorah(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	/*
	|--------------------------------------------------------------------------
	| TZAIS CONTINUED
	|--------------------------------------------------------------------------
	*/

	public function getTzais72Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(1.2);
	}

	/*
	|--------------------------------------------------------------------------
	| ZMANIS OFFSET HELPER
	|--------------------------------------------------------------------------
	*/

	private function getZmanisBasedOffset(float $hours = 0): Carbon|null
	{
		$shaahZmanis = $this->getShaahZmanisGra();
		if ($shaahZmanis == null || $hours == 0) {
			return null;
		}

		if ($hours > 0) {
			return $this->getTimeOffset($this->getElevationAdjustedSunset(), $shaahZmanis * $hours);
		} else {
			return $this->getTimeOffset($this->getElevationAdjustedSunrise(), $shaahZmanis * $hours);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| TZAIS CONTINUED x2
	|--------------------------------------------------------------------------
	*/

	public function getTzais90Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(1.5);
	}

	public function getTzais96Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(1.6);
	}

	public function getTzais90(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 90 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getTzais120(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 120 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getTzais120Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(2.0);
	}

	public function getTzais16Point1Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(ZmanimCalendar::ZENITH_16_POINT_1);
	}

	public function getTzais26Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_26_DEGREES);
	}

	public function getTzais18Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(AstronomicalCalendar::ASTRONOMICAL_ZENITH);
	}

	public function getTzais19Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_19_POINT_8);
	}

	public function getTzais96(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 96 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	/*
	|--------------------------------------------------------------------------
	| FIXED ZMANIM
	|--------------------------------------------------------------------------
	*/

	public function getFixedLocalChatzos(): Carbon|null
	{
		return $this->getLocalMeanTime(12.0);
	}

	public function getSofZmanShmaFixedLocal(): Carbon|null
	{
		return $this->getTimeOffset($this->getFixedLocalChatzos(), -180 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getSofZmanTfilaFixedLocal(): Carbon|null
	{
		return $this->getTimeOffset($this->getFixedLocalChatzos(), -120 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	/*
	|--------------------------------------------------------------------------
	| MOLAD
	|--------------------------------------------------------------------------
	*/

	public function getSofZmanKidushLevanaBetweenMoldos(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = new JewishCalendar($this->getCalendar());

		if ($jewishCalendar->getJewishDayOfMonth() < 11 || $jewishCalendar->getJewishDayOfMonth() > 16) {
			return null;
		}

		return $this->getMoladBasedTime($jewishCalendar->getSofZmanKidushLevanaBetweenMoldos(), $alos, $tzais, false);
	}

	private function getMoladBasedTime(Carbon $moladBasedTime, ?Carbon $alos, ?Carbon $tzais, bool $techila): Carbon|null
	{
		$lastMidnight = $this->getMidnightLastNight();
		$midnightTonight = $this->getMidnightTonight();
		if (!($moladBasedTime->lt($lastMidnight) || $moladBasedTime->gt($midnightTonight))){
			if ($alos != null || $tzais != null) {
				if ($techila && !($moladBasedTime->lt($tzais) || $moladBasedTime->gt($alos))){
					return $tzais;
				} else {
					return $alos;
				}
			}
			return $moladBasedTime;
		}
		return null;
	}

	public function getSofZmanKidushLevana15Days(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = new JewishCalendar($this->getCalendar());

		if ($jewishCalendar->getJewishDayOfMonth() < 11 || $jewishCalendar->getJewishDayOfMonth() > 17) {
			return null;
		}

		return $this->getMoladBasedTime($jewishCalendar->getSofZmanKidushLevana15Days(), $alos, $tzais, false);
	}

	public function getTchilasZmanKidushLevana3Days(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = new JewishCalendar($this->getCalendar());

		if ($jewishCalendar->getJewishDayOfMonth() > 5 && $jewishCalendar->getJewishDayOfMonth() < 30) {
			return null;
		}

		$zman = $this->getMoladBasedTime($jewishCalendar->getTchilasZmanKidushLevana3Days(), $alos, $tzais, true);

		if ($zman == null && $jewishCalendar->getJewishDayOfMonth() == 30) {
			$jewishCalendar->addMonthsJewish(1);
			$zman = $this->getMoladBasedTime($jewishCalendar->getTchilasZmanKidushLevana3Days(), null, null, true);
		}

		return $zman;
	}

	public function getZmanMolad(): Carbon|null
	{
		$calendar = $this->getCalendar()->copy()->shiftTimezone("GMT+2");
		$jewishCalendar = new JewishCalendar($calendar);

		if ($jewishCalendar->getJewishDayOfMonth() > 2 && $jewishCalendar->getJewishDayOfMonth() < 27) {
			return null;
		}

		// We can be up to 2 days off if it's right before midnight somewhere to the West of Jerusalem, but after midnight in Jerusalem
		for ($i = 0; $i <= 2; $i++) {
			$molad = $this->getMoladBasedTime($jewishCalendar->getMoladAsDate()->timezone($this->getGeoLocation()->getTimeZone()), null, null, true);

			if ($molad != null) {
				break;
			}

			$jewishCalendar->addDays(1);
		}

		return $molad;
	}

	public function getTchilasZmanKidushLevana7Days(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = new JewishCalendar($this->getCalendar());

		if ($jewishCalendar->getJewishDayOfMonth() < 4 || $jewishCalendar->getJewishDayOfMonth() > 9) {
			return null;
		}

		return $this->getMoladBasedTime($jewishCalendar->getTchilasZmanKidushLevana7Days(), $alos, $tzais, true);
	}

	/*
	|--------------------------------------------------------------------------
	| PESACH
	|--------------------------------------------------------------------------
	*/

	public function getSofZmanAchilasChametzGRA(): Carbon|null
	{
		return $this->getSofZmanTfilaGRA();
	}

	public function getSofZmanAchilasChametzMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanTfilaMGA72Minutes();
	}

	public function getSofZmanAchilasChametzMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanTfilaMGA16Point1Degrees();
	}

	public function getSofZmanBiurChametzGRA(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), $this->getShaahZmanisGra() * 5);
	}

	public function getSofZmanBiurChametzMGA72Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getAlos72(), $this->getShaahZmanisMGA() * 5);
	}

	public function getSofZmanBiurChametzMGA16Point1Degrees(): Carbon|null
	{
		return $this->getTimeOffset($this->getAlos16Point1Degrees(), $this->getShaahZmanis16Point1Degrees() * 5);
	}

	/*
	|--------------------------------------------------------------------------
	| BAAL HATANYA
	|--------------------------------------------------------------------------
	*/

	private function getSunriseBaalHatanya(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_1_POINT_583);
	}

	private function getSunsetBaalHatanya(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_1_POINT_583);
	}

	public function getShaahZmanisBaalHatanya(): float
	{
		return $this->getTemporalHour($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getAlosBaalHatanya(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_16_POINT_9);
	}

	public function getSofZmanShmaBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanShma($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getSofZmanTfilaBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getSofZmanAchilasChametzBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanTfilaBaalHatanya();
	}

	public function getSofZmanBiurChametzBaalHatanya(): Carbon|null
	{
		return $this->getTimeOffset($this->getSunriseBaalHatanya(), $this->getShaahZmanisBaalHatanya() * 5);
	}

	public function getMinchaGedolaBaalHatanya(): Carbon|null
	{
		return $this->getMinchaGedola($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getMinchaGedolaBaalHatanyaGreaterThan30(): Carbon|null
	{
		$fixed = $this->getMinchaGedola30Minutes();
		$tanya = $this->getMinchaGedolaBaalHatanya();
		if ($fixed == null || $tanya == null) {
			return null;
		} else {
			return $fixed->gt($tanya) ? $fixed : $tanya;
		}
	}

	public function getMinchaKetanaBaalHatanya(): Carbon|null
	{
		return $this->getMinchaKetana($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getPlagHaminchaBaalHatanya(): Carbon|null
	{
		return $this->getPlagHamincha($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getTzaisBaalHatanya(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_6_DEGREES);
	}

	/*
	|--------------------------------------------------------------------------
	| FIXED LOCAL CHATZOS
	|--------------------------------------------------------------------------
	*/

	public function getSofZmanShmaMGA18DegreesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos18Degrees(), $this->getFixedLocalChatzos(), 3);
	}

	public function getSofZmanShmaMGA16Point1DegreesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos16Point1Degrees(), $this->getFixedLocalChatzos(), 3);
	}

	public function getSofZmanShmaMGA90MinutesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos90(), $this->getFixedLocalChatzos(), 3);
	}

	public function getSofZmanShmaMGA72MinutesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos72(), $this->getFixedLocalChatzos(), 3);
	}

	public function getSofZmanShmaGRASunriseToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getSunrise(), $this->getFixedLocalChatzos(), 3);
	}

	public function getSofZmanTfilaGRASunriseToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getSunrise(), $this->getFixedLocalChatzos(), 4);
	}

	public function getMinchaGedolaGRAFixedLocalChatzos30Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getFixedLocalChatzos(), AstronomicalCalendar::MINUTE_MILLIS * 30);
	}

	public function getMinchaKetanaGRAFixedLocalChatzosToSunset(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getFixedLocalChatzos(), $this->getSunset(), 3.5);
	}

	public function getPlagHaminchaGRAFixedLocalChatzosToSunset(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getFixedLocalChatzos(), $this->getSunset(), 4.75);
	}

	/*
	|--------------------------------------------------------------------------
	| RAV MOSHE 50 MINUTES
	|--------------------------------------------------------------------------
	*/

	public function getTzais50(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 50 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	/*
	|--------------------------------------------------------------------------
	| SAMUCH LEMINCHA KETANA
	|--------------------------------------------------------------------------
	*/

	public function getSamuchLeMinchaKetanaGRA(): Carbon|null
	{
		return $this->getSamuchLeMinchaKetana($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset());
	}

	public function getSamuchLeMinchaKetana16Point1Degrees(): Carbon|null
	{
		return $this->getSamuchLeMinchaKetana($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getSamuchLeMinchaKetana72Minutes(): Carbon|null
	{
		return $this->getSamuchLeMinchaKetana($this->getAlos72(), $this->getTzais72());
	}
}
