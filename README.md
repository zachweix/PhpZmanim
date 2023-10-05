# PhpZmanim
A PHP port of the [KosherJava Zmanim API](https://kosherjava.com) from Eliyahu Hershfeld (code at the [KosherJava Zmanim project](https://github.com/KosherJava/zmanim)). See Kosher Java documentation for comments for every class variable and method. See below for how to install and a more detailed list of what you can access and methods you can call. Once instantiated, you can ask for many Zmanim right out of the gate:

```php
$zmanim = Zmanim::create(2019, 2, 22, 'Lakewood', 40.0721087, -74.2400243, 39.57, 'America/New_York');
$zmanim->tzais72->format('Y-m-d\TH:i:sP'); // 2019-02-22T18:52:38-05:00

$jewishCalendar = Zmanim::jewishCalendar(Carbon::createFromDate(2023, 9, 30)); // This will give you a Jewish calendar date for the given date
$jewishCalendar = Zmanim::jewishCalendar(5784, 7, 15); // This will give the same date, but with the Jewish date given as parameters
$jewishCalendar->isRoshHashana(); // false
$jewishCalendar->isSuccos(); // true

$daf = $jewishCalendar->getDafYomiBavli();
$format = Zmanim::format();
$format->formatDafYomiBavli($daf); // Kiddushin 48

$jewishCalendar = Zmanim::jewishCalendar(5784, 7, 26);
$format->formatParsha($jewishCalendar); // Bereshis
```

## Installation (with Composer)

```
$ composer require zachweix/php-zmanim
```

```json
{
    "require": {
        "zachweix/php-zmanim": "^2.0"
    }
}
```

## Setup

```php
<?php
require 'vendor/autoload.php';

use PhpZmanim\Zmanim;
```

## Instantiation

You can instantiate a new `Zmanim` object with the `Zmanim::create()` function. (If you want, you can instantiate by creating a `GeoLocation` object and a `ComplexZmanimCalendar` object like in KosherJava). There are currently two ways to calculate the Zmanim times, SunTimesCalculator and NoaaCalculator; the default calculator is NoaaCalculator.

### Via `Zmanim::create()`

The expected arguments are:
`$year`, `$month`, `$day`, `$locationName`, `$latitude`, `$longitude`, `$elevation`, `$timeZone`

All arguments are optional. The default values are today in Greenwich Mean Time at elevation 0. As you can see below, these are how you would set the location to either the default location, or a custom location. The location name is not used anywhere, and is only for your own internal reference (it can be null if you want).

If you give `null` as the year, month or day, today's UTC date will be used, which may be different than your current date (e.g. 9:00PM in New York on February 21 is 2:00AM in UTC on February 22).

```php
$zmanimInGMT = Zmanim::create(2019, 2, 21, "Greenwich");
$zmanim = Zmanim::create(2019, 2, 21, "New York City", 40.850519, -73.929214, 200, "America/New_York");
```

### Via `GeoLocation` and `ComplexZmanimCalendar`

`GeoLocation`'s expected arguments are:
`$locationName`, `$latitude`, `$longitude`, `$elevation`, `$timeZone`

`ComplexZmanimCalendar`'s expected arguments are:
`$geoLocation`, `$year`, `$month`, `$day`

If you give `null` as the year, month or day, today's UTC date will be used, which may be different than your current date (e.g. 9:00PM in New York on February 21 is 2:00AM in UTC on February 22).

```php
use PhpZmanim\Calendar\ComplexZmanimCalendar;
use PhpZmanim\Geo\GeoLocation;

$geoLocation = new GeoLocation("New York City", 40.850519, -73.929214, 200, "America/New_York");
$complexZmanimCalendar = new ComplexZmanimCalendar($geoLocation, 2019, 2, 21);
```

## Usage

Any parameter called like `$zmanim->sunrise` or a method name you see that is called like `$zmanim->get("Sunrise")` can be called by concatenating `get` to the zman and making sure to capitalize the first letter, so you would get `$zmanim->getSunrise()`; the following three will return identical results, all of which are a Carbon object.

```php
$sunrise = $zmanim->sunrise;
$sunrise = $zmanim->get("Sunrise");
$sunrise = $zmanim->getSunrise();
```

If you want to factor elevation when calculating, make sure to set the elevation when instantiating the object. If you set an elevation other than 0 and want to ignore elevation, you can call `$zmanim->setUseElevation(false)` and then when you want to use it again, simply call `$zmanim->setUseElevation(true)`.

### Available Methods

```php
$zmanim->setCalculatorType($type);     // 'SunTimes' and 'Noaa' are currently the only calculators
$zmanim->setDate($year, $month, $day); // Change current date
$zmanim->addDays($value);              // Change current date, by adding requested number of days
$zmanim->subDays($value);              // Change current date, by subtracting requested number of days
```

### List of Zmanim

Here is a list of many possible Zmanim you can request, all of them will return a Carbon object in the timezone set when creating the `$zmanim` object. If you don't find what you are looking for, see below for how to get more custom Zmanim.

#### Sunrise:
```php
$zmanim->sunrise;                    // Get sunrise based on current elevation
$zmanim->seaLevelSunrise;            // Get sunrise at zero elevation
$zmanim->beginCivilTwilight;         // The point when sun's zenith is at 96 degrees
$zmanim->beginNauticalTwilight;      // The point when sun's zenith is at 102 degrees
$zmanim->beginAstronomicalTwilight;  // The point when sun's zenith is at 108 degrees
```

#### Sunset
```php
$zmanim->sunset;                     // Sunset based on current elevation
$zmanim->seaLevelSunset;             // Sunset at zero elevation
$zmanim->endCivilTwilight;           // The point when sun's zenith is at 96 degrees
$zmanim->endNauticalTwilight;        // The point when sun's zenith is at 102 degrees
$zmanim->endAstronomicalTwilight;    // The point when sun's zenith is at 108 degrees
```

#### Length of Shaah Zmanim (in minutes)

```php
$zmanim->shaahZmanis19Point8Degrees;
$zmanim->shaahZmanis18Degrees;
$zmanim->shaahZmanis26Degrees;
$zmanim->shaahZmanis16Point1Degrees;
$zmanim->shaahZmanis60Minutes;
$zmanim->shaahZmanis72Minutes;
$zmanim->shaahZmanis72MinutesZmanis;
$zmanim->shaahZmanis90Minutes;
$zmanim->shaahZmanis90MinutesZmanis;
$zmanim->shaahZmanis96MinutesZmanis;
$zmanim->shaahZmanisAteretTorah;     // See note 1 below
$zmanim->shaahZmanisAlos16Point1ToTzais3Point8;
$zmanim->shaahZmanisAlos16Point1ToTzais3Point7;
$zmanim->shaahZmanis96Minutes;
$zmanim->shaahZmanis120Minutes;
$zmanim->shaahZmanis120MinutesZmanis;
$zmanim->shaahZmanisBaalHatanya;     // See note 3 below
$zmanim->shaahZmanisGra;
$zmanim->shaahZmanisMGA;
```

#### Alos Hashachar
```php
$zmanim->alosHashachar;              // Sunrise offset by 16.1 degrees
$zmanim->alos72;
$zmanim->alos60;
$zmanim->alos72Zmanis;
$zmanim->alos96;
$zmanim->alos90Zmanis;
$zmanim->alos96Zmanis;
$zmanim->alos90;
$zmanim->alos120;
$zmanim->alos120Zmanis;
$zmanim->alos26Degrees;
$zmanim->alos18Degrees;
$zmanim->alos19Degrees;
$zmanim->alos19Point8Degrees;
$zmanim->alos16Point1Degrees;        // Same as default
$zmanim->alosBaalHatanya;            // See note 3 below
```

#### Misheyakir
```php
$zmanim->misheyakir11Point5Degrees;
$zmanim->misheyakir11Degrees;
$zmanim->misheyakir10Point2Degrees;
$zmanim->misheyakir7Point65Degrees;
$zmanim->misheyakir9Point5Degrees;
```

#### Sof Zman Shma
```php
$zmanim->sofZmanShmaMGA;
$zmanim->sofZmanShmaGra;
$zmanim->sofZmanShmaMGA19Point8Degrees;
$zmanim->sofZmanShmaMGA16Point1Degrees;
$zmanim->sofZmanShmaMGA18Degrees;
$zmanim->sofZmanShmaMGA72Minutes;
$zmanim->sofZmanShmaMGA72MinutesZmanis;
$zmanim->sofZmanShmaMGA90Minutes;
$zmanim->sofZmanShmaMGA90MinutesZmanis;
$zmanim->sofZmanShmaMGA96Minutes;
$zmanim->sofZmanShmaMGA96MinutesZmanis;
$zmanim->sofZmanShma3HoursBeforeChatzos;
$zmanim->sofZmanShmaMGA120Minutes;
$zmanim->sofZmanShmaAlos16Point1ToSunset;
$zmanim->sofZmanShmaAlos16Point1ToTzaisGeonim7Point083Degrees;
$zmanim->sofZmanShmaKolEliyahu;
$zmanim->sofZmanShmaAteretTorah;     // See note 1 below
$zmanim->sofZmanShmaFixedLocal;      // See note 2 below
$zmanim->sofZmanShmaBaalHatanya;     // See note 3 below
$zmanim->sofZmanShmaMGA18DegreesToFixedLocalChatzos;
$zmanim->sofZmanShmaMGA16Point1DegreesToFixedLocalChatzos;
$zmanim->sofZmanShmaMGA90MinutesToFixedLocalChatzos;
$zmanim->sofZmanShmaMGA72MinutesToFixedLocalChatzos;
$zmanim->sofZmanShmaGRASunriseToFixedLocalChatzos;
```

#### Sof Zman Tfila
```php
$zmanim->sofZmanTfilaMGA;
$zmanim->sofZmanTfilaGra;
$zmanim->sofZmanTfilaMGA19Point8Degrees;
$zmanim->sofZmanTfilaMGA16Point1Degrees;
$zmanim->sofZmanTfilaMGA18Degrees;
$zmanim->sofZmanTfilaMGA72Minutes;
$zmanim->sofZmanTfilaMGA72MinutesZmanis;
$zmanim->sofZmanTfilaMGA90Minutes;
$zmanim->sofZmanTfilaMGA90MinutesZmanis;
$zmanim->sofZmanTfilaMGA96Minutes;
$zmanim->sofZmanTfilaMGA96MinutesZmanis;
$zmanim->sofZmanTfilaMGA120Minutes;
$zmanim->sofZmanTfila2HoursBeforeChatzos;
$zmanim->sofZmanTfilahAteretTorah;   // See note 1 below
$zmanim->sofZmanTfilaFixedLocal;     // See note 2 below
$zmanim->sofZmanTfilaBaalHatanya;    // See note 3 below
$zmanim->sofZmanTfilaGRASunriseToFixedLocalChatzos;
```

#### Erev Pesach
```php
$zmanim->sofZmanAchilasChametzGRA;
$zmanim->sofZmanAchilasChametzMGA72Minutes;
$zmanim->sofZmanAchilasChametzMGA16Point1Degrees;
$zmanim->sofZmanAchilasChametzBaalHatanya; // See note 3 below

$zmanim->sofZmanBiurChametzGRA;
$zmanim->sofZmanBiurChametzMGA72Minutes;
$zmanim->sofZmanBiurChametzMGA16Point1Degrees;
$zmanim->sofZmanBiurChametzBaalHatanya; // See note 3 below
```

#### Chatzos
```php
$zmanim->chatzos;                    // This defaults to astronomical chatzos
$zmanim->chatzosAsHalfDay;           // This defaults to halfway between sunrise and sunset and falls back to chatzos if that is not possible (e.g. the North Pole during summer)
$zmanim->fixedLocalChatzos;          // See note 2 below
```

#### Mincha Gedola
```php
$zmanim->minchaGedola;
$zmanim->minchaGedola30Minutes;
$zmanim->minchaGedola72Minutes;
$zmanim->minchaGedola16Point1Degrees;
$zmanim->minchaGedolaAhavatShalom;
$zmanim->minchaGedolaGreaterThan30;  // Fixed 30 minutes or degrees, whichever is later
$zmanim->minchaGedolaAteretTorah;    // See note 1 below
$zmanim->minchaGedolaBaalHatanya;    // See note 3 below
$zmanim->minchaGedolaBaalHatanyaGreaterThan30;
$zmanim->minchaGedolaGRAFixedLocalChatzos30Minutes;
```

#### Mincha Ketana
```php
$zmanim->minchaKetana;
$zmanim->minchaKetana16Point1Degrees;
$zmanim->minchaKetanaAhavatShalom;
$zmanim->minchaKetana72Minutes;
$zmanim->minchaKetanaAteretTorah;    // See note 1 below
$zmanim->minchaKetanaBaalHatanya;    // See note 3 below
$zmanim->minchaKetanaGRAFixedLocalChatzosToSunset;

$zmanim->samuchLeMinchaKetanaGRA;
$zmanim->samuchLeMinchaKetana16Point1Degrees;
$zmanim->samuchLeMinchaKetana72Minutes;
```

#### Plag Hamincha
```php
$zmanim->plagHamincha;
$zmanim->plagHamincha120MinutesZmanis;
$zmanim->plagHamincha120Minutes;
$zmanim->plagHamincha60Minutes;
$zmanim->plagHamincha72Minutes;
$zmanim->plagHamincha90Minutes;
$zmanim->plagHamincha96Minutes;
$zmanim->plagHamincha96MinutesZmanis;
$zmanim->plagHamincha90MinutesZmanis;
$zmanim->plagHamincha72MinutesZmanis;
$zmanim->plagHamincha16Point1Degrees;
$zmanim->plagHamincha19Point8Degrees;
$zmanim->plagHamincha26Degrees;
$zmanim->plagHamincha18Degrees;
$zmanim->plagAlosToSunset;
$zmanim->plagAlos16Point1ToTzaisGeonim7Point083Degrees;
$zmanim->plagAhavatShalom;
$zmanim->plagHaminchaAteretTorah;    // See note 1 below
$zmanim->plagHaminchaBaalHatanya;    // See note 3 below
$zmanim->plagHaminchaGRAFixedLocalChatzosToSunset;
```

#### Candle Lighting
```php
$zmanim->candleLighting; // Get sea level sunset minus candle lighting offset. Default is 18 minutes
$zmanim->setCandleLightingOffset($candleLightingOffset);
```

#### Start of Bain Hasmashos (According to Rabbeinu Tam)

```php
$zmanim->bainHashmashosRT13Point24Degrees;
$zmanim->bainHashmashosRT58Point5Minutes;
$zmanim->bainHashmashosRT13Point5MinutesBefore7Point083Degrees;
$zmanim->bainHashmashosRT2Stars;
$zmanim->bainHashmashosYereim18Minutes;
$zmanim->bainHashmashosYereim3Point05Degrees;
$zmanim->bainHashmashosYereim16Point875Minutes;
$zmanim->bainHashmashosYereim2Point8Degrees;
$zmanim->bainHashmashosYereim13Point5Minutes;
$zmanim->bainHashmashosYereim2Point1Degrees;
```

#### Tzais
```php
$zmanim->tzais;                      // Sunset offset by 8.5 degrees
$zmanim->tzais72;getTzais()
$zmanim->tzaisGeonim3Point7Degrees;
$zmanim->tzaisGeonim3Point8Degrees;
$zmanim->tzaisGeonim5Point95Degrees;
$zmanim->tzaisGeonim3Point65Degrees;
$zmanim->tzaisGeonim3Point676Degrees;
$zmanim->tzaisGeonim4Point61Degrees;
$zmanim->tzaisGeonim4Point37Degrees;
$zmanim->tzaisGeonim5Point88Degrees;
$zmanim->tzaisGeonim4Point8Degrees;
$zmanim->tzaisGeonim6Point45Degrees;
$zmanim->tzaisGeonim7Point083Degrees;
$zmanim->tzaisGeonim7Point67Degrees;
$zmanim->tzaisGeonim8Point5Degrees;
$zmanim->tzaisGeonim9Point3Degrees;
$zmanim->tzaisGeonim9Point75Degrees;
$zmanim->tzais60;
$zmanim->tzaisAteretTorah;           // See note 1 below
$zmanim->tzais72Zmanis;
$zmanim->tzais90Zmanis;
$zmanim->tzais96Zmanis;
$zmanim->tzais90;
$zmanim->tzais120;
$zmanim->tzais120Zmanis;
$zmanim->tzais16Point1Degrees;
$zmanim->tzais26Degrees;
$zmanim->tzais18Degrees;
$zmanim->tzais19Point8Degrees;
$zmanim->tzais96;
$zmanim->tzaisBaalHatanya;           // See note 3 below
$zmanim->tzais50;
```

#### Chatzos Halayla (Midnight)
```php
$zmanim->solarMidnight;
```

#### Molad Zmanim
```php
$zmanim->sofZmanKidushLevanaBetweenMoldos;
$zmanim->sofZmanKidushLevana15Days;
$zmanim->tchilasZmanKidushLevana3Days;
$zmanim->zmanMolad;
$zmanim->tchilasZmanKidushLevana7Days;
```

#### Notes
1. AteretTorah Zman is calculated based on a day being from Alos72Zmanis until 40 minutes after sunset. However, the exact time offset can be changed by calling `$zmanim->setAteretTorahSunsetOffset($ateretTorahSunsetOffset)`.
1. FixedLocalChatzos is based on a fixed time for Chatzos throughout the year, see KosherJava's documentation for more details.
1. Baal Hatanya calculates Zmanim based on a zenith of 1.583 degrees below the 90 degree geometric zenith.

### Alternative Zmanim

If you are looking for any Zman which is an offset from sunrise or sunset that is not listed in the list above, you can call one of the following methods with your offset.

```php
$zmanim->getSunriseOffsetByDegrees($offsetZenith);
$zmanim->getSunriseSolarDipFromOffset($minutes);
$zmanim->getSunsetOffsetByDegrees($offsetZenith);
$zmanim->getSunsetSolarDipFromOffset($minutes);
```

You can use those times as parameters for custom calculations for the following functions:

```php
$zmanim->getSofZmanShma($startOfDay, $endOfDay);
$zmanim->getSofZmanTfila($startOfDay, $endOfDay);
$zmanim->getMinchaGedola($startOfDay, $endOfDay);
$zmanim->getMinchaKetana($startOfDay, $endOfDay);
$zmanim->getPlagHamincha($startOfDay, $endOfDay);
```

## Jewish Calendar Calculations

For the usage syntax you can look at the KosherJava documentation. (TODO: Add the methods here as well).

## GeoLocation Mathematical Calculations

There are a few functions that can be run for calculations on GeoLocation objects:

```php
use PhpZmanim\Geo\GeoLocation;
use PhpZmanim\Geo\GeoLocationUtils;

$new_york = new GeoLocation("New York City", 40.850519, -73.929214, 200, "America/New_York");
$jerusalem = new GeoLocation('Jerusalem, Israel', 31.7781161, 35.233804, 740, 'Asia/Jerusalem');

GeoLocationUtils::getGeodesicInitialBearing($new_york, $jerusalem);
GeoLocationUtils::getGeodesicFinalBearing($new_york, $jerusalem);
GeoLocationUtils::getGeodesicDistance($new_york, $jerusalem);
GeoLocationUtils::getRhumbLineBearing($new_york, $jerusalem);
GeoLocationUtils::getRhumbLineDistance($new_york, $jerusalem);
```
