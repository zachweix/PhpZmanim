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

namespace PhpZmanim;

use PhpZmanim\Calendar\ComplexZmanimCalendar;
use PhpZmanim\Geo\GeoLocation;

/**
 * This class is meant to be a wrapper of ComplexZmanimCalendar. You can extend
 * this class to include any Zmanim calculations that aren't included in
 * AstronomicalCalendar, ZmanimCalendar, or ComplexZmanimCalendar
 *
 * @property-read \Carbon\Carbon|null $Sunrise
 * @property-read \Carbon\Carbon|null $SeaLevelSunrise
 * @property-read \Carbon\Carbon|null $BeginCivilTwilight
 * @property-read \Carbon\Carbon|null $BeginNauticalTwilight
 * @property-read \Carbon\Carbon|null $BeginAstronomicalTwilight
 * @property-read \Carbon\Carbon|null $Sunset
 * @property-read \Carbon\Carbon|null $SeaLevelSunset
 * @property-read \Carbon\Carbon|null $EndCivilTwilight
 * @property-read \Carbon\Carbon|null $EndNauticalTwilight
 * @property-read \Carbon\Carbon|null $EndAstronomicalTwilight
 * @property-read \Carbon\Carbon|null $ShaahZmanis19Point8Degrees
 * @property-read \Carbon\Carbon|null $ShaahZmanis18Degrees
 * @property-read \Carbon\Carbon|null $ShaahZmanis26Degrees
 * @property-read \Carbon\Carbon|null $ShaahZmanis16Point1Degrees
 * @property-read \Carbon\Carbon|null $ShaahZmanis60Minutes
 * @property-read \Carbon\Carbon|null $ShaahZmanis72Minutes
 * @property-read \Carbon\Carbon|null $ShaahZmanis72MinutesZmanis
 * @property-read \Carbon\Carbon|null $ShaahZmanis90Minutes
 * @property-read \Carbon\Carbon|null $ShaahZmanis90MinutesZmanis
 * @property-read \Carbon\Carbon|null $ShaahZmanis96MinutesZmanis
 * @property-read \Carbon\Carbon|null $ShaahZmanisAteretTorah
 * @property-read \Carbon\Carbon|null $ShaahZmanis96Minutes
 * @property-read \Carbon\Carbon|null $ShaahZmanis120Minutes
 * @property-read \Carbon\Carbon|null $ShaahZmanis120MinutesZmanis
 * @property-read \Carbon\Carbon|null $ShaahZmanisBaalHatanya
 * @property-read \Carbon\Carbon|null $AlosHashachar
 * @property-read \Carbon\Carbon|null $Alos72
 * @property-read \Carbon\Carbon|null $Alos60
 * @property-read \Carbon\Carbon|null $Alos72Zmanis
 * @property-read \Carbon\Carbon|null $Alos96
 * @property-read \Carbon\Carbon|null $Alos90Zmanis
 * @property-read \Carbon\Carbon|null $Alos96Zmanis
 * @property-read \Carbon\Carbon|null $Alos90
 * @property-read \Carbon\Carbon|null $Alos120
 * @property-read \Carbon\Carbon|null $Alos120Zmanis
 * @property-read \Carbon\Carbon|null $Alos26Degrees
 * @property-read \Carbon\Carbon|null $Alos18Degrees
 * @property-read \Carbon\Carbon|null $Alos19Degrees
 * @property-read \Carbon\Carbon|null $Alos19Point8Degrees
 * @property-read \Carbon\Carbon|null $Alos16Point1Degrees
 * @property-read \Carbon\Carbon|null $AlosBaalHatanya
 * @property-read \Carbon\Carbon|null $Misheyakir11Point5Degrees
 * @property-read \Carbon\Carbon|null $Misheyakir11Degrees
 * @property-read \Carbon\Carbon|null $Misheyakir10Point2Degrees
 * @property-read \Carbon\Carbon|null $Misheyakir7Point65Degrees
 * @property-read \Carbon\Carbon|null $Misheyakir9Point5Degrees
 * @property-read \Carbon\Carbon|null $SofZmanShmaMA
 * @property-read \Carbon\Carbon|null $SofZmanShmaGra
 * @property-read \Carbon\Carbon|null $SofZmanShmaMGA19Point8Degrees
 * @property-read \Carbon\Carbon|null $SofZmanShmaMGA16Point1Degrees
 * @property-read \Carbon\Carbon|null $SofZmanShmaMGA18Degrees
 * @property-read \Carbon\Carbon|null $SofZmanShmaMGA72Minutes
 * @property-read \Carbon\Carbon|null $SofZmanShmaMGA72MinutesZmanis
 * @property-read \Carbon\Carbon|null $SofZmanShmaMGA90Minutes
 * @property-read \Carbon\Carbon|null $SofZmanShmaMGA90MinutesZmanis
 * @property-read \Carbon\Carbon|null $SofZmanShmaMGA96Minutes
 * @property-read \Carbon\Carbon|null $SofZmanShmaMGA96MinutesZmanis
 * @property-read \Carbon\Carbon|null $SofZmanShma3HoursBeforeChatzos
 * @property-read \Carbon\Carbon|null $SofZmanShmaMGA120Minutes
 * @property-read \Carbon\Carbon|null $SofZmanShmaAlos16Point1ToSunset
 * @property-read \Carbon\Carbon|null $SofZmanShmaAlos16Point1ToTzaisGeonim7Point083Degrees
 * @property-read \Carbon\Carbon|null $SofZmanShmaAteretTorah
 * @property-read \Carbon\Carbon|null $SofZmanShmaFixedLocal
 * @property-read \Carbon\Carbon|null $SofZmanShmaBaalHatanya
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMA
 * @property-read \Carbon\Carbon|null $SofZmanTfilaGra
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMGA19Point8Degrees
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMGA16Point1Degrees
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMGA18Degrees
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMGA72Minutes
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMGA72MinutesZmanis
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMGA90Minutes
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMGA90MinutesZmanis
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMGA96Minutes
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMGA96MinutesZmanis
 * @property-read \Carbon\Carbon|null $SofZmanTfilaMGA120Minutes
 * @property-read \Carbon\Carbon|null $SofZmanTfila2HoursBeforeChatzos
 * @property-read \Carbon\Carbon|null $SofZmanTfilahAteretTorah
 * @property-read \Carbon\Carbon|null $SofZmanTfilaFixedLocal
 * @property-read \Carbon\Carbon|null $SofZmanTfilaBaalHatanya
 * @property-read \Carbon\Carbon|null $SofZmanAchilasChametzGRA
 * @property-read \Carbon\Carbon|null $SofZmanAchilasChametzMGA72Minutes
 * @property-read \Carbon\Carbon|null $SofZmanAchilasChametzMGA16Point1Degrees
 * @property-read \Carbon\Carbon|null $SofZmanAchilasChametzBaalHatanya
 * @property-read \Carbon\Carbon|null $SofZmanBiurChametzGRA
 * @property-read \Carbon\Carbon|null $SofZmanBiurChametzMGA72Minutes
 * @property-read \Carbon\Carbon|null $SofZmanBiurChametzMGA16Point1Degrees
 * @property-read \Carbon\Carbon|null $SofZmanBiurChametzBaalHatanya
 * @property-read \Carbon\Carbon|null $Chatzos
 * @property-read \Carbon\Carbon|null $FixedLocalChatzos
 * @property-read \Carbon\Carbon|null $MinchaGedolaGra
 * @property-read \Carbon\Carbon|null $MinchaGedola30Minutes
 * @property-read \Carbon\Carbon|null $MinchaGedola72Minutes
 * @property-read \Carbon\Carbon|null $MinchaGedola16Point1Degrees
 * @property-read \Carbon\Carbon|null $MinchaGedolaGreaterThan30
 * @property-read \Carbon\Carbon|null $MinchaGedolaAteretTorah
 * @property-read \Carbon\Carbon|null $MinchaGedolaBaalHatanya
 * @property-read \Carbon\Carbon|null $MinchaGedolaBaalHatanyaGreaterThan30
 * @property-read \Carbon\Carbon|null $MinchaKetanaGra
 * @property-read \Carbon\Carbon|null $MinchaKetana16Point1Degrees
 * @property-read \Carbon\Carbon|null $MinchaKetana72Minutes
 * @property-read \Carbon\Carbon|null $MinchaKetanaAteretTorah
 * @property-read \Carbon\Carbon|null $MinchaKetanaBaalHatanya
 * @property-read \Carbon\Carbon|null $PlagHaminchaGra
 * @property-read \Carbon\Carbon|null $PlagHamincha120MinutesZmanis
 * @property-read \Carbon\Carbon|null $PlagHamincha120Minutes
 * @property-read \Carbon\Carbon|null $PlagHamincha60Minutes
 * @property-read \Carbon\Carbon|null $PlagHamincha72Minutes
 * @property-read \Carbon\Carbon|null $PlagHamincha90Minutes
 * @property-read \Carbon\Carbon|null $PlagHamincha96Minutes
 * @property-read \Carbon\Carbon|null $PlagHamincha96MinutesZmanis
 * @property-read \Carbon\Carbon|null $PlagHamincha90MinutesZmanis
 * @property-read \Carbon\Carbon|null $PlagHamincha72MinutesZmanis
 * @property-read \Carbon\Carbon|null $PlagHamincha16Point1Degrees
 * @property-read \Carbon\Carbon|null $PlagHamincha19Point8Degrees
 * @property-read \Carbon\Carbon|null $PlagHamincha26Degrees
 * @property-read \Carbon\Carbon|null $PlagHamincha18Degrees
 * @property-read \Carbon\Carbon|null $PlagAlosToSunset
 * @property-read \Carbon\Carbon|null $PlagAlos16Point1ToTzaisGeonim7Point083Degrees
 * @property-read \Carbon\Carbon|null $PlagHaminchaAteretTorah
 * @property-read \Carbon\Carbon|null $PlagHaminchaBaalHatanya
 * @property-read \Carbon\Carbon|null $CandleLighting
 * @property-read \Carbon\Carbon|null $BainHasmashosRT13Point24Degrees
 * @property-read \Carbon\Carbon|null $BainHasmashosRT58Point5Minutes
 * @property-read \Carbon\Carbon|null $BainHasmashosRT13Point5MinutesBefore7Point083Degrees
 * @property-read \Carbon\Carbon|null $BainHasmashosRT2Stars
 * @property-read \Carbon\Carbon|null $Tzais
 * @property-read \Carbon\Carbon|null $Tzais72
 * @property-read \Carbon\Carbon|null $TzaisGeonim3Point7Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim3Point8Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim5Point95Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim3Point65Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim3Point676Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim4Point61Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim4Point37Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim5Point88Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim4Point8Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim6Point45Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim7Point083Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim7Point67Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim8Point5Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim9Point3Degrees
 * @property-read \Carbon\Carbon|null $TzaisGeonim9Point75Degrees
 * @property-read \Carbon\Carbon|null $Tzais60
 * @property-read \Carbon\Carbon|null $TzaisAteretTorah
 * @property-read \Carbon\Carbon|null $Tzais72Zmanis
 * @property-read \Carbon\Carbon|null $Tzais90Zmanis
 * @property-read \Carbon\Carbon|null $Tzais96Zmanis
 * @property-read \Carbon\Carbon|null $Tzais90
 * @property-read \Carbon\Carbon|null $Tzais120
 * @property-read \Carbon\Carbon|null $Tzais120Zmanis
 * @property-read \Carbon\Carbon|null $Tzais16Point1Degrees
 * @property-read \Carbon\Carbon|null $Tzais26Degrees
 * @property-read \Carbon\Carbon|null $Tzais18Degrees
 * @property-read \Carbon\Carbon|null $Tzais19Point8Degrees
 * @property-read \Carbon\Carbon|null $Tzais96
 * @property-read \Carbon\Carbon|null $TzaisBaalHatanya
 * @property-read \Carbon\Carbon|null $SolarMidnight
 */
class Zmanim extends ComplexZmanimCalendar {
	public static function create($year = null, $month = null, $day = null, $locationName = null,
		$latitude = 51.4772, $longitude = 0.0, $elevation = 0.0, $timeZone = "GMT") {
		$geoLocation = new GeoLocation($locationName, $latitude, $longitude, $elevation, $timeZone);

		return new Zmanim($geoLocation, $year, $month, $day);
	}
}