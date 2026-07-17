# Upgrading from v3 to v4

Version 4 is a large modernization of PhpZmanim. The public API was reorganized to match the current [KosherJava](https://kosherjava.com) structure, several classes were merged, namespaces changed, and a number of methods were renamed. This guide lists everything you need to change to move existing v3 code to v4.

If you are starting fresh, ignore this file and read the [README](../README.md) and [detailed usage guide](USAGE.md) instead.

## At a glance

| Area | v3 | v4 |
|------|----|----|
| Zmanim entry point | `PhpZmanim\Zmanim` (extends `ComplexZmanimCalendar`) | `PhpZmanim\Zman` (one class) |
| Calendar classes | `Calendar\AstronomicalCalendar`, `Calendar\ZmanimCalendar`, `Calendar\ComplexZmanimCalendar` | folded into `PhpZmanim\Zman` |
| Location | `PhpZmanim\Geo\GeoLocation` (+ `Geo\GeoLocationUtils`) | `PhpZmanim\GeoLocation` |
| Jewish calendar | `HebrewCalendar\JewishDate` + `HebrewCalendar\JewishCalendar` | one `PhpZmanim\JewishDate` |
| Formatting | `HebrewCalendar\HebrewDateFormatter` | fluent `$jewishDate->format()->hebrew()/->english()` |
| Daf yomi | `HebrewCalendar\Daf` + `YomiCalculator` + `YerushalmiYomiCalculator` | `PhpZmanim\Torah\Daf` + `$jewishDate->getDafYomiBavli()`/`getDafYomiYerushalmi()` |
| Parsha / holidays | `HebrewCalendar\Parsha` | `PhpZmanim\Torah\Parshah` / `PhpZmanim\Torah\YomTov` enums |
| Tefila rules | static `HebrewCalendar\TefilaRules` | instance methods on `PhpZmanim\JewishDate` |
| Magic properties | `$zmanim->sunset`, `$zmanim->get('Sunset')` | removed — call `$zman->getSunset()` |

## Requirements & Composer

v4 requires **PHP 8.1 or higher** (v3 ran on older versions). Carbon `^2.0 || ^3.0` is supported, the same as v3. Update your constraint:

```json
{
    "require": {
        "zachweix/php-zmanim": "^4.0"
    }
}
```

## Namespace & import reference

Update your `use` statements:

| v3 import | v4 import |
|-----------|-----------|
| `use PhpZmanim\Zmanim;` | `use PhpZmanim\Zman;` |
| `use PhpZmanim\Calendar\ComplexZmanimCalendar;` | *(removed — use `PhpZmanim\Zman`)* |
| `use PhpZmanim\Calendar\ZmanimCalendar;` | *(removed — use `PhpZmanim\Zman`)* |
| `use PhpZmanim\Calendar\AstronomicalCalendar;` | *(removed — use `PhpZmanim\Zman`)* |
| `use PhpZmanim\Geo\GeoLocation;` | `use PhpZmanim\GeoLocation;` |
| `use PhpZmanim\Geo\GeoLocationUtils;` | *(removed — methods are now on `GeoLocation`)* |
| `use PhpZmanim\HebrewCalendar\JewishDate;` | `use PhpZmanim\JewishDate;` |
| `use PhpZmanim\HebrewCalendar\JewishCalendar;` | `use PhpZmanim\JewishDate;` |
| `use PhpZmanim\HebrewCalendar\HebrewDateFormatter;` | *(removed — use `$jewishDate->format()`)* |
| `use PhpZmanim\HebrewCalendar\Daf;` | `use PhpZmanim\Torah\Daf;` |
| `use PhpZmanim\HebrewCalendar\Parsha;` | `use PhpZmanim\Torah\Parshah;` *(note the spelling)* |
| `use PhpZmanim\HebrewCalendar\TefilaRules;` | *(removed — methods on `JewishDate`)* |
| `use PhpZmanim\HebrewCalendar\YomiCalculator;` | *(removed — `$jewishDate->getDafYomiBavli()`)* |
| `use PhpZmanim\HebrewCalendar\YerushalmiYomiCalculator;` | *(removed — `$jewishDate->getDafYomiYerushalmi()`)* |

---

## Zmanim → Zman

### Creating the object

`Zmanim::create()` dropped the **`$locationName`** argument (location names are not used in calculations). The remaining arguments are unchanged in order, plus an optional calculator at the end.

```php
// v3
use PhpZmanim\Zmanim;
$zmanim = Zmanim::create(2019, 2, 21, "New York City", 40.850519, -73.929214, 200, "America/New_York");

// v4  (no location name)
use PhpZmanim\Zman;
$zman = Zman::create(2019, 2, 21, 40.850519, -73.929214, 200, "America/New_York");
```

v4 signature:

```
Zman::create($year, $month, $day, $latitude, $longitude, $elevation, $timezone, $calculator)
```

### Magic properties were removed

In v3 you could read a zman as a property or via `get()`:

```php
$zmanim->sunset;            // v3
$zmanim->get('Sunset');     // v3
$zmanim->getSunset();       // v3 and v4
```

v4 removed the magic `__get()` and `get()` accessors — **always call the `getX()` method**:

```php
$zman->getSunset();         // v4
```

If you have a lot of code that relies on the property style, you can restore it by extending `Zman` with your own `__get()`:

```php
use PhpZmanim\Zman;

class Zmanim extends Zman
{
    public function __get(string $name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \InvalidArgumentException("Unknown zman: {$name}");
    }
}

// create() returns your subclass, so property access works again:
$zman = Zmanim::create(2019, 2, 22, 40.0721087, -74.2400243, 39.57, 'America/New_York');
$zman->sunset;  // same as $zman->getSunset()
```

### Choosing a calculator

`setCalculatorType()` was removed. Pass a calculator instance instead — either to `create()` or via `setAstronomicalCalculator()`. Two new engines were added (`MeeusCalculator`, `SPACalculator`) alongside `NoaaCalculator` (default) and `SunTimesCalculator`.

```php
// v3
$zmanim->setCalculatorType('Noaa');

// v4
use PhpZmanim\Calculator\MeeusCalculator;
$zman->setAstronomicalCalculator(MeeusCalculator::create());
```

### Renamed methods

| v3 | v4 |
|----|----|
| `getCalendar()` | `getDate()` |
| `setCalendar()` | `setDate()` |
| `isUseElevation()` | `getUseElevation()` |
| `isUseAstronomicalChatzos()` | `getUseAstronomicalChatzos()` |
| `isUseAstronomicalChatzosForOtherZmanim()` | `getUseAstronomicalChatzosForOtherZmanim()` |
| `getChatzosAsHalfDay()` | `getChatzosHayomAsHalfDay()` |
| `getFixedLocalChatzos()` | `getFixedLocalChatzosHayom()` |
| `getAlos60()` | `getAlos60Minutes()` |
| `getAlos72()` | `getAlos72Minutes()` |
| `getAlos90()` | `getAlos90Minutes()` |
| `getAlos96()` | `getAlos96Minutes()` |
| `getAlos120()` | `getAlos120Minutes()` |
| `getTzais50()` | `getTzais50Minutes()` |
| `getTzais60()` | `getTzais60Minutes()` |
| `getTzais72()` | `getTzais72Minutes()` |
| `getTzais90()` | `getTzais90Minutes()` |
| `getTzais96()` | `getTzais96Minutes()` |
| `getTzais120()` | `getTzais120Minutes()` |
| `getSofZmanTfilahAteretTorah()` | `getSofZmanTfilaAteretTorah()` |
| `getPlagAlos16Point1ToTzaisGeonim7Point083Degrees()` | `getPlagAlos16Point1DegreesToTzaisGeonim7Point083Degrees()` |
| `getSofZmanShmaAlos16Point1ToTzaisGeonim7Point083Degrees()` | `getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees()` |
| `getShaahZmanisAlos16Point1ToTzais3Point7()` | `getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point7Degrees()` |
| `getShaahZmanisAlos16Point1ToTzais3Point8()` | `getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point8Degrees()` |

### Removed methods

| v3 | Replacement in v4 |
|----|-------------------|
| `get()`, `__get()` | call `getX()` directly (or add a `__get()` subclass, above) |
| `format()` (on the zmanim object) | formatting moved to `JewishDate`: `$jewishDate->format()->...` |
| `jewishCalendar()` | build a `JewishDate` directly |
| `setCalculatorType()` | `setAstronomicalCalculator(SomeCalculator::create())` |
| `getTemporalHour()` | use the `getShaahZmanis*()` methods (they return milliseconds) |
| `getTimeOffset()` | now internal |
| `getUTCSunrise()`, `getUTCSunset()`, `getUTCSeaLevelSunrise()`, `getUTCSeaLevelSunset()` | now internal to the calculators |
| `getSofZmanShmaFixedLocal()`, `getSofZmanTfilaFixedLocal()` | use the `...ToFixedLocalChatzos` variants |
| `getSofZmanShmaKolEliyahu()` | removed |
| `getMinchaGedolaBaalHatanyaGreaterThan30()` | `getMinchaGedolaBaalHatanya()` / `getMinchaGedolaGRAGreaterThan30()` |

The Geonim tzais methods were re-based on KosherJava's revised zeniths. These variants were removed:

`getTzaisGeonim3Point65Degrees()`, `getTzaisGeonim3Point676Degrees()`, `getTzaisGeonim4Point37Degrees()`, `getTzaisGeonim4Point61Degrees()`, `getTzaisGeonim5Point88Degrees()`

and these were added: `getTzaisGeonim4Point42Degrees()`, `getTzaisGeonim4Point66Degrees()`. If you used one of the removed values, switch to the closest current method, or compute it yourself with `getSunsetOffsetByDegrees()`.

---

## GeoLocation

`GeoLocation` moved out of the `Geo` sub-namespace, and its **constructor arguments were reordered** — the location name moved from first to last and is now optional.

```php
// v3
use PhpZmanim\Geo\GeoLocation;
$geo = new GeoLocation("New York City", 40.850519, -73.929214, 200, "America/New_York");

// v4  (name is last and optional)
use PhpZmanim\GeoLocation;
$geo = GeoLocation::create(40.850519, -73.929214, 200, "America/New_York", "New York City");
```

v4 signature: `GeoLocation::create($latitude, $longitude, $elevation, $timezone, $locationName)`.

Renamed and removed methods:

| v3 | v4 |
|----|----|
| `getTimeZone()` | `getTimezone()` |
| `setTimeZone()` | `setTimezone()` |
| `setLatitudeFromDegrees()` | *(removed)* |
| `setLongitudeFromDegrees()` | *(removed)* |
| `getStandardTimeOffset()` | *(removed)* |
| `GeoLocationUtils::*` (static geodesic/rhumb-line helpers) | instance methods on `GeoLocation` (e.g. `$locationA->getGeodesicDistance($locationB)`) |

---

## JewishCalendar / JewishDate → JewishDate

v3's `JewishDate` (dates) and `JewishCalendar` (holidays, parsha, daf, etc.) were merged into a single `PhpZmanim\JewishDate`. Everything a `JewishCalendar` did is now on `JewishDate`.

### Creating the object

Use the static factories instead of `new`:

```php
// v3
use PhpZmanim\HebrewCalendar\JewishCalendar;
$jc = new JewishCalendar(5784, 7, 15);          // Jewish date
$jc = new JewishCalendar(5784, 7, 15, true);    // in Israel

// v4
use PhpZmanim\JewishDate;
$jd = JewishDate::create(5784, 7, 15);          // Jewish date
$jd = JewishDate::create(5784, 7, 15, true);    // in Israel
$jd = JewishDate::createFromDate(2023, 9, 30);  // from a Gregorian date
```

Holiday and calendar predicates (`isSuccos()`, `isRoshChodesh()`, `getDayOfOmer()`, `getParshah()`, …) now live directly on `JewishDate`.

### Renamed / removed methods

| v3 | v4 |
|----|----|
| `getGregorianCalendar()` | `toCarbon()` |
| `getMoladAsDate()` | `getMoladAsCarbon()` |
| `isGregorianLeapYear()` | *(now internal)* |
| `getLastDayOfGregorianMonth()` | *(now internal)* |
| `getChalakimSinceMoladTohu()` | *(now internal)* |
| `getJewishCalendarElapsedDays()` | *(now internal)* |
| `__debugInfo()` | *(removed)* |

v4 also adds `getYomTov()` (returns a `PhpZmanim\Torah\YomTov` enum; the older `getYomTovIndex()` is still available), `getTekufaAsCarbon()`, and `getDafYomiYerushalmi()`.

### Daf yomi

`Daf` moved to `PhpZmanim\Torah\Daf` and is now an **immutable value object**. Get the daf from a `JewishDate` rather than the standalone calculators:

```php
// v3
use PhpZmanim\HebrewCalendar\YomiCalculator;
$daf = YomiCalculator::getDafYomiBavli($jewishCalendar);
$daf->getMasechtaTransliterated();  // "Bava Metzia"

// v4
$daf = $jewishDate->getDafYomiBavli();               // PhpZmanim\Torah\Daf
$daf->getMasechta();                                 // a masechta enum
$daf->getMasechta()->english();                      // "Bava Metzia"
$daf->getDaf();                                       // 92
$jewishDate->format()->english()->dafYomiBavli();    // "Bava Metzia 92"
```

`Daf` method changes:

| v3 | v4 |
|----|----|
| `getMasechta()` *(returned a Hebrew string)* | `getMasechta()` *(returns a `Nameable` enum; call `->hebrew()` / `->english()`)* |
| `getMasechtaTransliterated()` | `getMasechta()->english()` |
| `getMasechtaNumber()`, `setMasechtaNumber()` | *(removed — immutable)* |
| `setDaf()` | *(removed — immutable)* |
| `getYerushalmiMasechta*()` | `$jewishDate->getDafYomiYerushalmi()` (+ formatter) |

### Tefila rules

In v3, `TefilaRules` was a class of **static** methods that took a `JewishCalendar`. In v4 they are **instance methods on `JewishDate`** — call them on the date directly, with no argument:

```php
// v3
use PhpZmanim\HebrewCalendar\TefilaRules;
TefilaRules::isTachanunRecitedShacharis($jewishCalendar);

// v4
$jewishDate->isTachanunRecitedShacharis();
```

The runtime predicates keep the same names (`isTachanunRecitedShacharis`, `isTachanunRecitedMincha`, `isVeseinTalUmatarRecited`, `isMashivHaruachRecited`, `isHallelRecited`, `isYaalehVeyavoRecited`, …).

The **configuration flags** were also static in v3 and are now per-instance. Their setters keep the same names, but the boolean getters gained a `get` prefix:

| v3 (static getter) | v4 (instance getter) |
|--------------------|----------------------|
| `isTachanunRecitedSundays()` | `getIsTachanunRecitedSundays()` |
| `isTachanunRecitedFridays()` | `getIsTachanunRecitedFridays()` |
| `isTachanunRecitedWeekOfPurim()` | `getIsTachanunRecitedWeekOfPurim()` |
| `isTachanunRecitedWeekOfHod()` | `getIsTachanunRecitedWeekOfHod()` |
| `isTachanunRecitedEndOfTishrei()` | `getIsTachanunRecitedEndOfTishrei()` |
| `isTachanunRecited13SivanOutOfIsrael()` | `getIsTachanunRecited13SivanOutOfIsrael()` |
| `isTachanunRecited15IyarOutOfIsrael()` | `getIsTachanunRecited15IyarOutOfIsrael()` |
| `isTachanunRecitedMinchaErevLagBaomer()` | `getIsTachanunRecitedMinchaErevLagBaomer()` |
| `isTachanunRecitedShivasYemeiHamiluim()` | `getIsTachanunRecitedShivasYemeiHamiluim()` |
| `isTachanunRecitedWeekAfterShavuos()` | `getIsTachanunRecitedWeekAfterShavuos()` |
| `isTachanunRecitedPesachSheni()` | `getIsTachanunRecitedPesachSheni()` |
| `isTachanunRecitedMinchaAllYear()` | `getIsTachanunRecitedMinchaAllYear()` |
| `isMizmorLesodaRecitedErevYomKippurAndPesach()` | `getIsMizmorLesodaRecitedErevYomKippurAndPesach()` |

The matching `setTachanunRecited...()` / `setMizmorLesoda...()` setters keep their v3 names — call them on the `JewishDate` instance.

---

## Formatting: HebrewDateFormatter → fluent formatter

The stateful `HebrewDateFormatter` is gone. Formatting is now fluent and immutable off a `JewishDate`: call `format()`, choose a language with `hebrew()` or `english()`, then the piece you want. Instead of toggling `setHebrewFormat(true)`, you pick the language method.

```php
// v3
use PhpZmanim\HebrewCalendar\HebrewDateFormatter;
$f = new HebrewDateFormatter();
$f->setHebrewFormat(true);
$f->format($jewishDate);      // Hebrew date
$f->formatParsha($jewishDate);

// v4
$jewishDate->format()->hebrew()->date();      // Hebrew date
$jewishDate->format()->hebrew()->parshah();
$jewishDate->format()->english()->parshah();  // English
```

Method mapping:

| v3 (`HebrewDateFormatter`) | v4 |
|----------------------------|----|
| `format($jd)` | `$jd->format()->hebrew()->date()` / `->english()->date()` |
| `setHebrewFormat(true/false)` | choose `->hebrew()` or `->english()` |
| `formatParsha($jd)` | `$jd->format()->{lang}->parshah()` |
| `formatSpecialParsha($jd)` | `$jd->format()->{lang}->specialShabbos()` |
| `formatYomTov($jd)` | `$jd->format()->{lang}->yomTov()` |
| `formatRoshChodesh($jd)` | `$jd->format()->{lang}->roshChodesh()` |
| `formatOmer($jd)` | `$jd->format()->{lang}->omer()` |
| `formatDayOfWeek($jd)` | `$jd->format()->{lang}->dayOfWeek()` |
| `formatMonth($jd)` | `$jd->format()->{lang}->month()` |
| `formatDafYomiBavli($daf)` | `$jd->format()->{lang}->dafYomiBavli()` |
| `formatDafYomiYerushalmi($daf)` | `$jd->format()->{lang}->dafYomiYerushalmi()` |
| `getFormattedKviah($year)` | `$jd->format()->hebrew()->kviah()` *(Hebrew only)* |
| `formatHebrewNumber($n)` | *(internal — used by `date()` etc.)* |

Where `{lang}` is `hebrew` or `english`.

The formatting **options** on the old formatter no longer exist — the output is now fixed. These have no v4 equivalent:

`setUseGershGershayim()`, `setUseFinalFormLetters()`, `setUseLongHebrewYears()`, `setLongWeekFormat()`, `setHebrewOmerPrefix()`, `setTransliteratedMonthList()`, `setTransliteratedHolidayList()`, `setTransliteratedParshiosList()`, `setTransliteratedShabbosDayOfWeek()`.

Hebrew output uses gershayim, short (no-thousands) years, and non-final letter forms; English output uses a fixed Ashkenazi transliteration.

---

## What's new in v4

Not required for migration, but worth knowing:

- Two additional calculators: `MeeusCalculator` and `SPACalculator`.
- Yerushalmi daf yomi via `$jewishDate->getDafYomiYerushalmi()`.
- Erev Pesach chametz zmanim, high-latitude "polar" zmanim, and a split between `getChatzosHayom()` (midday) and `getChatzosHalayla()` (midnight).
- `PhpZmanim\Torah\Parshah` and `PhpZmanim\Torah\YomTov` enums with `->hebrew()` / `->english()`.

See the [detailed usage guide](USAGE.md) for the full v4 API.
