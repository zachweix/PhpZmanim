# PhpZmanim
A PHP port of the [KosherJava Zmanim API](https://kosherjava.com) from Eliyahu Hershfeld (code at the [KosherJava Zmanim project](https://github.com/KosherJava/zmanim)). See Kosher Java documentation for comments for every class variable and method. Only calculations for Zmanim times were ported, for a library with most calculations from KosherJava's JewishCalendar you can view [PHP Zman Library](https://github.com/zmanim/zman). See below for how to install and a more detailed list of what you can access and methods you can call. Once instantiated, you can ask for many Zmanim right out of the gate:

```php
$zmanim = Zmanim::create(2019, 2, 22, 'Lakewood', 40.0721087, -74.2400243, 39.57, 'America/New_York');
$zmanim->Tzais72->format('Y-m-d\TH:i:sP'); // 2019-02-22T18:52:38-05:00
```

## Installation (with Composer)

```
$ composer require zachweix/php-zmanim
```

```json
{
    "require": {
        "zachweix/php-zmanim": "^1.0"
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

You can instantiate a new `Zmanim` object with the `Zmanim::create()` function. If you want, you can instantiate by creating a `GeoLocation` object and a `ComplexZmanimCalendar` object. There are currently two ways to calculate the Zmanim times, SunTimesCalculator and NoaaCalculator; the default calculator is NoaaCalculator.

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

Any parameter called like `$zmanim->Sunrise` or a method name you see that is called like `$zmanim->get("Sunrise")` can be called by concatenating `get` to the zman, so you would get `$zmanim->getSunrise()`, so the following three will return identical results, all of which are a Carbon object.

```php
$sunrise = $zmanim->Sunrise;
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
$zmanim->Sunrise;                    // Get sunrise based on current elevation
$zmanim->SeaLevelSunrise;            // Get sunrise at zero elevation
$zmanim->BeginCivilTwilight;         // The point when sun's zenith is at 96 degrees
$zmanim->BeginNauticalTwilight;      // The point when sun's zenith is at 102 degrees
$zmanim->BeginAstronomicalTwilight;  // The point when sun's zenith is at 108 degrees
```

#### Sunset
```php
$zmanim->Sunset;                     // Sunset based on current elevation
$zmanim->SeaLevelSunset;             // Sunset at zero elevation
$zmanim->EndCivilTwilight;           // The point when sun's zenith is at 96 degrees
$zmanim->EndNauticalTwilight;        // The point when sun's zenith is at 102 degrees
$zmanim->EndAstronomicalTwilight;    // The point when sun's zenith is at 108 degrees
```

#### Length of Shaah Zmanim (in minutes)

```php
$zmanim->ShaahZmanis19Point8Degrees;
$zmanim->ShaahZmanis18Degrees;
$zmanim->ShaahZmanis26Degrees;
$zmanim->ShaahZmanis16Point1Degrees;
$zmanim->ShaahZmanis60Minutes;
$zmanim->ShaahZmanis72Minutes;
$zmanim->ShaahZmanis72MinutesZmanis;
$zmanim->ShaahZmanis90Minutes;
$zmanim->ShaahZmanis90MinutesZmanis;
$zmanim->ShaahZmanis96MinutesZmanis;
$zmanim->ShaahZmanisAteretTorah;     // See note 1 below
$zmanim->ShaahZmanis96Minutes;
$zmanim->ShaahZmanis120Minutes;
$zmanim->ShaahZmanis120MinutesZmanis;
$zmanim->ShaahZmanisBaalHatanya;     // See note 3 below
```

#### Alos Hashachar
```php
$zmanim->AlosHashachar;              // Sunrise offset by 16.1 degrees
$zmanim->Alos72;
$zmanim->Alos60;
$zmanim->Alos72Zmanis;
$zmanim->Alos96;
$zmanim->Alos90Zmanis;
$zmanim->Alos96Zmanis;
$zmanim->Alos90;
$zmanim->Alos120;
$zmanim->Alos120Zmanis;
$zmanim->Alos26Degrees;
$zmanim->Alos18Degrees;
$zmanim->Alos19Degrees;
$zmanim->Alos19Point8Degrees;
$zmanim->Alos16Point1Degrees;        // Same as default
$zmanim->AlosBaalHatanya;            // See note 3 below
```

#### Misheyakir
```php
$zmanim->Misheyakir11Point5Degrees;
$zmanim->Misheyakir11Degrees;
$zmanim->Misheyakir10Point2Degrees;
$zmanim->Misheyakir7Point65Degrees;
$zmanim->Misheyakir9Point5Degrees;
```

#### Sof Zman Shma
```php
$zmanim->SofZmanShmaMA;
$zmanim->SofZmanShmaGra;
$zmanim->SofZmanShmaMGA19Point8Degrees;
$zmanim->SofZmanShmaMGA16Point1Degrees;
$zmanim->SofZmanShmaMGA18Degrees;
$zmanim->SofZmanShmaMGA72Minutes;
$zmanim->SofZmanShmaMGA72MinutesZmanis;
$zmanim->SofZmanShmaMGA90Minutes;
$zmanim->SofZmanShmaMGA90MinutesZmanis;
$zmanim->SofZmanShmaMGA96Minutes;
$zmanim->SofZmanShmaMGA96MinutesZmanis;
$zmanim->SofZmanShma3HoursBeforeChatzos;
$zmanim->SofZmanShmaMGA120Minutes;
$zmanim->SofZmanShmaAlos16Point1ToSunset;
$zmanim->SofZmanShmaAlos16Point1ToTzaisGeonim7Point083Degrees;
$zmanim->SofZmanShmaAteretTorah;     // See note 1 below
$zmanim->SofZmanShmaFixedLocal;      // See note 2 below
$zmanim->SofZmanShmaBaalHatanya;     // See note 3 below
```

#### Sof Zman Tfila
```php
$zmanim->SofZmanTfilaMA;
$zmanim->SofZmanTfilaGra;
$zmanim->SofZmanTfilaMGA19Point8Degrees;
$zmanim->SofZmanTfilaMGA16Point1Degrees;
$zmanim->SofZmanTfilaMGA18Degrees;
$zmanim->SofZmanTfilaMGA72Minutes;
$zmanim->SofZmanTfilaMGA72MinutesZmanis;
$zmanim->SofZmanTfilaMGA90Minutes;
$zmanim->SofZmanTfilaMGA90MinutesZmanis;
$zmanim->SofZmanTfilaMGA96Minutes;
$zmanim->SofZmanTfilaMGA96MinutesZmanis;
$zmanim->SofZmanTfilaMGA120Minutes;
$zmanim->SofZmanTfila2HoursBeforeChatzos;
$zmanim->SofZmanTfilahAteretTorah;   // See note 1 below
$zmanim->SofZmanTfilaFixedLocal;     // See note 2 below
$zmanim->SofZmanTfilaBaalHatanya;    // See note 3 below
```

#### Erev Pesach
```php
$zmanim->SofZmanAchilasChametzGRA;
$zmanim->SofZmanAchilasChametzMGA72Minutes;
$zmanim->SofZmanAchilasChametzMGA16Point1Degrees;
$zmanim->SofZmanAchilasChametzBaalHatanya; // See note 3 below

$zmanim->SofZmanBiurChametzGRA;
$zmanim->SofZmanBiurChametzMGA72Minutes;
$zmanim->SofZmanBiurChametzMGA16Point1Degrees;
$zmanim->SofZmanBiurChametzBaalHatanya; // See note 3 below
```

#### Chatzos
```php
$zmanim->Chatzos;
$zmanim->FixedLocalChatzos;          // See note 2 below
```

#### Mincha Gedola
```php
$zmanim->MinchaGedolaGra;
$zmanim->MinchaGedola30Minutes;
$zmanim->MinchaGedola72Minutes;
$zmanim->MinchaGedola16Point1Degrees;
$zmanim->MinchaGedolaGreaterThan30;  // Fixed 30 minutes or degrees, whichever is later
$zmanim->MinchaGedolaAteretTorah;    // See note 1 below
$zmanim->MinchaGedolaBaalHatanya;    // See note 3 below
$zmanim->MinchaGedolaBaalHatanyaGreaterThan30;
```

#### Mincha Ketana
```php
$zmanim->MinchaKetanaGra;
$zmanim->MinchaKetana16Point1Degrees;
$zmanim->MinchaKetana72Minutes;
$zmanim->MinchaKetanaAteretTorah;    // Set note 1 below
$zmanim->MinchaKetanaBaalHatanya;    // See note 3 below
```

#### Plag Hamincha
```php
$zmanim->PlagHaminchaGra;
$zmanim->PlagHamincha120MinutesZmanis;
$zmanim->PlagHamincha120Minutes;
$zmanim->PlagHamincha60Minutes;
$zmanim->PlagHamincha72Minutes;
$zmanim->PlagHamincha90Minutes;
$zmanim->PlagHamincha96Minutes;
$zmanim->PlagHamincha96MinutesZmanis;
$zmanim->PlagHamincha90MinutesZmanis;
$zmanim->PlagHamincha72MinutesZmanis;
$zmanim->PlagHamincha16Point1Degrees;
$zmanim->PlagHamincha19Point8Degrees;
$zmanim->PlagHamincha26Degrees;
$zmanim->PlagHamincha18Degrees;
$zmanim->PlagAlosToSunset;
$zmanim->PlagAlos16Point1ToTzaisGeonim7Point083Degrees;
$zmanim->PlagHaminchaAteretTorah;    // Set note 1 below
$zmanim->PlagHaminchaBaalHatanya;    // See note 3 below
```

#### Candle Lighting
```php
$zmanim->CandleLighting; // Get sea level sunset minus candle lighting offset. Default is 18 minutes
$zmanim->setCandleLightingOffset($candleLightingOffset);
```

#### Tzais
```php
$zmanim->Tzais;                      // Sunset offset by 8.5 degrees
$zmanim->Tzais72;
$zmanim->TzaisGeonim3Point7Degrees;
$zmanim->TzaisGeonim3Point8Degrees;
$zmanim->TzaisGeonim5Point95Degrees;
$zmanim->TzaisGeonim3Point65Degrees;
$zmanim->TzaisGeonim3Point676Degrees;
$zmanim->TzaisGeonim4Point61Degrees;
$zmanim->TzaisGeonim4Point37Degrees;
$zmanim->TzaisGeonim5Point88Degrees;
$zmanim->TzaisGeonim4Point8Degrees;
$zmanim->TzaisGeonim6Point45Degrees;
$zmanim->TzaisGeonim7Point083Degrees;
$zmanim->TzaisGeonim7Point67Degrees;
$zmanim->TzaisGeonim8Point5Degrees;
$zmanim->TzaisGeonim9Point3Degrees;
$zmanim->TzaisGeonim9Point75Degrees;
$zmanim->Tzais60;
$zmanim->TzaisAteretTorah;           // Set note 1 below
$zmanim->Tzais72Zmanis;
$zmanim->Tzais90Zmanis;
$zmanim->Tzais96Zmanis;
$zmanim->Tzais90;
$zmanim->Tzais120;
$zmanim->Tzais120Zmanis;
$zmanim->Tzais16Point1Degrees;
$zmanim->Tzais26Degrees;
$zmanim->Tzais18Degrees;
$zmanim->Tzais19Point8Degrees;
$zmanim->Tzais96;
$zmanim->TzaisBaalHatanya;           // Set note 3 below
```

#### Chatzos Halayla (Midnight)
```php
$zmanim->SolarMidnight;
```

#### Notes
1 AteretTorah Zman is calculated based on a day being from Alos72Zmanis until 40 minutes after sunset. However, the exact time offset can be changed by calling `$zmanim->setAteretTorahSunsetOffset($ateretTorahSunsetOffset)`.
1 FixedLocalChatzos is based on a fixed time for Chatzos throughout the year, see KosherJava's documentation for more details.
1 Baal Hatanya calculates Zmanim based on a zenith of 1.583 degrees below the 90 degree geometric zenith.

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