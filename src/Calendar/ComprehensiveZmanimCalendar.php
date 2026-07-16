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
use PhpZmanim\HebrewCalendar\JewishCalendar;

class ComprehensiveZmanimCalendar extends ZmanimCalendar
{
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private float $ateretTorahSunsetOffset = 40;

	const ZENITH_3_POINT_7 = AstronomicalCalendar::GEOMETRIC_ZENITH + 3.7;
	const ZENITH_3_POINT_8 = AstronomicalCalendar::GEOMETRIC_ZENITH + 3.8;
	const ZENITH_5_POINT_95 = AstronomicalCalendar::GEOMETRIC_ZENITH + 5.95;
	const ZENITH_7_POINT_083 = AstronomicalCalendar::GEOMETRIC_ZENITH + 7 + (5.0 / 60);
	const ZENITH_10_POINT_2 = AstronomicalCalendar::GEOMETRIC_ZENITH + 10.2;
	const ZENITH_11_DEGREES = AstronomicalCalendar::GEOMETRIC_ZENITH + 11;
	const ZENITH_11_POINT_5 = AstronomicalCalendar::GEOMETRIC_ZENITH + 11.5;
	const ZENITH_12_POINT_85 = AstronomicalCalendar::GEOMETRIC_ZENITH + 12.85;
	const ZENITH_13_POINT_24 = AstronomicalCalendar::GEOMETRIC_ZENITH + 13.24;
	const ZENITH_19_DEGREES = AstronomicalCalendar::GEOMETRIC_ZENITH + 19;
	const ZENITH_19_POINT_8 = AstronomicalCalendar::GEOMETRIC_ZENITH + 19.8;
	const ZENITH_26_DEGREES = AstronomicalCalendar::GEOMETRIC_ZENITH + 26.0;
	const ZENITH_4_POINT_42 = AstronomicalCalendar::GEOMETRIC_ZENITH + 4.42;
	const ZENITH_4_POINT_66 = AstronomicalCalendar::GEOMETRIC_ZENITH + 4.66;
	const ZENITH_4_POINT_8 = AstronomicalCalendar::GEOMETRIC_ZENITH + 4.8;
	const ZENITH_16_POINT_9 = AstronomicalCalendar::GEOMETRIC_ZENITH + 16.9;
	const ZENITH_6_DEGREES = AstronomicalCalendar::GEOMETRIC_ZENITH + 6;
	const ZENITH_6_POINT_45 = AstronomicalCalendar::GEOMETRIC_ZENITH + 6.45;
	const ZENITH_7_POINT_65 = AstronomicalCalendar::GEOMETRIC_ZENITH + 7.65;
	const ZENITH_7_POINT_67 = AstronomicalCalendar::GEOMETRIC_ZENITH + 7.67;
	const ZENITH_9_POINT_3 = AstronomicalCalendar::GEOMETRIC_ZENITH + 9.3;
	const ZENITH_9_POINT_5 = AstronomicalCalendar::GEOMETRIC_ZENITH + 9.5;
	const ZENITH_9_POINT_75 = AstronomicalCalendar::GEOMETRIC_ZENITH + 9.75;
	const ZENITH_MINUS_2_POINT_1 = AstronomicalCalendar::GEOMETRIC_ZENITH - 2.1;
	const ZENITH_MINUS_2_POINT_8 = AstronomicalCalendar::GEOMETRIC_ZENITH - 2.8;
	const ZENITH_MINUS_3_POINT_05 = AstronomicalCalendar::GEOMETRIC_ZENITH - 3.05;

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function getAteretTorahSunsetOffset(): float
	{
		return $this->ateretTorahSunsetOffset;
	}

	public function setAteretTorahSunsetOffset(float $ateretTorahSunsetOffset): self
	{
		$this->ateretTorahSunsetOffset = $ateretTorahSunsetOffset;

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| SHAAH ZMANIS
	|--------------------------------------------------------------------------
	*/

	public function getShaahZmanis72Minutes(): float|null
	{
		return $this->getShaahZmanisMGA();
	}

	public function getShaahZmanis19Point8Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees());
	}

	public function getShaahZmanis18Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos18Degrees(), $this->getTzais18Degrees());
	}

	public function getShaahZmanis26Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos26Degrees(), $this->getTzais26Degrees());
	}

	public function getShaahZmanis16Point1Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees());
	}

	public function getShaahZmanis60Minutes(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos60Minutes(), $this->getTzais60Minutes());
	}

	public function getShaahZmanis72MinutesZmanis(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos72Zmanis(), $this->getTzais72Zmanis());
	}

	public function getShaahZmanis90Minutes(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos90Minutes(), $this->getTzais90Minutes());
	}

	public function getShaahZmanis90MinutesZmanis(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos90Zmanis(), $this->getTzais90Zmanis());
	}

	public function getShaahZmanis96Minutes(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos96Minutes(), $this->getTzais96Minutes());
	}

	public function getShaahZmanis96MinutesZmanis(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos96Zmanis(), $this->getTzais96Zmanis());
	}

	public function getShaahZmanis120Minutes(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos120Minutes(), $this->getTzais120Minutes());
	}

	public function getShaahZmanis120MinutesZmanis(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos120Zmanis(), $this->getTzais120Zmanis());
	}

	public function getShaahZmanisAteretTorah(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos72Zmanis(), $this->getTzaisAteretTorah());
	}

	public function getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point8Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos16Point1Degrees(), $this->getTzaisGeonim3Point8Degrees());
	}

	public function getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point7Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos16Point1Degrees(), $this->getTzaisGeonim3Point7Degrees());
	}

	public function getShaahZmanisAlos16Point1DegreesToTzaisGeonim7Point083Degrees(): float|null
	{
		return $this->temporalHourOrNull($this->getAlos16Point1Degrees(), $this->getTzaisGeonim7Point083Degrees());
	}

	public function getShaahZmanisBaalHatanya(): float|null
	{
		return $this->temporalHourOrNull($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya());
	}

	/*
	|--------------------------------------------------------------------------
	| ALOS
	|--------------------------------------------------------------------------
	*/

	public function getAlos72Minutes(): Carbon|null
	{
		return $this->getAlos72();
	}

	public function getAlos16Point1Degrees(): Carbon|null
	{
		return $this->getAlosHashachar();
	}

	public function getAlos60Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -60 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getAlos90Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -90 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getAlos96Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -96 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getAlos120Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunrise(), -120 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getAlos72Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-1.2);
	}

	public function getAlos90Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-1.5);
	}

	public function getAlos96Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-1.6);
	}

	public function getAlos120Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(-2.0);
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

	public function getAlos26Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_26_DEGREES);
	}

	/*
	|--------------------------------------------------------------------------
	| MISHEYAKIR
	|--------------------------------------------------------------------------
	*/

	public function getMisheyakir12Point85Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_12_POINT_85);
	}

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

	public function getMisheyakir9Point5Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_9_POINT_5);
	}

	public function getMisheyakir7Point65Degrees(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_7_POINT_65);
	}

	/*
	|--------------------------------------------------------------------------
	| CHATZOS
	|--------------------------------------------------------------------------
	*/

	public function getChatzosHayom(): Carbon|null
	{
		return $this->getChatzos();
	}

	public function getChatzosHayomAsHalfDay(): Carbon|null
	{
		return $this->getChatzosAsHalfDay();
	}

	/*
	|--------------------------------------------------------------------------
	| SOF ZMAN SHMA
	|--------------------------------------------------------------------------
	*/

	public function getSofZmanShmaMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanShmaMGA();
	}

	public function getSofZmanShmaMGA72MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos72Zmanis(), $this->getTzais72Zmanis(), true);
	}

	public function getSofZmanShmaMGA90Minutes(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos90Minutes(), $this->getTzais90Minutes(), true);
	}

	public function getSofZmanShmaMGA90MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos90Zmanis(), $this->getTzais90Zmanis(), true);
	}

	public function getSofZmanShmaMGA96Minutes(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos96Minutes(), $this->getTzais96Minutes(), true);
	}

	public function getSofZmanShmaMGA96MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos96Zmanis(), $this->getTzais96Zmanis(), true);
	}

	public function getSofZmanShmaMGA120Minutes(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos120Minutes(), $this->getTzais120Minutes(), true);
	}

	public function getSofZmanShmaMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getSofZmanShmaMGA18Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos18Degrees(), $this->getTzais18Degrees(), true);
	}

	public function getSofZmanShmaMGA19Point8Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees(), true);
	}

	public function getSofZmanShma3HoursBeforeChatzos(): Carbon|null
	{
		return $this->getTimeOffset($this->getChatzosHayom(), -3 * AstronomicalCalendar::HOUR_MILLIS);
	}

	public function getSofZmanShmaAlos16Point1ToSunset(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getElevationAdjustedSunset(), false);
	}

	public function getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos16Point1Degrees(), $this->getTzaisGeonim7Point083Degrees(), false);
	}

	public function getSofZmanShmaMGA18DegreesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos18Degrees(), $this->getFixedLocalChatzosHayom(), 3);
	}

	public function getSofZmanShmaMGA16Point1DegreesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos16Point1Degrees(), $this->getFixedLocalChatzosHayom(), 3);
	}

	public function getSofZmanShmaMGA90MinutesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos90Minutes(), $this->getFixedLocalChatzosHayom(), 3);
	}

	public function getSofZmanShmaMGA72MinutesToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getAlos72Minutes(), $this->getFixedLocalChatzosHayom(), 3);
	}

	public function getSofZmanShmaGRASunriseToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getElevationAdjustedSunrise(), $this->getFixedLocalChatzosHayom(), 3);
	}

	/*
	|--------------------------------------------------------------------------
	| SOF ZMAN TFILA
	|--------------------------------------------------------------------------
	*/

	public function getSofZmanTfilaMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanTfilaMGA();
	}

	public function getSofZmanTfilaMGA72MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos72Zmanis(), $this->getTzais72Zmanis(), true);
	}

	public function getSofZmanTfilaMGA90Minutes(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos90Minutes(), $this->getTzais90Minutes(), true);
	}

	public function getSofZmanTfilaMGA90MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos90Zmanis(), $this->getTzais90Zmanis(), true);
	}

	public function getSofZmanTfilaMGA96Minutes(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos96Minutes(), $this->getTzais96Minutes(), true);
	}

	public function getSofZmanTfilaMGA96MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos96Zmanis(), $this->getTzais96Zmanis(), true);
	}

	public function getSofZmanTfilaMGA120Minutes(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos120Minutes(), $this->getTzais120Minutes(), true);
	}

	public function getSofZmanTfilaMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getSofZmanTfilaMGA18Degrees(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos18Degrees(), $this->getTzais18Degrees(), true);
	}

	public function getSofZmanTfilaMGA19Point8Degrees(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees(), true);
	}

	public function getSofZmanTfila2HoursBeforeChatzos(): Carbon|null
	{
		return $this->getTimeOffset($this->getChatzosHayom(), -2 * AstronomicalCalendar::HOUR_MILLIS);
	}

	public function getSofZmanTfilaGRASunriseToFixedLocalChatzos(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getElevationAdjustedSunrise(), $this->getFixedLocalChatzosHayom(), 4);
	}

	/*
	|--------------------------------------------------------------------------
	| MINCHA GEDOLA
	|--------------------------------------------------------------------------
	*/

	public function getMinchaGedola30Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getChatzosHayom(), 30 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getMinchaGedola72Minutes(): Carbon|null
	{
		if ($this->getUseAstronomicalChatzosForOtherZmanim()) {
			return $this->getHalfDayBasedZman($this->getChatzosHayom(), $this->getTzais72Minutes(), 0.5);
		}

		return $this->getMinchaGedola($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	public function getMinchaGedola16Point1Degrees(): Carbon|null
	{
		if ($this->getUseAstronomicalChatzosForOtherZmanim()) {
			return $this->getHalfDayBasedZman($this->getChatzosHayom(), $this->getTzais16Point1Degrees(), 0.5);
		}

		return $this->getMinchaGedola($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getMinchaGedolaAhavatShalom(): Carbon|null
	{
		$chatzos = $this->getChatzosHayom();
		$minchaGedola30 = $this->getMinchaGedola30Minutes();
		$shaahZmanis = $this->getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point7Degrees();
		if ($chatzos == null || $minchaGedola30 == null || $shaahZmanis == null) {
			return null;
		}

		$minchaGedolaAhavatShalom = $this->getTimeOffset($chatzos, $shaahZmanis / 2);

		return $minchaGedola30->gt($minchaGedolaAhavatShalom) ? $minchaGedola30 : $minchaGedolaAhavatShalom;
	}

	public function getMinchaGedolaGreaterThan30(?Carbon $minchaGedola): Carbon|null
	{
		$minchaGedola30 = $this->getMinchaGedola30Minutes();
		if ($minchaGedola30 == null || $minchaGedola == null) {
			return null;
		}

		return $minchaGedola30->gt($minchaGedola) ? $minchaGedola30 : $minchaGedola;
	}

	public function getMinchaGedolaGRAGreaterThan30(): Carbon|null
	{
		return $this->getMinchaGedolaGreaterThan30($this->getMinchaGedolaGRA());
	}

	public function getMinchaGedolaGRAFixedLocalChatzos30Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getFixedLocalChatzosHayom(), 30 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	/*
	|--------------------------------------------------------------------------
	| MINCHA KETANA
	|--------------------------------------------------------------------------
	*/

	public function getMinchaKetana16Point1Degrees(): Carbon|null
	{
		return $this->getMinchaKetana($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getMinchaKetana72Minutes(): Carbon|null
	{
		return $this->getMinchaKetana($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	public function getMinchaKetanaAhavatShalom(): Carbon|null
	{
		$shaahZmanis = $this->getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point8Degrees();
		if ($shaahZmanis == null) {
			return null;
		}

		return $this->getTimeOffset($this->getTzaisGeonim3Point8Degrees(), -$shaahZmanis * 2.5);
	}

	public function getMinchaKetanaGRAFixedLocalChatzosToSunset(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getFixedLocalChatzosHayom(), $this->getElevationAdjustedSunset(), 3.5);
	}

	/*
	|--------------------------------------------------------------------------
	| SAMUCH LEMINCHA KETANA
	|--------------------------------------------------------------------------
	*/

	public function getSamuchLeMinchaKetanaGRA(): Carbon|null
	{
		return $this->getSamuchLeMinchaKetana($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getSamuchLeMinchaKetana16Point1Degrees(): Carbon|null
	{
		return $this->getSamuchLeMinchaKetana($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getSamuchLeMinchaKetana72Minutes(): Carbon|null
	{
		return $this->getSamuchLeMinchaKetana($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	/*
	|--------------------------------------------------------------------------
	| PLAG HAMINCHA
	|--------------------------------------------------------------------------
	*/

	public function getPlagHamincha60Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos60Minutes(), $this->getTzais60Minutes(), true);
	}

	public function getPlagHamincha72Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	public function getPlagHamincha72MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos72Zmanis(), $this->getTzais72Zmanis(), true);
	}

	public function getPlagHamincha90Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos90Minutes(), $this->getTzais90Minutes(), true);
	}

	public function getPlagHamincha90MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos90Zmanis(), $this->getTzais90Zmanis(), true);
	}

	public function getPlagHamincha96Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos96Minutes(), $this->getTzais96Minutes(), true);
	}

	public function getPlagHamincha96MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos96Zmanis(), $this->getTzais96Zmanis(), true);
	}

	public function getPlagHamincha120Minutes(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos120Minutes(), $this->getTzais120Minutes(), true);
	}

	public function getPlagHamincha120MinutesZmanis(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos120Zmanis(), $this->getTzais120Zmanis(), true);
	}

	public function getPlagHamincha16Point1Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getPlagHamincha18Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos18Degrees(), $this->getTzais18Degrees(), true);
	}

	public function getPlagHamincha19Point8Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos19Point8Degrees(), $this->getTzais19Point8Degrees(), true);
	}

	public function getPlagHamincha26Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos26Degrees(), $this->getTzais26Degrees(), true);
	}

	public function getPlagAlosToSunset(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getElevationAdjustedSunset(), false);
	}

	public function getPlagAlos16Point1DegreesToTzaisGeonim7Point083Degrees(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos16Point1Degrees(), $this->getTzaisGeonim7Point083Degrees(), false);
	}

	public function getPlagAhavatShalom(): Carbon|null
	{
		$shaahZmanis = $this->getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point8Degrees();
		if ($shaahZmanis == null) {
			return null;
		}

		return $this->getTimeOffset($this->getTzaisGeonim3Point8Degrees(), -$shaahZmanis * 1.25);
	}

	public function getPlagHaminchaGRAFixedLocalChatzosToSunset(): Carbon|null
	{
		return $this->getHalfDayBasedZman($this->getFixedLocalChatzosHayom(), $this->getElevationAdjustedSunset(), 4.75);
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
		return $this->getTimeOffset($this->getTzaisGeonim7Point083Degrees(), -13.5 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getBainHashmashosRT2Stars(): Carbon|null
	{
		$alos19Point8 = $this->getAlos19Point8Degrees();
		$sunrise = $this->getElevationAdjustedSunrise();
		if ($alos19Point8 == null || $sunrise == null) {
			return null;
		}

		$alosToSunrise = ($sunrise->getPreciseTimestamp() - $alos19Point8->getPreciseTimestamp()) / 1000;

		return $this->getTimeOffset($this->getElevationAdjustedSunset(), $alosToSunrise * (5 / 18));
	}

	public function getBainHashmashosYereim18Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), -18 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getBainHashmashosYereim16Point875Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), -16.875 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getBainHashmashosYereim13Point5Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), -13.5 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getBainHashmashosYereim3Point05Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_MINUS_3_POINT_05);
	}

	public function getBainHashmashosYereim2Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_MINUS_2_POINT_8);
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

	public function getTzais72Minutes(): Carbon|null
	{
		return $this->getTzais72();
	}

	public function getTzaisGeonim8Point5Degrees(): Carbon|null
	{
		return $this->getTzais();
	}

	public function getTzais50Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 50 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getTzais60Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 60 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getTzais90Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 90 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getTzais96Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 96 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getTzais120Minutes(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), 120 * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getTzais72Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(1.2);
	}

	public function getTzais90Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(1.5);
	}

	public function getTzais96Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(1.6);
	}

	public function getTzais120Zmanis(): Carbon|null
	{
		return $this->getZmanisBasedOffset(2.0);
	}

	public function getTzais16Point1Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_16_POINT_1);
	}

	public function getTzais18Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(AstronomicalCalendar::ASTRONOMICAL_ZENITH);
	}

	public function getTzais19Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_19_POINT_8);
	}

	public function getTzais26Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_26_DEGREES);
	}

	public function getTzaisGeonim3Point7Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_3_POINT_7);
	}

	public function getTzaisGeonim3Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_3_POINT_8);
	}

	public function getTzaisGeonim4Point42Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_4_POINT_42);
	}

	public function getTzaisGeonim4Point66Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_4_POINT_66);
	}

	public function getTzaisGeonim4Point8Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_4_POINT_8);
	}

	public function getTzaisGeonim5Point95Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_5_POINT_95);
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

	public function getTzaisGeonim9Point3Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_9_POINT_3);
	}

	public function getTzaisGeonim9Point75Degrees(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_9_POINT_75);
	}

	/*
	|--------------------------------------------------------------------------
	| ATERET TORAH
	|--------------------------------------------------------------------------
	*/

	public function getTzaisAteretTorah(): Carbon|null
	{
		return $this->getTimeOffset($this->getElevationAdjustedSunset(), $this->getAteretTorahSunsetOffset() * AstronomicalCalendar::MINUTE_MILLIS);
	}

	public function getSofZmanShmaAteretTorah(): Carbon|null
	{
		return $this->getSofZmanShma($this->getAlos72Zmanis(), $this->getTzaisAteretTorah(), false);
	}

	public function getSofZmanTfilaAteretTorah(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getAlos72Zmanis(), $this->getTzaisAteretTorah(), false);
	}

	public function getMinchaGedolaAteretTorah(): Carbon|null
	{
		return $this->getMinchaGedola($this->getAlos72Zmanis(), $this->getTzaisAteretTorah(), false);
	}

	public function getMinchaKetanaAteretTorah(): Carbon|null
	{
		return $this->getMinchaKetana($this->getAlos72Zmanis(), $this->getTzaisAteretTorah(), false);
	}

	public function getPlagHaminchaAteretTorah(): Carbon|null
	{
		return $this->getPlagHamincha($this->getAlos72Zmanis(), $this->getTzaisAteretTorah(), false);
	}

	/*
	|--------------------------------------------------------------------------
	| FIXED LOCAL CHATZOS
	|--------------------------------------------------------------------------
	*/

	public function getFixedLocalChatzosHayom(): Carbon|null
	{
		return $this->getLocalMeanTime(12);
	}

	/*
	|--------------------------------------------------------------------------
	| CHAMETZ
	|--------------------------------------------------------------------------
	*/

	public function getSofZmanAchilasChametzGRA(): Carbon|null
	{
		return $this->getSofZmanAchilasChametz($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getSofZmanAchilasChametzMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanAchilasChametz($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	public function getSofZmanAchilasChametzMGA72MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanAchilasChametz($this->getAlos72Zmanis(), $this->getTzais72Zmanis(), true);
	}

	public function getSofZmanAchilasChametzMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanAchilasChametz($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	public function getSofZmanBiurChametzGRA(): Carbon|null
	{
		return $this->getSofZmanBiurChametz($this->getElevationAdjustedSunrise(), $this->getElevationAdjustedSunset(), true);
	}

	public function getSofZmanBiurChametzMGA72Minutes(): Carbon|null
	{
		return $this->getSofZmanBiurChametz($this->getAlos72Minutes(), $this->getTzais72Minutes(), true);
	}

	public function getSofZmanBiurChametzMGA72MinutesZmanis(): Carbon|null
	{
		return $this->getSofZmanBiurChametz($this->getAlos72Zmanis(), $this->getTzais72Zmanis(), true);
	}

	public function getSofZmanBiurChametzMGA16Point1Degrees(): Carbon|null
	{
		return $this->getSofZmanBiurChametz($this->getAlos16Point1Degrees(), $this->getTzais16Point1Degrees(), true);
	}

	/*
	|--------------------------------------------------------------------------
	| BAAL HATANYA
	|--------------------------------------------------------------------------
	*/

	public function getAlosBaalHatanya(): Carbon|null
	{
		return $this->getSunriseOffsetByDegrees(self::ZENITH_16_POINT_9);
	}

	public function getSofZmanShmaBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanShma($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getSofZmanTfilaBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanTfila($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getSofZmanAchilasChametzBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanAchilasChametz($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getSofZmanBiurChametzBaalHatanya(): Carbon|null
	{
		return $this->getSofZmanBiurChametz($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getMinchaGedolaBaalHatanya(): Carbon|null
	{
		return $this->getMinchaGedola($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getMinchaKetanaBaalHatanya(): Carbon|null
	{
		return $this->getMinchaKetana($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getPlagHaminchaBaalHatanya(): Carbon|null
	{
		return $this->getPlagHamincha($this->getSunriseBaalHatanya(), $this->getSunsetBaalHatanya(), true);
	}

	public function getTzaisBaalHatanya(): Carbon|null
	{
		return $this->getSunsetOffsetByDegrees(self::ZENITH_6_DEGREES);
	}

	/*
	|--------------------------------------------------------------------------
	| KIDUSH LEVANA / MOLAD
	|--------------------------------------------------------------------------
	*/

	public function getSofZmanKidushLevanaBetweenMoldos(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = $this->jewishCalendar();
		if ($jewishCalendar->getJewishDayOfMonth() < 11 || $jewishCalendar->getJewishDayOfMonth() > 16) {
			return null;
		}

		return $this->getMoladBasedTime($jewishCalendar->getSofZmanKidushLevanaBetweenMoldos(), $alos, $tzais, false);
	}

	public function getSofZmanKidushLevana15Days(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = $this->jewishCalendar();
		if ($jewishCalendar->getJewishDayOfMonth() < 11 || $jewishCalendar->getJewishDayOfMonth() > 17) {
			return null;
		}

		return $this->getMoladBasedTime($jewishCalendar->getSofZmanKidushLevana15Days(), $alos, $tzais, false);
	}

	public function getTchilasZmanKidushLevana3Days(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = $this->jewishCalendar();
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

	public function getTchilasZmanKidushLevana7Days(?Carbon $alos = null, ?Carbon $tzais = null): Carbon|null
	{
		$jewishCalendar = $this->jewishCalendar();
		if ($jewishCalendar->getJewishDayOfMonth() < 4 || $jewishCalendar->getJewishDayOfMonth() > 9) {
			return null;
		}

		return $this->getMoladBasedTime($jewishCalendar->getTchilasZmanKidushLevana7Days(), $alos, $tzais, true);
	}

	public function getZmanMolad(): Carbon|null
	{
		$jewishCalendar = $this->jewishCalendar();
		if ($jewishCalendar->getJewishDayOfMonth() > 2 && $jewishCalendar->getJewishDayOfMonth() < 27) {
			return null;
		}

		$molad = $this->getMoladBasedTime($jewishCalendar->getMoladAsDate(), null, null, true);
		if ($molad == null && $jewishCalendar->getJewishDayOfMonth() > 26) {
			$jewishCalendar->addMonthsJewish(1);
			$molad = $this->getMoladBasedTime($jewishCalendar->getMoladAsDate(), null, null, true);
		}

		return $molad;
	}

	/*
	|--------------------------------------------------------------------------
	| POLAR
	|--------------------------------------------------------------------------
	*/

	public function getPolarSunriseBenIshChai(): Carbon|null
	{
		if ($this->getElevationAdjustedSunrise() == null) {
			return $this->getTimeAtAzimuth90Or270(90);
		}

		return null;
	}

	public function getPolarSunsetBenIshChai(): Carbon|null
	{
		if ($this->getElevationAdjustedSunset() == null) {
			return $this->getTimeAtAzimuth90Or270(270);
		}

		return null;
	}

	public function getPolarPlagHaminchaBenIshChai(): Carbon|null
	{
		return $this->getPlagHamincha($this->getPolarSunriseBenIshChai(), $this->getPolarSunsetBenIshChai(), true);
	}

	public function getPolarStartOfDayTeshuvosVehanhagos(): Carbon|null
	{
		if ($this->getElevationAdjustedSunrise() != null || $this->getElevationAdjustedSunset() != null) {
			return null;
		}

		$chatzosHayom = $this->getChatzosHayom();
		$chatzosHalayla = $this->getChatzosHalayla();
		$calculator = $this->getAstronomicalCalculator();
		$chatzosHayomSolarElevation = $calculator->getSolarElevation($chatzosHayom, $this->getGeoLocation());
		$chatzosHalaylaSolarElevation = $calculator->getSolarElevation($chatzosHalayla, $this->getGeoLocation());
		$sunriseElevation = $calculator->getSolarRadius() + $calculator->getRefraction();

		if ($chatzosHayomSolarElevation < (0 - $sunriseElevation) && $chatzosHalaylaSolarElevation < (0 - $sunriseElevation)
				&& $this->getAlos16Point1Degrees() == null && $this->getElevationAdjustedSunrise() == null) {
			return $chatzosHayom;
		}

		if ($chatzosHayomSolarElevation > (0 - $sunriseElevation) && $chatzosHalaylaSolarElevation > (0 - $sunriseElevation)) {
			return $chatzosHalayla;
		}

		return null;
	}

	public function getPolarPlagHaminchaTeshuvosVehanhagos(): Carbon|null
	{
		$polarStartOfDay = $this->getPolarStartOfDayTeshuvosVehanhagos();
		if ($polarStartOfDay == null) {
			return null;
		}

		$yesterday = $polarStartOfDay->copy()->subDay();

		return $this->getPlagHamincha($yesterday, $polarStartOfDay, true);
	}

	/*
	|--------------------------------------------------------------------------
	| CALCULATIONS
	|--------------------------------------------------------------------------
	*/

	private function temporalHourOrNull(?Carbon $startOfDay, ?Carbon $endOfDay): float|null
	{
		if ($startOfDay == null || $endOfDay == null) {
			return null;
		}

		return $this->getTemporalHour($startOfDay, $endOfDay);
	}

	private function jewishCalendar(): JewishCalendar
	{
		$jewishCalendar = new JewishCalendar();
		$jewishCalendar->setGregorianDate($this->date->year, $this->date->month, $this->date->day);

		return $jewishCalendar;
	}

	private function getMoladBasedTime(?Carbon $moladBasedTime, ?Carbon $alos, ?Carbon $tzais, bool $techila): Carbon|null
	{
		if ($moladBasedTime == null) {
			return null;
		}

		$lastMidnight = $this->getMidnightLastNight();
		$midnightTonight = $this->getMidnightTonight();
		if ($moladBasedTime->lt($lastMidnight) || $moladBasedTime->gt($midnightTonight)) {
			return null;
		}

		if ($alos == null || $tzais == null) {
			return $moladBasedTime;
		}

		if ($moladBasedTime->gt($alos) && $moladBasedTime->lt($tzais)) {
			return $techila ? $tzais : $alos;
		}

		return $moladBasedTime;
	}
}
