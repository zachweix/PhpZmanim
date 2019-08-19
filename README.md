# PhpZmanim
A PHP port of the [KosherJava Zmanim API](https://kosherjava.com) from Eliyahu Hershfeld. (code at the [KosherJava Zmanim project](https://github.com/KosherJava/zmanim)). See Kosher Java documentation for comments for every class variable and method.

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

You can instantiate a new `Zmanim` object with the `Zmanim::create` function. If you want, you can instantiate by creating a `GeoLocation` object and a `ComplexZmanimCalendar` object, however the first method is recommended and the rest of the docs will assume that is how the zmanim were instantiated.

There are two ways to calculate the Zmanim times, SunTimesCalculator and NoaaCalculator; the default calculator is NoaaCalculator.

### Via `Zmanim::create`

The expected arguments are:
`($year, $month, $day, $locationName, $latitude, $longitude, $elevation, $timeZone)`

All arguments are optional. The default values are today in Greenwich Mean Time at elevation 0. As you can see below, these are how you would set the location to either the default location, or a custom location. The location name is not used anywhere, and is only for your own internal reference (it can be null if you want).

If you give `null` as the year, month and day, today's UTC date will be used, which may be different than your current date.

```php
$zmanimInGMT = Zmanim::create(2019, 2, 21, "Greenwich");
$zmanim = Zmanim::create(2019, 2, 21, "New York City", 40.850519, -73.929214, 200, "America/New_York");
```

### Via `GeoLocation` and `ComplexZmanimCalendar`

`GeoLocation`'s expected arguments are:
`($locationName, $latitude, $longitude, $elevation, $timeZone)`

`ComplexZmanimCalendar`'s expected arguments are:
`($geoLocation, $year, $month, $day)`

If you give `null` as the year, month and day, today's UTC date will be used, which may be different than your current date.

```php
$geoLocation = new GeoLocation("New York City", 40.850519, -73.929214, 200, "America/New_York");
$complexZmanimCalendar = new ComplexZmanimCalendar($geoLocation, 2019, 2, 21);
```

## Usage

Any method name you see that is called like `$zmanim->get("Sunrise")` or parameter called like `$zmanim->Sunrise` can be called by concatenating `get` to the zman, so you would get `$zmanim->getSunrise`, so the following three will return identical results. However, only the method name `getSunrise` works if you instantiated your zmanim object using `GeoLocation` and `ComplexZmanimCalendar`.

```php
$sunrise = $zmanim->Sunrise;
$sunrise = $zmanim->get("Sunrise");
$sunrise = $zmanim->getSunrise();
```

If you want to factor elevation when calculating, make sure to set the elevation when instantiating the object. If you set an elevation other than 0 and want to ignore elevation, you can call `$zmanim->setUseElevation(false)` and then when you want to use it again, simply call `$zmanim->setUseElevation(true)`.

### Available Methods (Only if instantiated via `Zmanim` object)

```php
$zmanim->setCalculatorType($type);     // 'SunTimes' and 'Noaa' are currently the only valid calculators
$zmanim->setDate($year, $month, $day); // Change the current date
$zmanim->addDays($value);              // Change the current date, by adding the requested number of days
$zmanim->subDays($value);              // Change the current date, by subtracting the requested number of days
```

### List of Zmanim

Here is a list of many possible Zmanim you can request. If you don't find what you are looking for, see below for how to get more custom Zmanim.

#### Sunrise:
```php
* $zmanim->Sunrise;                    // Get sunrise based on current elevation
* $zmanim->SeaLevelSunrise;            // Get sunrise at zero elevation
* $zmanim->BeginCivilTwilight;         // The point when sun's zenith is at 96 degrees
* $zmanim->BeginNauticalTwilight;      // The point when sun's zenith is at 102 degrees
* $zmanim->BeginAstronomicalTwilight;  // The point when sun's zenith is at 108 degrees
```

#### Sunset
```php
* $zmanim->Sunset;                     // Sunset based on current elevation
* $zmanim->SeaLevelSunset;             // Sunset at zero elevation
* $zmanim->EndCivilTwilight;           // The point when sun's zenith is at 96 degrees
* $zmanim->EndNauticalTwilight;        // The point when sun's zenith is at 102 degrees
* $zmanim->EndAstronomicalTwilight;    // The point when sun's zenith is at 108 degrees
```

#### Alos Hashachar
```php
* $zmanim->AlosHashachar;              // Sunrise offset by 16.1 degrees
* $zmanim->Alos72;                     // 72 minutes before sunrise
```

#### Sof Zman Shma
```php
* $zmanim->SofZmanShmaMA;              // Based on calculations of Magen Avraham
* $zmanim->SofZmanShmaGra;             // Based on calculations of the Gra
```

#### Sof Zman Tfila
```php
* $zmanim->SofZmanTfilaMA;             // Based on calculations of Magen Avraham
* $zmanim->SofZmanTfilaGra;            // Based on calculations of the Gra
```

#### Chatzos
```php
* $zmanim->Chatzos;                    // Midpoint between sunrise and sunset
```

#### Mincha Gedola
```php
* $zmanim->MinchaGedolaGra;            // Based on calculations of the Gra
```

#### Mincha Ketana
```php
* $zmanim->MinchaKetanaGra;            // Based on calculations of the Gra
```

#### Plag Hamincha
```php
* $zmanim->PlagHaminchaGra;            // Based on calculations of the Gra
```

#### Candle Lighting
```php
* $zmanim->CandleLighting;                                 // Get candle lighting time (offset from sea level sunset). Default is 18 minutes
* $zmanim->setCandleLightingOffset($candleLightingOffset); // Change the offset for candle lighting
```

#### Tzais
```php
* $zmanim->Tzais;                      // Sunset offset by 8.5 degrees
* $zmanim->Tzais72;                    // 72 minutes after sunset
```

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