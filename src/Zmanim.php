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

namespace PhpZmanim;

use PhpZmanim\Calendar\ComplexZmanimCalendar;
use PhpZmanim\Geo\GeoLocation;

/**
 * This class is meant to be a wrapper of ComplexZmanimCalendar. You can extend
 * this class to include any Zmanim calculations that aren't included in
 * AstronomicalCalendar, ZmanimCalendar, or ComplexZmanimCalendar
 *
 * @property-read \Carbon\Carbon|null $sunrise
 * @property-read \Carbon\Carbon|null $seaLevelSunrise
 * @property-read \Carbon\Carbon|null $beginCivilTwilight
 * @property-read \Carbon\Carbon|null $beginNauticalTwilight
 * @property-read \Carbon\Carbon|null $beginAstronomicalTwilight
 * @property-read \Carbon\Carbon|null $sunset
 * @property-read \Carbon\Carbon|null $seaLevelSunset
 * @property-read \Carbon\Carbon|null $endCivilTwilight
 * @property-read \Carbon\Carbon|null $endNauticalTwilight
 * @property-read \Carbon\Carbon|null $endAstronomicalTwilight
 * @property-read \Carbon\Carbon|null $shaahZmanis19Point8Degrees
 * @property-read \Carbon\Carbon|null $shaahZmanis18Degrees
 * @property-read \Carbon\Carbon|null $shaahZmanis26Degrees
 * @property-read \Carbon\Carbon|null $shaahZmanis16Point1Degrees
 * @property-read \Carbon\Carbon|null $shaahZmanis60Minutes
 * @property-read \Carbon\Carbon|null $shaahZmanis72Minutes
 * @property-read \Carbon\Carbon|null $shaahZmanis72MinutesZmanis
 * @property-read \Carbon\Carbon|null $shaahZmanis90Minutes
 * @property-read \Carbon\Carbon|null $shaahZmanis90MinutesZmanis
 * @property-read \Carbon\Carbon|null $shaahZmanis96MinutesZmanis
 * @property-read \Carbon\Carbon|null $shaahZmanisAteretTorah
 * @property-read \Carbon\Carbon|null $shaahZmanis96Minutes
 * @property-read \Carbon\Carbon|null $shaahZmanis120Minutes
 * @property-read \Carbon\Carbon|null $shaahZmanis120MinutesZmanis
 * @property-read \Carbon\Carbon|null $shaahZmanisBaalHatanya
 * @property-read \Carbon\Carbon|null $shaahZmanisGra
 * @property-read \Carbon\Carbon|null $shaahZmanisMGA
 * @property-read \Carbon\Carbon|null $alosHashachar
 * @property-read \Carbon\Carbon|null $alos72
 * @property-read \Carbon\Carbon|null $alos60
 * @property-read \Carbon\Carbon|null $alos72Zmanis
 * @property-read \Carbon\Carbon|null $alos96
 * @property-read \Carbon\Carbon|null $alos90Zmanis
 * @property-read \Carbon\Carbon|null $alos96Zmanis
 * @property-read \Carbon\Carbon|null $alos90
 * @property-read \Carbon\Carbon|null $alos120
 * @property-read \Carbon\Carbon|null $alos120Zmanis
 * @property-read \Carbon\Carbon|null $alos26Degrees
 * @property-read \Carbon\Carbon|null $alos18Degrees
 * @property-read \Carbon\Carbon|null $alos19Degrees
 * @property-read \Carbon\Carbon|null $alos19Point8Degrees
 * @property-read \Carbon\Carbon|null $alos16Point1Degrees
 * @property-read \Carbon\Carbon|null $alosBaalHatanya
 * @property-read \Carbon\Carbon|null $misheyakir11Point5Degrees
 * @property-read \Carbon\Carbon|null $misheyakir11Degrees
 * @property-read \Carbon\Carbon|null $misheyakir10Point2Degrees
 * @property-read \Carbon\Carbon|null $misheyakir7Point65Degrees
 * @property-read \Carbon\Carbon|null $misheyakir9Point5Degrees
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA
 * @property-read \Carbon\Carbon|null $sofZmanShmaGra
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA19Point8Degrees
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA16Point1Degrees
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA18Degrees
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA72Minutes
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA72MinutesZmanis
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA90Minutes
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA90MinutesZmanis
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA96Minutes
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA96MinutesZmanis
 * @property-read \Carbon\Carbon|null $sofZmanShma3HoursBeforeChatzos
 * @property-read \Carbon\Carbon|null $sofZmanShmaMGA120Minutes
 * @property-read \Carbon\Carbon|null $sofZmanShmaAlos16Point1ToSunset
 * @property-read \Carbon\Carbon|null $sofZmanShmaAlos16Point1ToTzaisGeonim7Point083Degrees
 * @property-read \Carbon\Carbon|null $sofZmanShmaAteretTorah
 * @property-read \Carbon\Carbon|null $sofZmanShmaFixedLocal
 * @property-read \Carbon\Carbon|null $sofZmanShmaBaalHatanya
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA
 * @property-read \Carbon\Carbon|null $sofZmanTfilaGra
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA19Point8Degrees
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA16Point1Degrees
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA18Degrees
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA72Minutes
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA72MinutesZmanis
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA90Minutes
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA90MinutesZmanis
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA96Minutes
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA96MinutesZmanis
 * @property-read \Carbon\Carbon|null $sofZmanTfilaMGA120Minutes
 * @property-read \Carbon\Carbon|null $sofZmanTfila2HoursBeforeChatzos
 * @property-read \Carbon\Carbon|null $sofZmanTfilahAteretTorah
 * @property-read \Carbon\Carbon|null $sofZmanTfilaFixedLocal
 * @property-read \Carbon\Carbon|null $sofZmanTfilaBaalHatanya
 * @property-read \Carbon\Carbon|null $sofZmanAchilasChametzGRA
 * @property-read \Carbon\Carbon|null $sofZmanAchilasChametzMGA72Minutes
 * @property-read \Carbon\Carbon|null $sofZmanAchilasChametzMGA16Point1Degrees
 * @property-read \Carbon\Carbon|null $sofZmanAchilasChametzBaalHatanya
 * @property-read \Carbon\Carbon|null $sofZmanBiurChametzGRA
 * @property-read \Carbon\Carbon|null $sofZmanBiurChametzMGA72Minutes
 * @property-read \Carbon\Carbon|null $sofZmanBiurChametzMGA16Point1Degrees
 * @property-read \Carbon\Carbon|null $sofZmanBiurChametzBaalHatanya
 * @property-read \Carbon\Carbon|null $chatzos
 * @property-read \Carbon\Carbon|null $fixedLocalChatzos
 * @property-read \Carbon\Carbon|null $minchaGedolaGra
 * @property-read \Carbon\Carbon|null $minchaGedola30Minutes
 * @property-read \Carbon\Carbon|null $minchaGedola72Minutes
 * @property-read \Carbon\Carbon|null $minchaGedola16Point1Degrees
 * @property-read \Carbon\Carbon|null $minchaGedolaGreaterThan30
 * @property-read \Carbon\Carbon|null $minchaGedolaAteretTorah
 * @property-read \Carbon\Carbon|null $minchaGedolaBaalHatanya
 * @property-read \Carbon\Carbon|null $minchaGedolaBaalHatanyaGreaterThan30
 * @property-read \Carbon\Carbon|null $minchaKetanaGra
 * @property-read \Carbon\Carbon|null $minchaKetana16Point1Degrees
 * @property-read \Carbon\Carbon|null $minchaKetana72Minutes
 * @property-read \Carbon\Carbon|null $minchaKetanaAteretTorah
 * @property-read \Carbon\Carbon|null $minchaKetanaBaalHatanya
 * @property-read \Carbon\Carbon|null $plagHaminchaGra
 * @property-read \Carbon\Carbon|null $plagHamincha120MinutesZmanis
 * @property-read \Carbon\Carbon|null $plagHamincha120Minutes
 * @property-read \Carbon\Carbon|null $plagHamincha60Minutes
 * @property-read \Carbon\Carbon|null $plagHamincha72Minutes
 * @property-read \Carbon\Carbon|null $plagHamincha90Minutes
 * @property-read \Carbon\Carbon|null $plagHamincha96Minutes
 * @property-read \Carbon\Carbon|null $plagHamincha96MinutesZmanis
 * @property-read \Carbon\Carbon|null $plagHamincha90MinutesZmanis
 * @property-read \Carbon\Carbon|null $plagHamincha72MinutesZmanis
 * @property-read \Carbon\Carbon|null $plagHamincha16Point1Degrees
 * @property-read \Carbon\Carbon|null $plagHamincha19Point8Degrees
 * @property-read \Carbon\Carbon|null $plagHamincha26Degrees
 * @property-read \Carbon\Carbon|null $plagHamincha18Degrees
 * @property-read \Carbon\Carbon|null $plagAlosToSunset
 * @property-read \Carbon\Carbon|null $plagAlos16Point1ToTzaisGeonim7Point083Degrees
 * @property-read \Carbon\Carbon|null $plagHaminchaAteretTorah
 * @property-read \Carbon\Carbon|null $plagHaminchaBaalHatanya
 * @property-read \Carbon\Carbon|null $candleLighting
 * @property-read \Carbon\Carbon|null $bainHasmashosRT13Point24Degrees
 * @property-read \Carbon\Carbon|null $bainHasmashosRT58Point5Minutes
 * @property-read \Carbon\Carbon|null $bainHasmashosRT13Point5MinutesBefore7Point083Degrees
 * @property-read \Carbon\Carbon|null $bainHasmashosRT2Stars
 * @property-read \Carbon\Carbon|null $tzais
 * @property-read \Carbon\Carbon|null $tzais72
 * @property-read \Carbon\Carbon|null $tzaisGeonim3Point7Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim3Point8Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim5Point95Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim3Point65Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim3Point676Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim4Point61Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim4Point37Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim5Point88Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim4Point8Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim6Point45Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim7Point083Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim7Point67Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim8Point5Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim9Point3Degrees
 * @property-read \Carbon\Carbon|null $tzaisGeonim9Point75Degrees
 * @property-read \Carbon\Carbon|null $tzais60
 * @property-read \Carbon\Carbon|null $tzaisAteretTorah
 * @property-read \Carbon\Carbon|null $tzais72Zmanis
 * @property-read \Carbon\Carbon|null $tzais90Zmanis
 * @property-read \Carbon\Carbon|null $tzais96Zmanis
 * @property-read \Carbon\Carbon|null $tzais90
 * @property-read \Carbon\Carbon|null $tzais120
 * @property-read \Carbon\Carbon|null $tzais120Zmanis
 * @property-read \Carbon\Carbon|null $tzais16Point1Degrees
 * @property-read \Carbon\Carbon|null $tzais26Degrees
 * @property-read \Carbon\Carbon|null $tzais18Degrees
 * @property-read \Carbon\Carbon|null $tzais19Point8Degrees
 * @property-read \Carbon\Carbon|null $tzais96
 * @property-read \Carbon\Carbon|null $tzaisBaalHatanya
 * @property-read \Carbon\Carbon|null $solarMidnight
 */
class Zmanim extends ComplexZmanimCalendar {
	public static function create($year = null, $month = null, $day = null, $locationName = null,
		$latitude = 51.4772, $longitude = 0.0, $elevation = 0.0, $timeZone = "GMT") {
		$geoLocation = new GeoLocation($locationName, $latitude, $longitude, $elevation, $timeZone);

		return new Zmanim($geoLocation, $year, $month, $day);
	}
}