# PhpZmanim
A PHP port of the [KosherJava Zmanim API](https://kosherjava.com) from Eliyahu Hershfeld (code at the [KosherJava Zmanim project](https://github.com/KosherJava/zmanim)). See Kosher Java documentation for comments for every class variable and method. Only calculations for Zmanim times were ported, for a library with most calculations from KosherJava's JewishCalendar you can view [PHP Zman Library](https://github.com/zmanim/zman). See below for how to install and a more detailed list of what you can access and methods you can call. Once instantiated, you can ask for many Zmanim right out of the gate:

```php
$zmanim = Zmanim::create(2019, 2, 22, 'Lakewood, NJ', 40.0721087, -74.2400243, 39.57, 'America/New_York');
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

#### Alos Hashachar
```php
$zmanim->AlosHashachar;              // Sunrise offset by 16.1 degrees
$zmanim->Alos72;                     // 72 minutes before sunrise
```

#### Sof Zman Shma
```php
$zmanim->SofZmanShmaMA;              // Based on calculations of Magen Avraham
$zmanim->SofZmanShmaGra;             // Based on calculations of the Gra
```

#### Sof Zman Tfila
```php
$zmanim->SofZmanTfilaMA;             // Based on calculations of Magen Avraham
$zmanim->SofZmanTfilaGra;            // Based on calculations of the Gra
```

#### Chatzos
```php
$zmanim->Chatzos;                    // Midpoint between sunrise and sunset
```

#### Mincha Gedola
```php
$zmanim->MinchaGedolaGra;            // Based on calculations of the Gra
```

#### Mincha Ketana
```php
$zmanim->MinchaKetanaGra;            // Based on calculations of the Gra
```

#### Plag Hamincha
```php
$zmanim->PlagHaminchaGra;            // Based on calculations of the Gra
```

#### Candle Lighting
```php
$zmanim->CandleLighting; // Get sea level sunset minus candle lighting offset. Default is 18 minutes
$zmanim->setCandleLightingOffset($candleLightingOffset); // Change the offset for candle lighting
```

#### Tzais
```php
$zmanim->Tzais;                      // Sunset offset by 8.5 degrees
$zmanim->Tzais72;                    // 72 minutes after sunset
```

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