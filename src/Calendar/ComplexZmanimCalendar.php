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

namespace PhpZmanim\Calendar;

use PhpZmanim\Geo\GeoLocation;

/**
 * See https://github.com/KosherJava/zmanim/blob/master/src/net/sourceforge/zmanim/ComplexZmanimCalendar.java
 * for more detailed explanations regarding the methods and variables on this page.
 */
class ComplexZmanimCalendar extends ZmanimCalendar {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private $ateretTorahSunsetOffset = 40;

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

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getShaahZmanis19Point8Degrees() {
		return $this->getTemporalHour($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees());
	}

	public function getShaahZmanis18Degrees() {
		return $this->getTemporalHour($this->getAlos18Degrees(), $this->getTzais18Degrees());
	}

	public function getShaahZmanis26Degrees() {
		return $this->getTemporalHour($this->getAlos26Degrees(), $this->getTzais26Degrees());
	}

	public function getShaahZmanis16Point1Degrees() {
		return $this->getTemporalHour($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getShaahZmanis60Minutes() {
		return $this->getTemporalHour($this->getAlos60(), $this->getTzais60());
	}

	public function getShaahZmanis72Minutes() {
		return $this->getShaahZmanisMGA();
	}

	public function getShaahZmanis72MinutesZmanis() {
		return $this->getTemporalHour($this->getAlos72Zmanis(), $this->getTzais72Zmanis());
	}

	public function getShaahZmanis90Minutes() {
		return $this->getTemporalHour($this->getAlos90(), $this->getTzais90());
	}

	public function getShaahZmanis90MinutesZmanis() {
		return $this->getTemporalHour($this->getAlos90Zmanis(), $this->getTzais90Zmanis());
	}

	public function getShaahZmanis96MinutesZmanis() {
		return $this->getTemporalHour($this->getAlos96Zmanis(), $this->getTzais96Zmanis());
	}

	public function getShaahZmanisAteretTorah() {
		return $this->getTemporalHour($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getShaahZmanis96Minutes() {
		return $this->getTemporalHour($this->getAlos96(), $this->getTzais96());
	}

	public function getShaahZmanis120Minutes() {
		return $this->getTemporalHour($this->getAlos120(), $this->getTzais120());
	}

	public function getShaahZmanis120MinutesZmanis() {
		return $this->getTemporalHour($this->getAlos120Zmanis(), $this->getTzais120Zmanis());
	}

	public function getPlagHamincha120MinutesZmanis() {
		return $this->getPlagHamincha($this->getAlos120Zmanis(), $this->getTzais120Zmanis());
	}

	public function getPlagHamincha120Minutes() {
		return $this->getPlagHamincha($this->getAlos120(), $this->getTzais120());
	}

	public function getAlos60() {
		return $this->getTimeOffset($this->getSunrise(), -60 * GeoLocation::MINUTE_SECOND);
	}

	public function getAlos72Zmanis() {
		$shaahZmanis = $this->getShaahZmanisGra();
		if ($shaahZmanis == null) {
			return null;
		}
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), $shaahZmanis * -1.2);
	}

	public function getAlos96() {
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -96 * GeoLocation::MINUTE_SECOND);
	}

	public function getAlos90Zmanis() {
		$shaahZmanis = $this->getShaahZmanisGra();
		if ($shaahZmanis == null) {
			return null;
		}
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), $shaahZmanis * -1.5);
	}

	public function getAlos96Zmanis() {
		$shaahZmanis = $this->getShaahZmanisGra();
		if ($shaahZmanis == null) {
			return null;
		}
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), $shaahZmanis * -1.6);
	}

	public function getAlos90() {
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -90 * GeoLocation::MINUTE_SECOND);
	}

	public function getAlos120() {
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -120 * GeoLocation::MINUTE_SECOND);
	}

	public function getAlos120Zmanis() {
		$shaahZmanis = $this->getShaahZmanisGra();
		if ($shaahZmanis == null) {
			return null;
		}
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), $shaahZmanis * -2);
	}

	public function getAlos26Degrees() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_26_DEGREES);
	}

	public function getAlos18Degrees() {
		return $this->getSunriseOffsetByDegrees(AstronomicalCalendar::ASTRONOMICAL_ZENITH);
	}

	public function getAlos19Degrees() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_19_DEGREES);
	}

	public function getAlos19Point8Degrees() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_19_POINT_8);
	}

	public function getAlos16Point1Degrees() {
		return $this->getSunriseOffsetByDegrees(ZmanimCalendar::ZENITH_16_POINT_1);
	}

	public function getMisheyakir11Point5Degrees() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_11_POINT_5);
	}

	public function getMisheyakir11Degrees() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_11_DEGREES);
	}

	public function getMisheyakir10Point2Degrees() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_10_POINT_2);
	}

	public function getMisheyakir7Point65Degrees() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_7_POINT_65);
	}

	public function getMisheyakir9Point5Degrees() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_9_POINT_5);
	}

	public function getSofZmanShmaMGA19Point8Degrees() {
		return $this->getSofZmanShma($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees());
	}

	public function getSofZmanShmaMGA16Point1Degrees() {
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getSofZmanShmaMGA18Degrees() {
		return $this->getSofZmanShma($this->getAlos18Degrees(), $this->getTzais18Degrees());
	}

	public function getSofZmanShmaMGA72Minutes() {
		return $this->getSofZmanShmaMGA();
	}

	public function getSofZmanShmaMGA72MinutesZmanis() {
		return $this->getSofZmanShma($this->getAlos72Zmanis(), $this->getTzais72Zmanis());
	}

	public function getSofZmanShmaMGA90Minutes() {
		return $this->getSofZmanShma($this->getAlos90(), $this->getTzais90());
	}

	public function getSofZmanShmaMGA90MinutesZmanis() {
		return $this->getSofZmanShma($this->getAlos90Zmanis(), $this->getTzais90Zmanis());
	}

	public function getSofZmanShmaMGA96Minutes() {
		return $this->getSofZmanShma($this->getAlos96(), $this->getTzais96());
	}

	public function getSofZmanShmaMGA96MinutesZmanis() {
		return $this->getSofZmanShma($this->getAlos96Zmanis(), $this->getTzais96Zmanis());
	}

	public function getSofZmanShma3HoursBeforeChatzos() {
		return $this->getTimeOffset($this->getChatzos(), -180 * GeoLocation::MINUTE_SECOND);
	}

	public function getSofZmanShmaMGA120Minutes() {
		return $this->getSofZmanShma($this->getAlos120(), $this->getTzais120());
	}

	public function getSofZmanShmaAlos16Point1ToSunset() {
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getElevationAdjustedSunset());
	}

	public function getSofZmanShmaAlos16Point1ToTzaisGeonim7Point083Degrees() {
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getTzaisGeonim7Point083Degrees());
	}

	public function getSofZmanTfilaMGA19Point8Degrees() {
		return $this->getSofZmanTfila($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees());
	}

	public function getSofZmanTfilaMGA16Point1Degrees() {
		return $this->getSofZmanTfila($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getSofZmanTfilaMGA18Degrees() {
		return $this->getSofZmanTfila($this->getAlos18Degrees(), $this->getTzais18Degrees());
	}

	public function getSofZmanTfilaMGA72Minutes() {
		return $this->getSofZmanTfilaMGA();
	}

	public function getSofZmanTfilaMGA72MinutesZmanis() {
		return $this->getSofZmanTfila($this->getAlos72Zmanis(), $this->getTzais72Zmanis());
	}

	public function getSofZmanTfilaMGA90Minutes() {
		return $this->getSofZmanTfila($this->getAlos90(), $this->getTzais90());
	}

	public function getSofZmanTfilaMGA90MinutesZmanis() {
		return $this->getSofZmanTfila($this->getAlos90Zmanis(), $this->getTzais90Zmanis());
	}

	public function getSofZmanTfilaMGA96Minutes() {
		return $this->getSofZmanTfila($this->getAlos96(), $this->getTzais96());
	}

	public function getSofZmanTfilaMGA96MinutesZmanis() {
		return $this->getSofZmanTfila($this->getAlos96Zmanis(), $this->getTzais96Zmanis());
	}

	public function getSofZmanTfilaMGA120Minutes() {
		return $this->getSofZmanTfila($this->getAlos120(), $this->getTzais120());
	}

	public function getSofZmanTfila2HoursBeforeChatzos() {
		return $this->getTimeOffset($this->getChatzos(), -120 * GeoLocation::MINUTE_SECOND);
	}

	public function getMinchaGedola30Minutes() {
		return $this->getTimeOffset($this->getChatzos(), GeoLocation::MINUTE_SECOND * 30);
	}

	public function getMinchaGedola72Minutes() {
		return $this->getMinchaGedola($this->getAlos72(), $this->getTzais72());
	}

	public function getMinchaGedola16Point1Degrees() {
		return $this->getMinchaGedola($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getMinchaGedolaGreaterThan30() {
		$fixed = $this->getMinchaGedola30Minutes();
		$gra = $this->getMinchaGedolaGra();
		if ($fixed == null || $gra == null) {
			return null;
		} else {
			return $fixed->gt($gra) ? $fixed : $gra;
		}
	}

	public function getMinchaKetana16Point1Degrees() {
		return $this->getMinchaKetana($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getMinchaKetana72Minutes() {
		return $this->getMinchaKetana($this->getAlos72(), $this->getTzais72());
	}

	public function getPlagHamincha60Minutes() {
		return $this->getPlagHamincha($this->getAlos60(), $this->getTzais60());
	}

	public function getPlagHamincha72Minutes() {
		return $this->getPlagHamincha($this->getAlos72(), $this->getTzais72());
	}

	public function getPlagHamincha90Minutes() {
		return $this->getPlagHamincha($this->getAlos90(), $this->getTzais90());
	}

	public function getPlagHamincha96Minutes() {
		return $this->getPlagHamincha($this->getAlos96(), $this->getTzais96());
	}

	public function getPlagHamincha96MinutesZmanis() {
		return $this->getPlagHamincha($this->getAlos96Zmanis(), $this->getTzais96Zmanis());
	}

	public function getPlagHamincha90MinutesZmanis() {
		return $this->getPlagHamincha($this->getAlos90Zmanis(), $this->getTzais90Zmanis());
	}

	public function getPlagHamincha72MinutesZmanis() {
		return $this->getPlagHamincha($this->getAlos72Zmanis(), $this->getTzais72Zmanis());
	}

	public function getPlagHamincha16Point1Degrees() {
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getPlagHamincha19Point8Degrees() {
		return $this->getPlagHamincha($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees());
	}

	public function getPlagHamincha26Degrees() {
		return $this->getPlagHamincha($this->getAlos26Degrees(), $this->getTzais26Degrees());
	}

	public function getPlagHamincha18Degrees() {
		return $this->getPlagHamincha($this->getAlos18Degrees(), $this->getTzais18Degrees());
	}

	public function getPlagAlosToSunset() {
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getElevationAdjustedSunset());
	}

	public function getPlagAlos16Point1ToTzaisGeonim7Point083Degrees() {
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getTzaisGeonim7Point083Degrees());
	}

	public function getBainHasmashosRT13Point24Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_13_POINT_24);
	}

	public function getBainHasmashosRT58Point5Minutes() {
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 58.5 * GeoLocation::MINUTE_SECOND);
	}

	public function getBainHasmashosRT13Point5MinutesBefore7Point083Degrees() {
		return $this->getTimeOffset($this->getSunsetOffsetByDegrees(self::ZENITH_7_POINT_083), -13.5 * GeoLocation::MINUTE_SECOND);
	}

	public function getBainHasmashosRT2Stars() {
		$alos19Point8 = $this->getAlos19Point8Degrees();
		$sunrise = $this->getElevationAdjustedSunrise();
		if ($alos19Point8 == null || $sunrise == null) {
			return null;
		}

		$alos19Point8 = $alos19Point8->format('U.u');
		$sunrise = $sunrise->format('U.u');

		return $this->getTimeOffset($this->getElevationAdjustedSunset(), ($sunrise - $alos19Point8) * (5 / 18));
	}

	public function getTzaisGeonim3Point7Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_3_POINT_7);
	}

	public function getTzaisGeonim3Point8Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_3_POINT_8);
	}

	public function getTzaisGeonim5Point95Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_5_POINT_95);
	}

	public function getTzaisGeonim3Point65Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_3_POINT_65);
	}

	public function getTzaisGeonim3Point676Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_3_POINT_676);
	}

	public function getTzaisGeonim4Point61Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_4_POINT_61);
	}

	public function getTzaisGeonim4Point37Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_4_POINT_37);
	}

	public function getTzaisGeonim5Point88Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_5_POINT_88);
	}

	public function getTzaisGeonim4Point8Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_4_POINT_8);
	}

	public function getTzaisGeonim6Point45Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_6_POINT_45);
	}

	public function getTzaisGeonim7Point083Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_7_POINT_083);
	}

	public function getTzaisGeonim7Point67Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_7_POINT_67);
	}

	public function getTzaisGeonim8Point5Degrees() {
		return $this->getSunsetOffsetByDegrees(ZmanimCalculator::ZENITH_8_POINT_5);
	}

	public function getTzaisGeonim9Point3Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_9_POINT_3);
	}

	public function getTzaisGeonim9Point75Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_9_POINT_75);
	}

	public function getTzais60() {
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 60 * GeoLocation::MINUTE_SECOND);
	}

	public function getTzaisAteretTorah() {
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), $this->getAteretTorahSunsetOffset() * GeoLocation::MINUTE_SECOND);
	}

	public function getAteretTorahSunsetOffset() {
		return $this->ateretTorahSunsetOffset;
	}

	public function setAteretTorahSunsetOffset($ateretTorahSunsetOffset) {
		$this->ateretTorahSunsetOffset = $ateretTorahSunsetOffset;
	}

	public function getSofZmanShmaAteretTorah() {
		return $this->getSofZmanShma($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getSofZmanTfilahAteretTorah() {
		return $this->getSofZmanTfila($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getMinchaGedolaAteretTorah() {
		return $this->getMinchaGedola($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getMinchaKetanaAteretTorah() {
		return $this->getMinchaKetana($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getPlagHaminchaAteretTorah() {
		return $this->getPlagHamincha($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getTzais72Zmanis() {
		$shaahZmanis = $this->getShaahZmanisGra();
		if ($shaahZmanis == null) {
			return null;
		}
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), $shaahZmanis * 1.2);
	}

	public function getTzais90Zmanis() {
		$shaahZmanis = $this->getShaahZmanisGra();
		if ($shaahZmanis == null) {
			return null;
		}
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), $shaahZmanis * 1.5);
	}

	public function getTzais96Zmanis() {
		$shaahZmanis = $this->getShaahZmanisGra();
		if ($shaahZmanis == null) {
			return null;
		}
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), $shaahZmanis * 1.6);
	}

	public function getTzais90() {
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 90 * GeoLocation::MINUTE_SECOND);
	}

	public function getTzais120() {
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 120 * GeoLocation::MINUTE_SECOND);
	}

	public function getTzais120Zmanis() {
		$shaahZmanis = $this->getShaahZmanisGra();
		if ($shaahZmanis == null) {
			return null;
		}
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), $shaahZmanis * 2.0);
	}

	public function getTzais16Point1Degrees() {
		return $this->getSunsetOffsetByDegrees(ZmanimCalendar::ZENITH_16_POINT_1);
	}

	public function getTzais26Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_26_DEGREES);
	}

	public function getTzais18Degrees() {
		return $this->getSunsetOffsetByDegrees(AstronomicalCalendar::ASTRONOMICAL_ZENITH);
	}

	public function getTzais19Point8Degrees() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_19_POINT_8);
	}

	public function getTzais96() {
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 96 * GeoLocation::MINUTE_SECOND);
	}

	public function getFixedLocalChatzos() {
		return $this->getTimeOffset($this->getDateFromTime(12.0 - $this->getGeoLocation()->getStandardTimeOffset()
				/ GeoLocation::HOUR_MILLIS, true), -$this->getGeoLocation()->getLocalMeanTimeOffset());
	}

	public function getSofZmanShmaFixedLocal() {
		return $this->getTimeOffset($this->getFixedLocalChatzos(), -180 * GeoLocation::MINUTE_SECOND);
	}

	public function getSofZmanTfilaFixedLocal() {
		return $this->getTimeOffset($this->getFixedLocalChatzos(), -120 * GeoLocation::MINUTE_SECOND);
	}

	public function getSofZmanAchilasChametzGRA() {
		return $this->getSofZmanTfilaGRA();
	}

	public function getSofZmanAchilasChametzMGA72Minutes() {
		return $this->getSofZmanTfilaMGA72Minutes();
	}

	public function getSofZmanAchilasChametzMGA16Point1Degrees() {
		return $this->getSofZmanTfilaMGA16Point1Degrees();
	}

	public function getSofZmanBiurChametzGRA() {
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), $this->getShaahZmanisGra() * 5);
	}

	public function getSofZmanBiurChametzMGA72Minutes() {
		return $this->getTimeOffset($this->getAlos72(), $this->getShaahZmanisMGA() * 5);
	}

	public function getSofZmanBiurChametzMGA16Point1Degrees() {
		return $this->getTimeOffset($this->getAlos16Point1Degrees(), $this->getShaahZmanis16Point1Degrees() * 5);
	}

	public function getSolarMidnight() {
		$clonedCal = $this->copy();
		$clonedCal->getCalendar()->addDay();
		$sunset = $this->getSeaLevelSunset();
		$sunrise = $clonedCal->getSeaLevelSunrise();
		return $this->getTimeOffset($sunset, $this->getTemporalHour($sunset, $sunrise) * 6);
	}

	private function getSunriseBaalHatanya() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_1_POINT_583);
	}

	private function getSunsetBaalHatanya() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_1_POINT_583);
	}

	public function getShaahZmanisBaalHatanya() {
		return $this->getTemporalHour($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getAlosBaalHatanya() {
		return $this->getSunriseOffsetByDegrees(self::ZENITH_16_POINT_9);
	}

	public function getSofZmanShmaBaalHatanya() {
		return $this->getSofZmanShma($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getSofZmanTfilaBaalHatanya() {
		return $this->getSofZmanTfila($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getSofZmanAchilasChametzBaalHatanya() {
		return $this->getSofZmanTfilaBaalHatanya();
	}

	public function getSofZmanBiurChametzBaalHatanya() {
		return $this->getTimeOffset($this->getSunriseBaalHatanya(), $this->getShaahZmanisBaalHatanya() * 5);
	}

	public function getMinchaGedolaBaalHatanya() {
		return $this->getMinchaGedola($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getMinchaGedolaBaalHatanyaGreaterThan30() {
		$fixed = $this->getMinchaGedola30Minutes();
		$tanya = $this->getMinchaGedolaBaalHatanya();
		if ($fixed == null || $tanya == null) {
			return null;
		} else {
			return $fixed->gt($tanya) ? $fixed : $tanya;
		}
	}

	public function getMinchaKetanaBaalHatanya() {
		return $this->getMinchaKetana($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getPlagHaminchaBaalHatanya() {
		return $this->getPlagHamincha($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	public function getTzaisBaalHatanya() {
		return $this->getSunsetOffsetByDegrees(self::ZENITH_6_DEGREES);
	}
}