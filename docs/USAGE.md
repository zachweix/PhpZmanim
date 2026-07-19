# PhpZmanim — Detailed Usage

This is the detailed reference for [PhpZmanim](../README.md), a PHP port of the [KosherJava Zmanim API](https://kosherjava.com). See the [KosherJava documentation](https://kosherjava.com) for the meaning of every individual zman and calendar method — this document covers how to call them from PHP.

**Contents**

- [Zmanim](#zmanim)
  - [Creating a `Zman`](#creating-a-zman)
  - [Configuration](#configuration)
  - [Calculators](#calculators)
  - [Full list of zmanim](#full-list-of-zmanim)
  - [Shaah Zmanim (temporal hours)](#shaah-zmanim-temporal-hours)
  - [Conditional zmanim](#conditional-zmanim)
  - [Custom zmanim](#custom-zmanim)
- [The Jewish Calendar](#the-jewish-calendar)
  - [Creating a `JewishDate`](#creating-a-jewishdate)
  - [Calendar information](#calendar-information)
  - [Moving between dates](#moving-between-dates)
  - [Formatting](#formatting)

---

## Zmanim

### Creating a `Zman`

```php
use PhpZmanim\Zman;

$zman = Zman::create(2019, 2, 22, 40.0721087, -74.2400243, 39.57, 'America/New_York');
```

The arguments, in order, are all optional:

| Argument     | Default    | Notes                                                 |
|--------------|------------|-------------------------------------------------------|
| `$year`      | today      | Gregorian year                                        |
| `$month`     | today      | Gregorian month                                       |
| `$day`       | today      | Gregorian day                                         |
| `$latitude`  | `51.4772`  | Decimal degrees                                       |
| `$longitude` | `0.0`      | Decimal degrees                                       |
| `$elevation` | `0.0`      | Meters                                                 |
| `$timezone`  | `'GMT'`    | Any PHP timezone identifier                           |

The defaults place you at Greenwich on today's date at sea level. If you pass `null` for the year, month, or day, today's **UTC** date is used, which may differ from your local date (e.g. 9:00 PM in New York on February 21 is 2:00 AM UTC on February 22).

Every zman is retrieved with a `getX()` method and returns a [Carbon](https://carbon.nesbot.com/) instance in the timezone you supplied:

```php
$zman->getSunset();                   // Carbon
$zman->getSunset()->format('g:i A');  // 5:41 PM
```

You can move the same object to another date instead of recreating it:

```php
$zman->setDate(2019, 4, 19);  // set a new Gregorian date
$zman->addDays(1);            // advance one day
$zman->subDays(1);            // go back one day
```

### Configuration

```php
$zman->setUseElevation(true);                  // factor the elevation set at creation (default false)
$zman->setCandleLightingOffset(18);            // minutes before sunset for candle lighting (default 18)
$zman->setAteretTorahSunsetOffset(40);         // minutes after sunset for Ateret Torah zmanim (default 40)
$zman->setUseAstronomicalChatzos(true);        // use astronomical (solar-transit) chatzos (default true)
$zman->setUseAstronomicalChatzosForOtherZmanim(false); // base other zmanim on astronomical chatzos (default false)
```

Each setter returns the `Zman` instance, so calls can be chained.

### Calculators

The default engine is the `NoaaCalculator`. Four are available:

- `PhpZmanim\Calculator\NoaaCalculator` (default)
- `PhpZmanim\Calculator\MeeusCalculator` — the high-accuracy method of Jean Meeus
- `PhpZmanim\Calculator\SPACalculator` — the NREL Solar Position Algorithm, the most accurate option
- `PhpZmanim\Calculator\SunTimesCalculator` — **deprecated**; a legacy algorithm retained for backward compatibility and as a reference for validating historically calculated times. Prefer one of the above for new code.

Select one with the `create()` factory, either at construction or afterward:

```php
use PhpZmanim\Calculator\MeeusCalculator;

$zman = Zman::create(2019, 2, 22, 40.0721087, -74.2400243, 39.57, 'America/New_York', MeeusCalculator::create());
// or:
$zman->setAstronomicalCalculator(MeeusCalculator::create());
```

#### Tuning a calculator

Every calculator exposes the constants it uses. Each setter returns the calculator, so calls can be chained:

```php
$calculator = MeeusCalculator::create();

$calculator->getRefraction();               // atmospheric refraction, in degrees (default 34/60)
$calculator->getSolarRadius();              // solar radius, in degrees (default 16/60)
$calculator->getEarthRadius();              // earth radius, in KM (default 6371.0088)
$calculator->getUseApparentSolarRadius();   // use the seasonally varying solar radius (default true)

$calculator->setUseApparentSolarRadius(false);
```

Note that `setSolarRadius()` turns off the apparent solar radius, since supplying an explicit radius implies a fixed one. Call `setUseApparentSolarRadius(true)` afterwards if you want it back on.

### Full list of zmanim

Every method below takes no arguments, returns a Carbon, and is called on a `Zman` instance (e.g. `$zman->getSunrise()`). See the [KosherJava documentation](https://kosherjava.com) for what each one represents.

> **Some zmanim are deprecated.** Following KosherJava, a number of these methods carry an `@deprecated` docblock. In almost every case this does **not** mean the method will be removed — it means the zman is *lechumra* only, because it returns a time so early or so late that relying on it *lekula* can lead to a real halachic problem. Your editor will show these as struck through. Read the docblock before using one; it explains the specific risk. The deprecated zmanim are the `getPlagHamincha*` variants listed below (except `getPlagHamincha60Minutes`), `getPlagAlosToSunset`, `getAlos120Minutes`, `getAlos120Zmanis`, `getAlos26Degrees`, `getTzais120Minutes`, `getTzais120Zmanis`, `getTzais26Degrees`, the four narrow `getTzaisGeonim*` degree variants (3.7, 3.8, 4.42, 4.66), and `getMisheyakir12Point85Degrees`. Separately, `getSunriseSolarDipFromOffset()` and `getSunsetSolarDipFromOffset()` are deprecated for performance — use `getSolarElevation()` instead.

#### Sunrise & dawn twilight

```php
$zman->getSunrise();                    // sunrise at the configured elevation
$zman->getSeaLevelSunrise();            // sunrise at sea level
$zman->getBeginCivilTwilight();         // sun's zenith at 96 degrees
$zman->getBeginNauticalTwilight();      // sun's zenith at 102 degrees
$zman->getBeginAstronomicalTwilight();  // sun's zenith at 108 degrees
```

#### Sunset & dusk twilight

```php
$zman->getSunset();                     // sunset at the configured elevation
$zman->getSeaLevelSunset();             // sunset at sea level
$zman->getEndCivilTwilight();           // sun's zenith at 96 degrees
$zman->getEndNauticalTwilight();        // sun's zenith at 102 degrees
$zman->getEndAstronomicalTwilight();    // sun's zenith at 108 degrees
```

#### Alos Hashachar (dawn)

```php
$zman->getAlos16Point1Degrees();
$zman->getAlos18Degrees();
$zman->getAlos19Degrees();
$zman->getAlos19Point8Degrees();
$zman->getAlos26Degrees();
$zman->getAlos60Minutes();
$zman->getAlos72Minutes();
$zman->getAlos72Zmanis();
$zman->getAlos90Minutes();
$zman->getAlos90Zmanis();
$zman->getAlos96Minutes();
$zman->getAlos96Zmanis();
$zman->getAlos120Minutes();
$zman->getAlos120Zmanis();
$zman->getAlosBaalHatanya();
```

#### Misheyakir

```php
$zman->getMisheyakir7Point65Degrees();
$zman->getMisheyakir9Point5Degrees();
$zman->getMisheyakir10Point2Degrees();
$zman->getMisheyakir11Degrees();
$zman->getMisheyakir11Point5Degrees();
$zman->getMisheyakir12Point85Degrees();
```

#### Sof Zman Shma (latest Shema)

```php
$zman->getSofZmanShmaGRA();
$zman->getSofZmanShmaMGA16Point1Degrees();
$zman->getSofZmanShmaMGA18Degrees();
$zman->getSofZmanShmaMGA19Point8Degrees();
$zman->getSofZmanShmaMGA72Minutes();
$zman->getSofZmanShmaMGA72MinutesZmanis();
$zman->getSofZmanShmaMGA90Minutes();
$zman->getSofZmanShmaMGA90MinutesZmanis();
$zman->getSofZmanShmaMGA96Minutes();
$zman->getSofZmanShmaMGA96MinutesZmanis();
$zman->getSofZmanShmaMGA120Minutes();
$zman->getSofZmanShma3HoursBeforeChatzos();
$zman->getSofZmanShmaAlos16Point1ToSunset();
$zman->getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees();
$zman->getSofZmanShmaAteretTorah();
$zman->getSofZmanShmaBaalHatanya();
$zman->getSofZmanShmaGRASunriseToFixedLocalChatzos();
$zman->getSofZmanShmaMGA16Point1DegreesToFixedLocalChatzos();
$zman->getSofZmanShmaMGA18DegreesToFixedLocalChatzos();
$zman->getSofZmanShmaMGA72MinutesToFixedLocalChatzos();
$zman->getSofZmanShmaMGA90MinutesToFixedLocalChatzos();
```

#### Sof Zman Tfila (latest Shacharis)

```php
$zman->getSofZmanTfilaGRA();
$zman->getSofZmanTfilaMGA16Point1Degrees();
$zman->getSofZmanTfilaMGA18Degrees();
$zman->getSofZmanTfilaMGA19Point8Degrees();
$zman->getSofZmanTfilaMGA72Minutes();
$zman->getSofZmanTfilaMGA72MinutesZmanis();
$zman->getSofZmanTfilaMGA90Minutes();
$zman->getSofZmanTfilaMGA90MinutesZmanis();
$zman->getSofZmanTfilaMGA96Minutes();
$zman->getSofZmanTfilaMGA96MinutesZmanis();
$zman->getSofZmanTfilaMGA120Minutes();
$zman->getSofZmanTfila2HoursBeforeChatzos();
$zman->getSofZmanTfilaAteretTorah();
$zman->getSofZmanTfilaBaalHatanya();
$zman->getSofZmanTfilaGRASunriseToFixedLocalChatzos();
```

#### Chatzos (midday)

```php
$zman->getChatzosHayom();            // astronomical (solar transit) chatzos
$zman->getChatzosHayomAsHalfDay();   // halfway between sunrise and sunset
$zman->getFixedLocalChatzosHayom();  // fixed local chatzos
```

#### Mincha Gedola

```php
$zman->getMinchaGedolaGRA();
$zman->getMinchaGedola16Point1Degrees();
$zman->getMinchaGedola30Minutes();
$zman->getMinchaGedola72Minutes();
$zman->getMinchaGedolaAhavatShalom();
$zman->getMinchaGedolaAteretTorah();
$zman->getMinchaGedolaBaalHatanya();
$zman->getMinchaGedolaGRAGreaterThan30();
$zman->getMinchaGedolaGRAFixedLocalChatzos30Minutes();
```

#### Mincha Ketana

```php
$zman->getMinchaKetanaGRA();
$zman->getMinchaKetana16Point1Degrees();
$zman->getMinchaKetana72Minutes();
$zman->getMinchaKetanaAhavatShalom();
$zman->getMinchaKetanaAteretTorah();
$zman->getMinchaKetanaBaalHatanya();
$zman->getMinchaKetanaGRAFixedLocalChatzosToSunset();
$zman->getSamuchLeMinchaKetanaGRA();
$zman->getSamuchLeMinchaKetana16Point1Degrees();
$zman->getSamuchLeMinchaKetana72Minutes();
```

#### Plag Hamincha

```php
$zman->getPlagHaminchaGRA();
$zman->getPlagHamincha16Point1Degrees();
$zman->getPlagHamincha18Degrees();
$zman->getPlagHamincha19Point8Degrees();
$zman->getPlagHamincha26Degrees();
$zman->getPlagHamincha60Minutes();
$zman->getPlagHamincha72Minutes();
$zman->getPlagHamincha72MinutesZmanis();
$zman->getPlagHamincha90Minutes();
$zman->getPlagHamincha90MinutesZmanis();
$zman->getPlagHamincha96Minutes();
$zman->getPlagHamincha96MinutesZmanis();
$zman->getPlagHamincha120Minutes();
$zman->getPlagHamincha120MinutesZmanis();
$zman->getPlagAlosToSunset();
$zman->getPlagAlos16Point1DegreesToTzaisGeonim7Point083Degrees();
$zman->getPlagAhavatShalom();
$zman->getPlagHaminchaAteretTorah();
$zman->getPlagHaminchaBaalHatanya();
$zman->getPlagHaminchaGRAFixedLocalChatzosToSunset();
```

#### Candle lighting & melacha

```php
$zman->getCandleLighting();  // sea-level sunset minus the candle-lighting offset (default 18 minutes)

// Is melacha forbidden at a given moment? Unlike the zmanim above, this takes arguments
// and returns a bool rather than a Carbon.
$zman->isAssurBemelacha($currentTime, $tzais, $inIsrael);
```

`JewishDate` has a day-level counterpart, `isAssurBemelacha()`, which takes no time arguments and answers for the day as a whole.

#### Bain Hashmashos (twilight)

```php
$zman->getBainHashmashosRT13Point24Degrees();
$zman->getBainHashmashosRT58Point5Minutes();
$zman->getBainHashmashosRT13Point5MinutesBefore7Point083Degrees();
$zman->getBainHashmashosRT2Stars();
$zman->getBainHashmashosYereim18Minutes();
$zman->getBainHashmashosYereim16Point875Minutes();
$zman->getBainHashmashosYereim13Point5Minutes();
$zman->getBainHashmashosYereim3Point05Degrees();
$zman->getBainHashmashosYereim2Point8Degrees();
$zman->getBainHashmashosYereim2Point1Degrees();
```

#### Tzais (nightfall)

```php
$zman->getTzais16Point1Degrees();
$zman->getTzais18Degrees();
$zman->getTzais19Point8Degrees();
$zman->getTzais26Degrees();
$zman->getTzais50Minutes();
$zman->getTzais60Minutes();
$zman->getTzais72Minutes();
$zman->getTzais72Zmanis();
$zman->getTzais90Minutes();
$zman->getTzais90Zmanis();
$zman->getTzais96Minutes();
$zman->getTzais96Zmanis();
$zman->getTzais120Minutes();
$zman->getTzais120Zmanis();
$zman->getTzaisAteretTorah();
$zman->getTzaisBaalHatanya();
$zman->getTzaisGeonim3Point7Degrees();
$zman->getTzaisGeonim3Point8Degrees();
$zman->getTzaisGeonim4Point42Degrees();
$zman->getTzaisGeonim4Point66Degrees();
$zman->getTzaisGeonim4Point8Degrees();
$zman->getTzaisGeonim5Point95Degrees();
$zman->getTzaisGeonim6Point45Degrees();
$zman->getTzaisGeonim7Point083Degrees();
$zman->getTzaisGeonim7Point67Degrees();
$zman->getTzaisGeonim8Point5Degrees();
$zman->getTzaisGeonim9Point3Degrees();
$zman->getTzaisGeonim9Point75Degrees();
```

#### Chatzos Halayla (midnight)

```php
$zman->getSolarMidnight();
$zman->getChatzosHalayla();
```

#### Sun transit

```php
$zman->getSunTransit();  // the moment the sun crosses the local meridian
```

### Shaah Zmanim (temporal hours)

A _shaah zmanis_ is a halachic hour — one-twelfth of the day, whose length varies with the season and the opinion used. Unlike the zmanim above, these methods **do not return a Carbon**. Each returns a **`float`: the length of one shaah zmanis in milliseconds.** For example, `getShaahZmanisGRA()` might return `3299103.85` (about 55 minutes). Divide by `60000` for minutes.

```php
$zman->getShaahZmanisGRA();
$zman->getShaahZmanisBaalHatanya();
$zman->getShaahZmanisAteretTorah();
$zman->getShaahZmanis16Point1Degrees();
$zman->getShaahZmanis18Degrees();
$zman->getShaahZmanis19Point8Degrees();
$zman->getShaahZmanis26Degrees();
$zman->getShaahZmanis60Minutes();
$zman->getShaahZmanis72Minutes();
$zman->getShaahZmanis72MinutesZmanis();
$zman->getShaahZmanis90Minutes();
$zman->getShaahZmanis90MinutesZmanis();
$zman->getShaahZmanis96Minutes();
$zman->getShaahZmanis96MinutesZmanis();
$zman->getShaahZmanis120Minutes();
$zman->getShaahZmanis120MinutesZmanis();
$zman->getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point7Degrees();
$zman->getShaahZmanisAlos16Point1DegreesToTzaisGeonim3Point8Degrees();
$zman->getShaahZmanisAlos16Point1DegreesToTzaisGeonim7Point083Degrees();
```

### Conditional zmanim

These zmanim apply only in a particular context and return `null` on any other date or location — that is expected behavior, not an error. Always create the `Zman` for a date (and location) where the zman is relevant.

#### Erev Pesach — Sof Zman Achilas / Biur Chametz

Return a time only when the date is erev Pesach (14 Nissan):

```php
$zman->getSofZmanAchilasChametzGRA();
$zman->getSofZmanAchilasChametzMGA72Minutes();
$zman->getSofZmanAchilasChametzMGA72MinutesZmanis();
$zman->getSofZmanAchilasChametzMGA16Point1Degrees();
$zman->getSofZmanAchilasChametzBaalHatanya();
$zman->getSofZmanBiurChametzGRA();
$zman->getSofZmanBiurChametzMGA72Minutes();
$zman->getSofZmanBiurChametzMGA72MinutesZmanis();
$zman->getSofZmanBiurChametzMGA16Point1Degrees();
$zman->getSofZmanBiurChametzBaalHatanya();
```

#### Kiddush Levana & the Molad

Return a time only when the date falls within the relevant window of the Jewish month:

```php
$zman->getZmanMolad();
$zman->getTchilasZmanKidushLevana3Days();
$zman->getTchilasZmanKidushLevana7Days();
$zman->getSofZmanKidushLevanaBetweenMoldos();
$zman->getSofZmanKidushLevana15Days();
```

#### Polar (high-latitude) zmanim

Alternative calculations intended for high latitudes, where they return values that the standard zmanim cannot:

```php
$zman->getPolarSunriseBenIshChai();
$zman->getPolarSunsetBenIshChai();
$zman->getPolarPlagHaminchaBenIshChai();
$zman->getPolarPlagHaminchaTeshuvosVehanhagos();
$zman->getPolarStartOfDayTeshuvosVehanhagos();
```

### Custom zmanim

If you need a zman that isn't listed, start from a sunrise/sunset offset. These return a Carbon you can pass into the general calculators below:

```php
$zman->getSunriseOffsetByDegrees($offsetZenith);
$zman->getSunsetOffsetByDegrees($offsetZenith);
$zman->getSunriseSolarDipFromOffset($minutes);
$zman->getSunsetSolarDipFromOffset($minutes);
```

The general zman calculators take a start-of-day and end-of-day and compute the proportional time between them:

```php
$startOfDay = $zman->getAlos16Point1Degrees();
$endOfDay   = $zman->getTzais16Point1Degrees();

$zman->getSofZmanShma($startOfDay, $endOfDay);
$zman->getSofZmanTfila($startOfDay, $endOfDay);
$zman->getMinchaGedola($startOfDay, $endOfDay);
$zman->getMinchaKetana($startOfDay, $endOfDay);
$zman->getPlagHamincha($startOfDay, $endOfDay);
```

For example, sof zman shma from sunrise to sunset:

```php
$zman->getSofZmanShma($zman->getSunrise(), $zman->getSunset());
```

---

## The Jewish Calendar

### Creating a `JewishDate`

```php
use PhpZmanim\JewishDate;

$jewishDate = JewishDate::createFromDate(2023, 9, 30);  // from a Gregorian year, month, day
$jewishDate = JewishDate::create(5784, 7, 15);          // from a Jewish year, month, day
```

Both give the same day. With no arguments, `JewishDate::create()` uses today. Pass `true` as the fourth argument to `create()` or `createFromDate()` for the Israel holiday schedule (one day of yom tov):

```php
$jewishDate = JewishDate::create(5784, 7, 15, true);  // in Israel
```

### Configuration

```php
$jewishDate->setInIsrael(true);            // use the Israel holiday schedule (default false)
$jewishDate->setIsMukafChoma(true);        // treat the location as a walled city for Purim (default false)
$jewishDate->setUseModernHolidays(true);   // recognize Yom Haatzmaut, Yom Hazikaron, etc. (default false)
```

Each has a matching getter (`getInIsrael()`, `getIsMukafChoma()`, `getUseModernHolidays()`), and each setter returns the `JewishDate` instance, so calls can be chained.

### Calendar information

```php
$jewishDate->getJewishYear();        // 5784
$jewishDate->getJewishMonth();       // 7 (Tishrei)
$jewishDate->getJewishDayOfMonth();  // 15
$jewishDate->getDayOfWeek();         // 1 (Sunday) through 7 (Shabbos)
$jewishDate->toCarbon();             // the Gregorian date as a Carbon instance, you can pass an optional timezone to default to your requested timezone

// Holiday and day-type predicates (a selection — see the KosherJava docs for the full set)
$jewishDate->isYomTov();
$jewishDate->isSuccos();
$jewishDate->isRoshHashana();
$jewishDate->isYomKippur();
$jewishDate->isPesach();
$jewishDate->isCholHamoed();
$jewishDate->isChanukah();
$jewishDate->isRoshChodesh();
$jewishDate->isTaanis();

// Omer, parsha and daf yomi
$jewishDate->getDayOfOmer();           // day of the omer, or -1 if not during the omer
$jewishDate->getParshah();             // the Parshah read on this day (only on Shabbos)
$jewishDate->getUpcomingParshah();     // the next Shabbos's Parshah (any day of the week)
$jewishDate->getDafYomiBavli();        // a Daf value object
$jewishDate->getDafYomiYerushalmi();   // a Daf value object, or null on Yom Kippur / Tisha B'Av
```

`getDafYomiBavli()` returns a `Daf` value object; read it with `getMasechta()` (a masechta enum) and `getDaf()` (the page number), or format it directly (see below).

### Molad and tekufa

```php
$jewishDate->getMolad();               // a JewishDate carrying the molad's day, hours and chalakim
$jewishDate->getMoladAsCarbon();       // the molad as a Carbon instant
$jewishDate->getTekufaAsCarbon(false); // the tekufa as a Carbon instant, or null if not a tekufa day
```

**These return Jerusalem standard time (`GMT+2`), not your own timezone.** The molad and the tekufa are defined as Jerusalem quantities, so there is no other sensible anchor — but it means calling `format()` on the result shows Jerusalem wall-clock time. The instant itself is correct; to read it where you are, change the timezone on the Carbon object:

```php
$molad = JewishDate::createFromDate(2024, 1, 11)->getMoladAsCarbon();

$molad->format('Y-m-d H:i:s T');                              // 2024-01-11 08:24:16 GMT+0200
$molad->setTimezone('America/New_York')->format('Y-m-d H:i:s T'); // 2024-01-11 01:24:16 EST
```

Note that the offset is a fixed `GMT+2` rather than `Asia/Jerusalem`, so it deliberately does not follow Israeli daylight saving — the molad is reckoned in standard time. This matches KosherJava.

### Moving between dates

```php
$jewishDate->addDays(1);
$jewishDate->subDays(1);
$jewishDate->addMonthsJewish(1);
$jewishDate->addYearsJewish(1);
$jewishDate->addMonthsGregorian(1);
$jewishDate->addYearsGregorian(1);
```

### Formatting

Formatting is fluent and immutable. Call `format()` on a `JewishDate`, then pick a language with `english()` or `hebrew()`, then the piece you want. Every piece returns a `string` (empty when it doesn't apply to the date):

```php
$jewishDate = JewishDate::create(5784, 7, 15);

$jewishDate->format()->english()->date();   // 15 Tishrei, 5784
$jewishDate->format()->hebrew()->date();     // ט״ו תשרי תשפ״ד

$jewishDate->format()->english()->yomTov();  // Succos
$jewishDate->format()->hebrew()->yomTov();    // סוכות
```

The available pieces, in both languages:

```php
$f = $jewishDate->format()->english();  // or ->hebrew()

$f->date();             // e.g. "15 Tishrei, 5784"
$f->parshah();          // the Parshah, only on Shabbos (empty otherwise)
$f->specialShabbos();   // e.g. Shabbos Shekalim (empty if none)
$f->yomTov();           // the holiday (empty if none)
$f->roshChodesh();      // e.g. "Rosh Chodesh Cheshvan" (empty if not Rosh Chodesh)
$f->omer();             // e.g. "Omer 6" (empty if not during the omer)
$f->dafYomiBavli();     // e.g. "Bava Metzia 92"
$f->dafYomiYerushalmi();
```

Some examples with real values:

```php
// Parsha — note parshah() is only populated on Shabbos itself
JewishDate::create(5784, 7, 29)->format()->english()->parshah();  // Bereshis
JewishDate::create(5784, 7, 29)->format()->hebrew()->parshah();    // בראשית

// On a weekday, use getUpcomingParshah() and format the enum:
JewishDate::create(5784, 7, 26)->getUpcomingParshah()->english();  // Bereshis
JewishDate::create(5784, 7, 26)->getUpcomingParshah()->hebrew();    // בראשית

// Daf yomi
JewishDate::createFromDate(2024, 5, 30)->format()->english()->dafYomiBavli();  // Bava Metzia 92
JewishDate::createFromDate(2024, 5, 30)->format()->hebrew()->dafYomiBavli();    // בבא מציעא צ״ב

// Omer
JewishDate::create(5784, 1, 21)->format()->english()->omer();  // Omer 6
JewishDate::create(5784, 1, 21)->format()->hebrew()->omer();    // ו׳ בעומר

// Rosh Chodesh
JewishDate::create(5784, 8, 1)->format()->english()->roshChodesh();  // Rosh Chodesh Cheshvan
JewishDate::create(5784, 8, 1)->format()->hebrew()->roshChodesh();    // ראש חודש חשון
```

#### Kviah (Hebrew only)

The _kviah_ (the year's type) is available only on the Hebrew formatter:

```php
JewishDate::create(5784, 7, 1)->format()->hebrew()->kviah();  // זחג
```

Because it is Hebrew-specific, `->english()->kviah()` does not exist.

#### Customizing the output

`hebrew()` and `english()` each accept an optional array of options. The formatter stays immutable — the options apply to the formatter you get back, and nothing is stored on the `JewishDate`:

```php
$jewishDate->format()->english(['shabbos' => 'Shabbat'])->dayOfWeek();  // Shabbat
$jewishDate->format()->hebrew(['useGershGershayim' => false])->date();  // טו תשרי תשפד
```

Shared by both languages:

| Option | Type | Default | Effect |
|--------|------|---------|--------|
| `months` | array of 14 | the built-in list | Month names, Nissan through Adar I. Must have exactly 14 entries |
| `names` | array | `[]` | Overrides for holiday, parsha and masechta names — see below |

Hebrew only:

| Option | Type | Default | Effect |
|--------|------|---------|--------|
| `daysOfWeek` | array of 7 | the built-in list | Day names, Sunday first. Must have exactly 7 entries |
| `omerPrefix` | string | `ב` | The letter prefixed to "עומר" |
| `useGershGershayim` | bool | `true` | Add geresh/gershayim marks to numbers |
| `useFinalFormLetters` | bool | `false` | Use a final-form letter when a year ends in a round ten (`תש״פ` → `תש״ף`) |
| `useLongHebrewYears` | bool | `false` | Include the thousands prefix in years (`תשפ״ד` → `ה׳ תשפ״ד`) |

English only:

| Option | Type | Default | Effect |
|--------|------|---------|--------|
| `shabbos` | string | `Shabbos` | What `dayOfWeek()` returns on Shabbos |

**Unknown option keys throw an `InvalidArgumentException`**, so a typo fails loudly instead of being silently ignored. Options are checked per language — passing `useGershGershayim` to `english()` is an error, because it means nothing there.

Note that `useFinalFormLetters` follows KosherJava's rule: the substitution happens only when the number ends in a round ten *and* is not one of the short forms Hebrew writes with a single letter. In practice that means years like 5780 (`תש״פ` → `תש״ף`), never a day of the month — day 20 stays `כ׳` and does not become `ך׳`.

##### Renaming holidays, parshiyos and masechtos

Those names live on the `Parshah`, `YomTov`, `MasechtaBavli` and `MasechtaYerushalmi` enums, so the `names` option overrides them by enum class and case name:

```php
use PhpZmanim\Torah\YomTov;
use PhpZmanim\Torah\MasechtaYerushalmi;

$options = ['names' => [
    YomTov::class => ['SUCCOS' => 'Sukkot', 'SHAVUOS' => 'Shavuot'],
    MasechtaYerushalmi::class => ['BERACHOS' => 'Brachot'],
]];

$jewishDate->format()->english($options)->yomTov();  // Sukkot
```

Keying by enum class matters: `MasechtaBavli` and `MasechtaYerushalmi` share many case names, so a `MasechtaYerushalmi` entry changes only the Yerushalmi output and leaves Bavli alone. Unknown enum classes and unknown case names both throw, so a misspelled case name is caught immediately.

Anything you don't pass keeps its default, and you only need to include the entries you actually want to change.
