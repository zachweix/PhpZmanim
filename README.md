# PhpZmanim

A PHP port of the [KosherJava Zmanim API](https://kosherjava.com) by Eliyahu Hershfeld (source at the [KosherJava Zmanim project](https://github.com/KosherJava/zmanim)). It calculates halachic times (_zmanim_) for any date and location, and provides a full Jewish calendar (dates, holidays, parsha, daf yomi, molad, and more).

Because this is a port, the [KosherJava documentation](https://kosherjava.com) remains the canonical reference for the meaning of every zman and calendar method. This README is the quick start; see [docs/USAGE.md](docs/USAGE.md) for detailed usage.

> **Upgrading from v3?** v4 renames and reorganizes much of the API. See the [v3 → v4 upgrade guide](docs/UPGRADING.md).

```php
use PhpZmanim\Zman;
use PhpZmanim\JewishDate;

// Zmanim for a date and location
$zman = Zman::create(2019, 2, 22, 40.0721087, -74.2400243, 39.57, 'America/New_York');
$zman->getSunrise()->format('Y-m-d\TH:i:sP');  // 2019-02-22T06:39:45-05:00
$zman->getTzais72()->format('Y-m-d\TH:i:sP');   // 2019-02-22T18:52:39-05:00

// The Jewish calendar
$jewishDate = JewishDate::createFromDate(2023, 9, 30);  // from a Gregorian date
$jewishDate = JewishDate::create(5784, 7, 15);          // or from a Jewish date
$jewishDate->isSuccos();       // true
$jewishDate->isRoshHashana();  // false

// Formatting, in English or Hebrew
$jewishDate->format()->english()->yomTov();  // Succos
$jewishDate->format()->hebrew()->date();      // ט״ו תשרי תשפ״ד
```

## Requirements

- PHP 8.1 or higher
- [Carbon](https://carbon.nesbot.com/) 2 or 3 (`^2.0 || ^3.0`)

## Installation (with Composer)

```
$ composer require zachweix/php-zmanim
```

```json
{
    "require": {
        "zachweix/php-zmanim": "^4.0"
    }
}
```

## Setup

```php
<?php
require 'vendor/autoload.php';

use PhpZmanim\Zman;
use PhpZmanim\JewishDate;
```

## Zmanim

Create a `Zman` object for a date and location with `Zman::create()`, then ask for any zman as a method call:

```php
$zman = Zman::create(2019, 2, 22, 40.0721087, -74.2400243, 39.57, 'America/New_York');

$zman->getSunrise();          // a Carbon instance
$zman->getSunset();
$zman->getSofZmanShmaGRA();
$zman->getChatzos();
$zman->getPlagHaminchaGRA();
$zman->getTzais72();
```

The arguments to `Zman::create()` are, in order:

`$year, $month, $day, $latitude, $longitude, $elevation, $timezone`

All are optional; the defaults are today's date in Greenwich Mean Time at elevation `0`. If you pass `null` for the year, month, or day, today's UTC date is used (which may differ from your local date — e.g. 9:00 PM in New York on February 21 is 2:00 AM UTC on February 22).

Every zman is retrieved with a `getX()` method and returns a [Carbon](https://carbon.nesbot.com/) instance (or `null` for zmanim that don't apply to the given date/location — see [docs/USAGE.md](docs/USAGE.md)). Format the result with any Carbon method:

```php
$zman->getSunset()->format('g:i A');   // 5:41 PM
$zman->getSunset()->toIso8601String();
```

### Elevation

Pass an elevation (in meters) to `Zman::create()` to factor it into the calculation. To ignore a set elevation temporarily, call `$zman->setUseElevation(false)`, and `$zman->setUseElevation(true)` to use it again.

### Calculators

The default calculation engine is the `NoaaCalculator`. Three others are available: `MeeusCalculator`, `SPACalculator`, and `SunTimesCalculator`. Select one by passing it to `Zman::create()` or `setAstronomicalCalculator()`:

```php
use PhpZmanim\Calculator\MeeusCalculator;

$zman = Zman::create(2019, 2, 22, 40.0721087, -74.2400243, 39.57, 'America/New_York', MeeusCalculator::create());
// or on an existing object:
$zman->setAstronomicalCalculator(MeeusCalculator::create());
```

There are far more zmanim than shown here (over 150), plus configuration options and custom calculations. See **[docs/USAGE.md](docs/USAGE.md)** for the full list.

## The Jewish Calendar

`JewishDate` gives you the Jewish calendar for a date. Create one from a Gregorian date with `createFromDate()`, or from a Jewish date with `create()`:

```php
$jewishDate = JewishDate::createFromDate(2023, 9, 30);  // Gregorian year, month, day
$jewishDate = JewishDate::create(5784, 7, 15);          // Jewish year, month, day

$jewishDate->isSuccos();          // true
$jewishDate->getJewishYear();     // 5784
$jewishDate->getDafYomiBavli();   // a Daf value object
```

### Formatting

Formatting is fluent and available in English or Hebrew off any `JewishDate` via `format()`:

```php
$jewishDate = JewishDate::create(5784, 7, 15);

$jewishDate->format()->english()->date();     // 15 Tishrei, 5784
$jewishDate->format()->hebrew()->date();       // ט״ו תשרי תשפ״ד
$jewishDate->format()->english()->yomTov();    // Succos
$jewishDate->format()->hebrew()->yomTov();      // סוכות

// Daf yomi
JewishDate::createFromDate(2024, 5, 30)->format()->english()->dafYomiBavli();  // Bava Metzia 92

// The kviah (year type) is Hebrew only
JewishDate::create(5784, 7, 1)->format()->hebrew()->kviah();  // זחג
```

See **[docs/USAGE.md](docs/USAGE.md)** for the full formatting reference (parsha, special shabbos, rosh chodesh, omer, and more) and the complete list of calendar methods.

## Detailed Documentation

- **[docs/USAGE.md](docs/USAGE.md)** — full zmanim reference, configuration, calculators, custom zmanim, and the Jewish calendar & formatting API.
- **[docs/UPGRADING.md](docs/UPGRADING.md)** — migrating existing code from v3 to v4.
- **[KosherJava documentation](https://kosherjava.com)** — the canonical reference for what each zman and method means.
