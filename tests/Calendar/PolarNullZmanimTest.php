<?php

/**
 * Zmanim PHP API
 * Copyright (C) 2019-2026 Zachary Weixelbaum
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

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PhpZmanim\Zman;

/**
 * Regression coverage for the zmanim whose inputs are themselves uncomputable inside the Arctic
 * and Antarctic circles. When an underlying alos / tzais / sunrise / sunset does not occur, Java
 * propagates a null all the way out through getTimeOffset(), getTemporalHour(),
 * getShaahZmanisBasedZman() and getHalfDayBasedZman(). The PHP port previously declared
 * non-nullable Carbon parameters on getTimeOffset(), getTemporalHour(), getChatzos(),
 * getSofZmanShma(), getMinchaGedola(), getMinchaKetana() and getSamuchLeMinchaKetana(), so those
 * same cases raised a TypeError instead of returning null.
 *
 * Every expected value here is ground truth generated from the current KosherJava
 * (ComprehensiveZmanimCalendar, default NOAACalculator, useElevation false). The fixtures are
 * deliberately mixed: Tromso on the summer solstice returns null for all of them, Tromso in
 * midwinter returns a real time for all but two, and Reykjavik / Anchorage on the solstice sit in
 * between. That combination pins the null handling in both directions, so a fix that over-nulls
 * fails just as loudly as one that throws.
 */
class PolarNullZmanimTest extends TestCase
{
	private const LOCATIONS = [
		'tromsoPolarDay' => ['lat' => 69.6492, 'lon' => 18.9553, 'elev' => 10, 'tz' => 'Europe/Oslo', 'date' => [2026, 6, 21]],
		'tromsoPolarNight' => ['lat' => 69.6492, 'lon' => 18.9553, 'elev' => 10, 'tz' => 'Europe/Oslo', 'date' => [2026, 1, 15]],
		'reykjavikSummer' => ['lat' => 64.1265, 'lon' => -21.8174, 'elev' => 0, 'tz' => 'Atlantic/Reykjavik', 'date' => [2026, 6, 21]],
		'anchorageSummer' => ['lat' => 61.2181, 'lon' => -149.9003, 'elev' => 30, 'tz' => 'America/Anchorage', 'date' => [2026, 6, 21]],
	];

	private function calendarFor(string $location): Zman
	{
		$fixture = self::LOCATIONS[$location];
		[$year, $month, $day] = $fixture['date'];

		return Zman::create($year, $month, $day, $fixture['lat'], $fixture['lon'], $fixture['elev'], $fixture['tz']);
	}

	/*
	|--------------------------------------------------------------------------
	| ZMANIM THAT DEPEND ON AN UNCOMPUTABLE ALOS / TZAIS
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('polarZmanimProvider')]
	public function matchesJavaInsideThePolarCircle(string $location, string $method, string|int|null $expected): void
	{
		$actual = $this->calendarFor($location)->$method();

		if ($expected === null) {
			$this->assertNull($actual);

			return;
		}

		if (is_int($expected)) {
			$this->assertIsFloat($actual);
			$this->assertSame($expected, (int) floor($actual));

			return;
		}

		$this->assertInstanceOf(Carbon::class, $actual);
		$this->assertEqualsWithDelta(
			Carbon::parse($expected, 'UTC')->getPreciseTimestamp() / 1e6,
			$actual->getPreciseTimestamp() / 1e6,
			0.001
		);
	}

	public static function polarZmanimProvider(): array
	{
		return [
			// tromsoPolarDay
			'tromsoPolarDay getAlos120Minutes' => ['tromsoPolarDay', 'getAlos120Minutes', null],
			'tromsoPolarDay getAlos60Minutes' => ['tromsoPolarDay', 'getAlos60Minutes', null],
			'tromsoPolarDay getAlos72Minutes' => ['tromsoPolarDay', 'getAlos72Minutes', null],
			'tromsoPolarDay getAlos90Minutes' => ['tromsoPolarDay', 'getAlos90Minutes', null],
			'tromsoPolarDay getAlos96Minutes' => ['tromsoPolarDay', 'getAlos96Minutes', null],
			'tromsoPolarDay getBainHashmashosRT13Point5MinutesBefore7Point083Degrees' => ['tromsoPolarDay', 'getBainHashmashosRT13Point5MinutesBefore7Point083Degrees', null],
			'tromsoPolarDay getBainHashmashosRT58Point5Minutes' => ['tromsoPolarDay', 'getBainHashmashosRT58Point5Minutes', null],
			'tromsoPolarDay getBainHashmashosYereim13Point5Minutes' => ['tromsoPolarDay', 'getBainHashmashosYereim13Point5Minutes', null],
			'tromsoPolarDay getBainHashmashosYereim16Point875Minutes' => ['tromsoPolarDay', 'getBainHashmashosYereim16Point875Minutes', null],
			'tromsoPolarDay getBainHashmashosYereim18Minutes' => ['tromsoPolarDay', 'getBainHashmashosYereim18Minutes', null],
			'tromsoPolarDay getCandleLighting' => ['tromsoPolarDay', 'getCandleLighting', null],
			'tromsoPolarDay getChatzosHayomAsHalfDay' => ['tromsoPolarDay', 'getChatzosHayomAsHalfDay', null],
			'tromsoPolarDay getMinchaGedola16Point1Degrees' => ['tromsoPolarDay', 'getMinchaGedola16Point1Degrees', null],
			'tromsoPolarDay getMinchaGedola72Minutes' => ['tromsoPolarDay', 'getMinchaGedola72Minutes', null],
			'tromsoPolarDay getMinchaGedolaAteretTorah' => ['tromsoPolarDay', 'getMinchaGedolaAteretTorah', null],
			'tromsoPolarDay getMinchaGedolaBaalHatanya' => ['tromsoPolarDay', 'getMinchaGedolaBaalHatanya', null],
			'tromsoPolarDay getMinchaGedolaGRA' => ['tromsoPolarDay', 'getMinchaGedolaGRA', null],
			'tromsoPolarDay getMinchaGedolaGRAGreaterThan30' => ['tromsoPolarDay', 'getMinchaGedolaGRAGreaterThan30', null],
			'tromsoPolarDay getMinchaKetana16Point1Degrees' => ['tromsoPolarDay', 'getMinchaKetana16Point1Degrees', null],
			'tromsoPolarDay getMinchaKetana72Minutes' => ['tromsoPolarDay', 'getMinchaKetana72Minutes', null],
			'tromsoPolarDay getMinchaKetanaAteretTorah' => ['tromsoPolarDay', 'getMinchaKetanaAteretTorah', null],
			'tromsoPolarDay getMinchaKetanaBaalHatanya' => ['tromsoPolarDay', 'getMinchaKetanaBaalHatanya', null],
			'tromsoPolarDay getMinchaKetanaGRA' => ['tromsoPolarDay', 'getMinchaKetanaGRA', null],
			'tromsoPolarDay getPlagHamincha120Minutes' => ['tromsoPolarDay', 'getPlagHamincha120Minutes', null],
			'tromsoPolarDay getPlagHamincha60Minutes' => ['tromsoPolarDay', 'getPlagHamincha60Minutes', null],
			'tromsoPolarDay getPlagHamincha72Minutes' => ['tromsoPolarDay', 'getPlagHamincha72Minutes', null],
			'tromsoPolarDay getPlagHamincha90Minutes' => ['tromsoPolarDay', 'getPlagHamincha90Minutes', null],
			'tromsoPolarDay getPlagHamincha96Minutes' => ['tromsoPolarDay', 'getPlagHamincha96Minutes', null],
			'tromsoPolarDay getPlagHaminchaAteretTorah' => ['tromsoPolarDay', 'getPlagHaminchaAteretTorah', null],
			'tromsoPolarDay getSamuchLeMinchaKetana16Point1Degrees' => ['tromsoPolarDay', 'getSamuchLeMinchaKetana16Point1Degrees', null],
			'tromsoPolarDay getSamuchLeMinchaKetana72Minutes' => ['tromsoPolarDay', 'getSamuchLeMinchaKetana72Minutes', null],
			'tromsoPolarDay getSamuchLeMinchaKetanaGRA' => ['tromsoPolarDay', 'getSamuchLeMinchaKetanaGRA', null],
			'tromsoPolarDay getShaahZmanis120Minutes' => ['tromsoPolarDay', 'getShaahZmanis120Minutes', null],
			'tromsoPolarDay getShaahZmanis60Minutes' => ['tromsoPolarDay', 'getShaahZmanis60Minutes', null],
			'tromsoPolarDay getShaahZmanis72Minutes' => ['tromsoPolarDay', 'getShaahZmanis72Minutes', null],
			'tromsoPolarDay getShaahZmanis90Minutes' => ['tromsoPolarDay', 'getShaahZmanis90Minutes', null],
			'tromsoPolarDay getShaahZmanis96Minutes' => ['tromsoPolarDay', 'getShaahZmanis96Minutes', null],
			'tromsoPolarDay getShaahZmanisAteretTorah' => ['tromsoPolarDay', 'getShaahZmanisAteretTorah', null],
			'tromsoPolarDay getSofZmanAchilasChametzMGA72Minutes' => ['tromsoPolarDay', 'getSofZmanAchilasChametzMGA72Minutes', null],
			'tromsoPolarDay getSofZmanBiurChametzMGA72Minutes' => ['tromsoPolarDay', 'getSofZmanBiurChametzMGA72Minutes', null],
			'tromsoPolarDay getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees' => ['tromsoPolarDay', 'getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees', null],
			'tromsoPolarDay getSofZmanShmaAlos16Point1ToSunset' => ['tromsoPolarDay', 'getSofZmanShmaAlos16Point1ToSunset', null],
			'tromsoPolarDay getSofZmanShmaAteretTorah' => ['tromsoPolarDay', 'getSofZmanShmaAteretTorah', null],
			'tromsoPolarDay getSofZmanShmaBaalHatanya' => ['tromsoPolarDay', 'getSofZmanShmaBaalHatanya', null],
			'tromsoPolarDay getSofZmanShmaGRA' => ['tromsoPolarDay', 'getSofZmanShmaGRA', null],
			'tromsoPolarDay getSofZmanShmaMGA120Minutes' => ['tromsoPolarDay', 'getSofZmanShmaMGA120Minutes', null],
			'tromsoPolarDay getSofZmanShmaMGA16Point1Degrees' => ['tromsoPolarDay', 'getSofZmanShmaMGA16Point1Degrees', null],
			'tromsoPolarDay getSofZmanShmaMGA18Degrees' => ['tromsoPolarDay', 'getSofZmanShmaMGA18Degrees', null],
			'tromsoPolarDay getSofZmanShmaMGA19Point8Degrees' => ['tromsoPolarDay', 'getSofZmanShmaMGA19Point8Degrees', null],
			'tromsoPolarDay getSofZmanShmaMGA72Minutes' => ['tromsoPolarDay', 'getSofZmanShmaMGA72Minutes', null],
			'tromsoPolarDay getSofZmanShmaMGA72MinutesToFixedLocalChatzos' => ['tromsoPolarDay', 'getSofZmanShmaMGA72MinutesToFixedLocalChatzos', null],
			'tromsoPolarDay getSofZmanShmaMGA72MinutesZmanis' => ['tromsoPolarDay', 'getSofZmanShmaMGA72MinutesZmanis', null],
			'tromsoPolarDay getSofZmanShmaMGA90Minutes' => ['tromsoPolarDay', 'getSofZmanShmaMGA90Minutes', null],
			'tromsoPolarDay getSofZmanShmaMGA90MinutesToFixedLocalChatzos' => ['tromsoPolarDay', 'getSofZmanShmaMGA90MinutesToFixedLocalChatzos', null],
			'tromsoPolarDay getSofZmanShmaMGA90MinutesZmanis' => ['tromsoPolarDay', 'getSofZmanShmaMGA90MinutesZmanis', null],
			'tromsoPolarDay getSofZmanShmaMGA96Minutes' => ['tromsoPolarDay', 'getSofZmanShmaMGA96Minutes', null],
			'tromsoPolarDay getSofZmanShmaMGA96MinutesZmanis' => ['tromsoPolarDay', 'getSofZmanShmaMGA96MinutesZmanis', null],
			'tromsoPolarDay getSofZmanTfilaAteretTorah' => ['tromsoPolarDay', 'getSofZmanTfilaAteretTorah', null],
			'tromsoPolarDay getSofZmanTfilaMGA120Minutes' => ['tromsoPolarDay', 'getSofZmanTfilaMGA120Minutes', null],
			'tromsoPolarDay getSofZmanTfilaMGA72Minutes' => ['tromsoPolarDay', 'getSofZmanTfilaMGA72Minutes', null],
			'tromsoPolarDay getSofZmanTfilaMGA90Minutes' => ['tromsoPolarDay', 'getSofZmanTfilaMGA90Minutes', null],
			'tromsoPolarDay getSofZmanTfilaMGA96Minutes' => ['tromsoPolarDay', 'getSofZmanTfilaMGA96Minutes', null],
			'tromsoPolarDay getTzais120Minutes' => ['tromsoPolarDay', 'getTzais120Minutes', null],
			'tromsoPolarDay getTzais50Minutes' => ['tromsoPolarDay', 'getTzais50Minutes', null],
			'tromsoPolarDay getTzais60Minutes' => ['tromsoPolarDay', 'getTzais60Minutes', null],
			'tromsoPolarDay getTzais72Minutes' => ['tromsoPolarDay', 'getTzais72Minutes', null],
			'tromsoPolarDay getTzais90Minutes' => ['tromsoPolarDay', 'getTzais90Minutes', null],
			'tromsoPolarDay getTzais96Minutes' => ['tromsoPolarDay', 'getTzais96Minutes', null],
			'tromsoPolarDay getTzaisAteretTorah' => ['tromsoPolarDay', 'getTzaisAteretTorah', null],
			// tromsoPolarNight
			'tromsoPolarNight getAlos120Minutes' => ['tromsoPolarNight', 'getAlos120Minutes', '2026-01-15T08:28:45.058Z'],
			'tromsoPolarNight getAlos60Minutes' => ['tromsoPolarNight', 'getAlos60Minutes', '2026-01-15T09:28:45.058Z'],
			'tromsoPolarNight getAlos72Minutes' => ['tromsoPolarNight', 'getAlos72Minutes', '2026-01-15T09:16:45.058Z'],
			'tromsoPolarNight getAlos90Minutes' => ['tromsoPolarNight', 'getAlos90Minutes', '2026-01-15T08:58:45.058Z'],
			'tromsoPolarNight getAlos96Minutes' => ['tromsoPolarNight', 'getAlos96Minutes', '2026-01-15T08:52:45.058Z'],
			'tromsoPolarNight getBainHashmashosRT13Point5MinutesBefore7Point083Degrees' => ['tromsoPolarNight', 'getBainHashmashosRT13Point5MinutesBefore7Point083Degrees', '2026-01-15T13:55:36.723Z'],
			'tromsoPolarNight getBainHashmashosRT58Point5Minutes' => ['tromsoPolarNight', 'getBainHashmashosRT58Point5Minutes', '2026-01-15T12:17:36.977Z'],
			'tromsoPolarNight getBainHashmashosYereim13Point5Minutes' => ['tromsoPolarNight', 'getBainHashmashosYereim13Point5Minutes', '2026-01-15T11:05:36.977Z'],
			'tromsoPolarNight getBainHashmashosYereim16Point875Minutes' => ['tromsoPolarNight', 'getBainHashmashosYereim16Point875Minutes', '2026-01-15T11:02:14.477Z'],
			'tromsoPolarNight getBainHashmashosYereim18Minutes' => ['tromsoPolarNight', 'getBainHashmashosYereim18Minutes', '2026-01-15T11:01:06.977Z'],
			'tromsoPolarNight getCandleLighting' => ['tromsoPolarNight', 'getCandleLighting', '2026-01-15T11:01:06.977Z'],
			'tromsoPolarNight getChatzosHayomAsHalfDay' => ['tromsoPolarNight', 'getChatzosHayomAsHalfDay', '2026-01-15T10:53:56.017Z'],
			'tromsoPolarNight getMinchaGedola16Point1Degrees' => ['tromsoPolarNight', 'getMinchaGedola16Point1Degrees', '2026-01-15T11:20:29.603Z'],
			'tromsoPolarNight getMinchaGedola72Minutes' => ['tromsoPolarNight', 'getMinchaGedola72Minutes', '2026-01-15T11:02:01.930Z'],
			'tromsoPolarNight getMinchaGedolaAteretTorah' => ['tromsoPolarNight', 'getMinchaGedolaAteretTorah', '2026-01-15T11:15:23.426Z'],
			'tromsoPolarNight getMinchaGedolaBaalHatanya' => ['tromsoPolarNight', 'getMinchaGedolaBaalHatanya', '2026-01-15T10:59:45.615Z'],
			'tromsoPolarNight getMinchaGedolaGRA' => ['tromsoPolarNight', 'getMinchaGedolaGRA', '2026-01-15T10:56:01.930Z'],
			'tromsoPolarNight getMinchaGedolaGRAGreaterThan30' => ['tromsoPolarNight', 'getMinchaGedolaGRAGreaterThan30', '2026-01-15T11:23:34.146Z'],
			'tromsoPolarNight getMinchaKetana16Point1Degrees' => ['tromsoPolarNight', 'getMinchaKetana16Point1Degrees', '2026-01-15T13:59:16.577Z'],
			'tromsoPolarNight getMinchaKetana72Minutes' => ['tromsoPolarNight', 'getMinchaKetana72Minutes', '2026-01-15T11:50:37.410Z'],
			'tromsoPolarNight getMinchaKetanaAteretTorah' => ['tromsoPolarNight', 'getMinchaKetanaAteretTorah', '2026-01-15T11:39:14.454Z'],
			'tromsoPolarNight getMinchaKetanaBaalHatanya' => ['tromsoPolarNight', 'getMinchaKetanaBaalHatanya', '2026-01-15T11:34:42.098Z'],
			'tromsoPolarNight getMinchaKetanaGRA' => ['tromsoPolarNight', 'getMinchaKetanaGRA', '2026-01-15T11:08:37.410Z'],
			'tromsoPolarNight getPlagHamincha120Minutes' => ['tromsoPolarNight', 'getPlagHamincha120Minutes', '2026-01-15T12:48:52.193Z'],
			'tromsoPolarNight getPlagHamincha60Minutes' => ['tromsoPolarNight', 'getPlagHamincha60Minutes', '2026-01-15T12:01:22.193Z'],
			'tromsoPolarNight getPlagHamincha72Minutes' => ['tromsoPolarNight', 'getPlagHamincha72Minutes', '2026-01-15T12:10:52.193Z'],
			'tromsoPolarNight getPlagHamincha90Minutes' => ['tromsoPolarNight', 'getPlagHamincha90Minutes', '2026-01-15T12:25:07.193Z'],
			'tromsoPolarNight getPlagHamincha96Minutes' => ['tromsoPolarNight', 'getPlagHamincha96Minutes', '2026-01-15T12:29:52.193Z'],
			'tromsoPolarNight getPlagHaminchaAteretTorah' => ['tromsoPolarNight', 'getPlagHaminchaAteretTorah', '2026-01-15T11:49:10.715Z'],
			'tromsoPolarNight getSamuchLeMinchaKetana16Point1Degrees' => ['tromsoPolarNight', 'getSamuchLeMinchaKetana16Point1Degrees', '2026-01-15T13:32:48.748Z'],
			'tromsoPolarNight getSamuchLeMinchaKetana72Minutes' => ['tromsoPolarNight', 'getSamuchLeMinchaKetana72Minutes', '2026-01-15T11:42:31.497Z'],
			'tromsoPolarNight getSamuchLeMinchaKetanaGRA' => ['tromsoPolarNight', 'getSamuchLeMinchaKetanaGRA', '2026-01-15T11:06:31.497Z'],
			'tromsoPolarNight getShaahZmanis120Minutes' => ['tromsoPolarNight', 'getShaahZmanis120Minutes', 1451826],
			'tromsoPolarNight getShaahZmanis60Minutes' => ['tromsoPolarNight', 'getShaahZmanis60Minutes', 851826],
			'tromsoPolarNight getShaahZmanis72Minutes' => ['tromsoPolarNight', 'getShaahZmanis72Minutes', 971826],
			'tromsoPolarNight getShaahZmanis90Minutes' => ['tromsoPolarNight', 'getShaahZmanis90Minutes', 1151826],
			'tromsoPolarNight getShaahZmanis96Minutes' => ['tromsoPolarNight', 'getShaahZmanis96Minutes', 1211826],
			'tromsoPolarNight getShaahZmanisAteretTorah' => ['tromsoPolarNight', 'getShaahZmanisAteretTorah', 477009],
			'tromsoPolarNight getSofZmanAchilasChametzMGA72Minutes' => ['tromsoPolarNight', 'getSofZmanAchilasChametzMGA72Minutes', null],
			'tromsoPolarNight getSofZmanBiurChametzMGA72Minutes' => ['tromsoPolarNight', 'getSofZmanBiurChametzMGA72Minutes', null],
			'tromsoPolarNight getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees' => ['tromsoPolarNight', 'getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees', '2026-01-15T07:44:37.551Z'],
			'tromsoPolarNight getSofZmanShmaAlos16Point1ToSunset' => ['tromsoPolarNight', 'getSofZmanShmaAlos16Point1ToSunset', '2026-01-15T07:02:07.614Z'],
			'tromsoPolarNight getSofZmanShmaAteretTorah' => ['tromsoPolarNight', 'getSofZmanShmaAteretTorah', '2026-01-15T10:47:33.894Z'],
			'tromsoPolarNight getSofZmanShmaBaalHatanya' => ['tromsoPolarNight', 'getSofZmanShmaBaalHatanya', '2026-01-15T10:18:59.718Z'],
			'tromsoPolarNight getSofZmanShmaGRA' => ['tromsoPolarNight', 'getSofZmanShmaGRA', '2026-01-15T10:41:20.538Z'],
			'tromsoPolarNight getSofZmanShmaMGA120Minutes' => ['tromsoPolarNight', 'getSofZmanShmaMGA120Minutes', '2026-01-15T09:41:20.538Z'],
			'tromsoPolarNight getSofZmanShmaMGA16Point1Degrees' => ['tromsoPolarNight', 'getSofZmanShmaMGA16Point1Degrees', '2026-01-15T08:15:14.800Z'],
			'tromsoPolarNight getSofZmanShmaMGA18Degrees' => ['tromsoPolarNight', 'getSofZmanShmaMGA18Degrees', '2026-01-15T08:03:57.543Z'],
			'tromsoPolarNight getSofZmanShmaMGA19Point8Degrees' => ['tromsoPolarNight', 'getSofZmanShmaMGA19Point8Degrees', '2026-01-15T07:53:28.177Z'],
			'tromsoPolarNight getSofZmanShmaMGA72Minutes' => ['tromsoPolarNight', 'getSofZmanShmaMGA72Minutes', '2026-01-15T10:05:20.538Z'],
			'tromsoPolarNight getSofZmanShmaMGA72MinutesToFixedLocalChatzos' => ['tromsoPolarNight', 'getSofZmanShmaMGA72MinutesToFixedLocalChatzos', '2026-01-15T10:00:27.893Z'],
			'tromsoPolarNight getSofZmanShmaMGA72MinutesZmanis' => ['tromsoPolarNight', 'getSofZmanShmaMGA72MinutesZmanis', '2026-01-15T10:38:49.442Z'],
			'tromsoPolarNight getSofZmanShmaMGA90Minutes' => ['tromsoPolarNight', 'getSofZmanShmaMGA90Minutes', '2026-01-15T09:56:20.538Z'],
			'tromsoPolarNight getSofZmanShmaMGA90MinutesToFixedLocalChatzos' => ['tromsoPolarNight', 'getSofZmanShmaMGA90MinutesToFixedLocalChatzos', '2026-01-15T09:51:27.893Z'],
			'tromsoPolarNight getSofZmanShmaMGA90MinutesZmanis' => ['tromsoPolarNight', 'getSofZmanShmaMGA90MinutesZmanis', '2026-01-15T10:38:11.668Z'],
			'tromsoPolarNight getSofZmanShmaMGA96Minutes' => ['tromsoPolarNight', 'getSofZmanShmaMGA96Minutes', '2026-01-15T09:53:20.538Z'],
			'tromsoPolarNight getSofZmanShmaMGA96MinutesZmanis' => ['tromsoPolarNight', 'getSofZmanShmaMGA96MinutesZmanis', '2026-01-15T10:37:59.076Z'],
			'tromsoPolarNight getSofZmanTfilaAteretTorah' => ['tromsoPolarNight', 'getSofZmanTfilaAteretTorah', '2026-01-15T10:55:30.903Z'],
			'tromsoPolarNight getSofZmanTfilaMGA120Minutes' => ['tromsoPolarNight', 'getSofZmanTfilaMGA120Minutes', '2026-01-15T10:05:32.364Z'],
			'tromsoPolarNight getSofZmanTfilaMGA72Minutes' => ['tromsoPolarNight', 'getSofZmanTfilaMGA72Minutes', '2026-01-15T10:21:32.364Z'],
			'tromsoPolarNight getSofZmanTfilaMGA90Minutes' => ['tromsoPolarNight', 'getSofZmanTfilaMGA90Minutes', '2026-01-15T10:15:32.364Z'],
			'tromsoPolarNight getSofZmanTfilaMGA96Minutes' => ['tromsoPolarNight', 'getSofZmanTfilaMGA96Minutes', '2026-01-15T10:13:32.364Z'],
			'tromsoPolarNight getTzais120Minutes' => ['tromsoPolarNight', 'getTzais120Minutes', '2026-01-15T13:19:06.977Z'],
			'tromsoPolarNight getTzais50Minutes' => ['tromsoPolarNight', 'getTzais50Minutes', '2026-01-15T12:09:06.977Z'],
			'tromsoPolarNight getTzais60Minutes' => ['tromsoPolarNight', 'getTzais60Minutes', '2026-01-15T12:19:06.977Z'],
			'tromsoPolarNight getTzais72Minutes' => ['tromsoPolarNight', 'getTzais72Minutes', '2026-01-15T12:31:06.977Z'],
			'tromsoPolarNight getTzais90Minutes' => ['tromsoPolarNight', 'getTzais90Minutes', '2026-01-15T12:49:06.977Z'],
			'tromsoPolarNight getTzais96Minutes' => ['tromsoPolarNight', 'getTzais96Minutes', '2026-01-15T12:55:06.977Z'],
			'tromsoPolarNight getTzaisAteretTorah' => ['tromsoPolarNight', 'getTzaisAteretTorah', '2026-01-15T11:59:06.977Z'],
			// reykjavikSummer
			'reykjavikSummer getAlos120Minutes' => ['reykjavikSummer', 'getAlos120Minutes', '2026-06-21T00:55:16.431Z'],
			'reykjavikSummer getAlos60Minutes' => ['reykjavikSummer', 'getAlos60Minutes', '2026-06-21T01:55:16.431Z'],
			'reykjavikSummer getAlos72Minutes' => ['reykjavikSummer', 'getAlos72Minutes', '2026-06-21T01:43:16.431Z'],
			'reykjavikSummer getAlos90Minutes' => ['reykjavikSummer', 'getAlos90Minutes', '2026-06-21T01:25:16.431Z'],
			'reykjavikSummer getAlos96Minutes' => ['reykjavikSummer', 'getAlos96Minutes', '2026-06-21T01:19:16.431Z'],
			'reykjavikSummer getBainHashmashosRT13Point5MinutesBefore7Point083Degrees' => ['reykjavikSummer', 'getBainHashmashosRT13Point5MinutesBefore7Point083Degrees', null],
			'reykjavikSummer getBainHashmashosRT58Point5Minutes' => ['reykjavikSummer', 'getBainHashmashosRT58Point5Minutes', '2026-06-22T01:01:24.349Z'],
			'reykjavikSummer getBainHashmashosYereim13Point5Minutes' => ['reykjavikSummer', 'getBainHashmashosYereim13Point5Minutes', '2026-06-21T23:49:24.349Z'],
			'reykjavikSummer getBainHashmashosYereim16Point875Minutes' => ['reykjavikSummer', 'getBainHashmashosYereim16Point875Minutes', '2026-06-21T23:46:01.849Z'],
			'reykjavikSummer getBainHashmashosYereim18Minutes' => ['reykjavikSummer', 'getBainHashmashosYereim18Minutes', '2026-06-21T23:44:54.349Z'],
			'reykjavikSummer getCandleLighting' => ['reykjavikSummer', 'getCandleLighting', '2026-06-21T23:44:54.349Z'],
			'reykjavikSummer getChatzosHayomAsHalfDay' => ['reykjavikSummer', 'getChatzosHayomAsHalfDay', '2026-06-21T13:29:05.390Z'],
			'reykjavikSummer getMinchaGedola16Point1Degrees' => ['reykjavikSummer', 'getMinchaGedola16Point1Degrees', null],
			'reykjavikSummer getMinchaGedola72Minutes' => ['reykjavikSummer', 'getMinchaGedola72Minutes', '2026-06-21T14:27:54.470Z'],
			'reykjavikSummer getMinchaGedolaAteretTorah' => ['reykjavikSummer', 'getMinchaGedolaAteretTorah', '2026-06-21T13:45:28.482Z'],
			'reykjavikSummer getMinchaGedolaBaalHatanya' => ['reykjavikSummer', 'getMinchaGedolaBaalHatanya', '2026-06-21T14:23:52.028Z'],
			'reykjavikSummer getMinchaGedolaGRA' => ['reykjavikSummer', 'getMinchaGedolaGRA', '2026-06-21T14:21:54.470Z'],
			'reykjavikSummer getMinchaGedolaGRAGreaterThan30' => ['reykjavikSummer', 'getMinchaGedolaGRAGreaterThan30', '2026-06-21T14:21:54.470Z'],
			'reykjavikSummer getMinchaKetana16Point1Degrees' => ['reykjavikSummer', 'getMinchaKetana16Point1Degrees', null],
			'reykjavikSummer getMinchaKetana72Minutes' => ['reykjavikSummer', 'getMinchaKetana72Minutes', '2026-06-21T20:20:48.949Z'],
			'reykjavikSummer getMinchaKetanaAteretTorah' => ['reykjavikSummer', 'getMinchaKetanaAteretTorah', '2026-06-21T19:44:04.409Z'],
			'reykjavikSummer getMinchaKetanaBaalHatanya' => ['reykjavikSummer', 'getMinchaKetanaBaalHatanya', '2026-06-21T19:52:34.494Z'],
			'reykjavikSummer getMinchaKetanaGRA' => ['reykjavikSummer', 'getMinchaKetanaGRA', '2026-06-21T19:38:48.949Z'],
			'reykjavikSummer getPlagHamincha120Minutes' => ['reykjavikSummer', 'getPlagHamincha120Minutes', '2026-06-21T23:25:51.649Z'],
			'reykjavikSummer getPlagHamincha60Minutes' => ['reykjavikSummer', 'getPlagHamincha60Minutes', '2026-06-21T22:38:21.649Z'],
			'reykjavikSummer getPlagHamincha72Minutes' => ['reykjavikSummer', 'getPlagHamincha72Minutes', '2026-06-21T22:47:51.649Z'],
			'reykjavikSummer getPlagHamincha90Minutes' => ['reykjavikSummer', 'getPlagHamincha90Minutes', '2026-06-21T23:02:06.649Z'],
			'reykjavikSummer getPlagHamincha96Minutes' => ['reykjavikSummer', 'getPlagHamincha96Minutes', '2026-06-21T23:06:51.649Z'],
			'reykjavikSummer getPlagHaminchaAteretTorah' => ['reykjavikSummer', 'getPlagHaminchaAteretTorah', '2026-06-21T22:13:29.379Z'],
			'reykjavikSummer getSamuchLeMinchaKetana16Point1Degrees' => ['reykjavikSummer', 'getSamuchLeMinchaKetana16Point1Degrees', null],
			'reykjavikSummer getSamuchLeMinchaKetana72Minutes' => ['reykjavikSummer', 'getSamuchLeMinchaKetana72Minutes', '2026-06-21T19:21:59.869Z'],
			'reykjavikSummer getSamuchLeMinchaKetanaGRA' => ['reykjavikSummer', 'getSamuchLeMinchaKetanaGRA', '2026-06-21T18:45:59.869Z'],
			'reykjavikSummer getShaahZmanis120Minutes' => ['reykjavikSummer', 'getShaahZmanis120Minutes', 7538159],
			'reykjavikSummer getShaahZmanis60Minutes' => ['reykjavikSummer', 'getShaahZmanis60Minutes', 6938159],
			'reykjavikSummer getShaahZmanis72Minutes' => ['reykjavikSummer', 'getShaahZmanis72Minutes', 7058159],
			'reykjavikSummer getShaahZmanis90Minutes' => ['reykjavikSummer', 'getShaahZmanis90Minutes', 7238159],
			'reykjavikSummer getShaahZmanis96Minutes' => ['reykjavikSummer', 'getShaahZmanis96Minutes', 7298159],
			'reykjavikSummer getShaahZmanisAteretTorah' => ['reykjavikSummer', 'getShaahZmanisAteretTorah', 7171975],
			'reykjavikSummer getSofZmanAchilasChametzMGA72Minutes' => ['reykjavikSummer', 'getSofZmanAchilasChametzMGA72Minutes', null],
			'reykjavikSummer getSofZmanBiurChametzMGA72Minutes' => ['reykjavikSummer', 'getSofZmanBiurChametzMGA72Minutes', null],
			'reykjavikSummer getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees' => ['reykjavikSummer', 'getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees', null],
			'reykjavikSummer getSofZmanShmaAlos16Point1ToSunset' => ['reykjavikSummer', 'getSofZmanShmaAlos16Point1ToSunset', null],
			'reykjavikSummer getSofZmanShmaAteretTorah' => ['reykjavikSummer', 'getSofZmanShmaAteretTorah', '2026-06-21T06:47:06.566Z'],
			'reykjavikSummer getSofZmanShmaBaalHatanya' => ['reykjavikSummer', 'getSofZmanShmaBaalHatanya', '2026-06-21T08:00:22.484Z'],
			'reykjavikSummer getSofZmanShmaGRA' => ['reykjavikSummer', 'getSofZmanShmaGRA', '2026-06-21T08:12:10.910Z'],
			'reykjavikSummer getSofZmanShmaMGA120Minutes' => ['reykjavikSummer', 'getSofZmanShmaMGA120Minutes', '2026-06-21T07:12:10.910Z'],
			'reykjavikSummer getSofZmanShmaMGA16Point1Degrees' => ['reykjavikSummer', 'getSofZmanShmaMGA16Point1Degrees', null],
			'reykjavikSummer getSofZmanShmaMGA18Degrees' => ['reykjavikSummer', 'getSofZmanShmaMGA18Degrees', null],
			'reykjavikSummer getSofZmanShmaMGA19Point8Degrees' => ['reykjavikSummer', 'getSofZmanShmaMGA19Point8Degrees', null],
			'reykjavikSummer getSofZmanShmaMGA72Minutes' => ['reykjavikSummer', 'getSofZmanShmaMGA72Minutes', '2026-06-21T07:36:10.910Z'],
			'reykjavikSummer getSofZmanShmaMGA72MinutesToFixedLocalChatzos' => ['reykjavikSummer', 'getSofZmanShmaMGA72MinutesToFixedLocalChatzos', '2026-06-21T07:35:16.303Z'],
			'reykjavikSummer getSofZmanShmaMGA72MinutesZmanis' => ['reykjavikSummer', 'getSofZmanShmaMGA72MinutesZmanis', '2026-06-21T07:08:48.014Z'],
			'reykjavikSummer getSofZmanShmaMGA90Minutes' => ['reykjavikSummer', 'getSofZmanShmaMGA90Minutes', '2026-06-21T07:27:10.910Z'],
			'reykjavikSummer getSofZmanShmaMGA90MinutesToFixedLocalChatzos' => ['reykjavikSummer', 'getSofZmanShmaMGA90MinutesToFixedLocalChatzos', '2026-06-21T07:26:16.303Z'],
			'reykjavikSummer getSofZmanShmaMGA90MinutesZmanis' => ['reykjavikSummer', 'getSofZmanShmaMGA90MinutesZmanis', '2026-06-21T06:52:57.290Z'],
			'reykjavikSummer getSofZmanShmaMGA96Minutes' => ['reykjavikSummer', 'getSofZmanShmaMGA96Minutes', '2026-06-21T07:24:10.910Z'],
			'reykjavikSummer getSofZmanShmaMGA96MinutesZmanis' => ['reykjavikSummer', 'getSofZmanShmaMGA96MinutesZmanis', '2026-06-21T06:47:40.382Z'],
			'reykjavikSummer getSofZmanTfilaAteretTorah' => ['reykjavikSummer', 'getSofZmanTfilaAteretTorah', '2026-06-21T08:46:38.542Z'],
			'reykjavikSummer getSofZmanTfilaMGA120Minutes' => ['reykjavikSummer', 'getSofZmanTfilaMGA120Minutes', '2026-06-21T09:17:49.070Z'],
			'reykjavikSummer getSofZmanTfilaMGA72Minutes' => ['reykjavikSummer', 'getSofZmanTfilaMGA72Minutes', '2026-06-21T09:33:49.070Z'],
			'reykjavikSummer getSofZmanTfilaMGA90Minutes' => ['reykjavikSummer', 'getSofZmanTfilaMGA90Minutes', '2026-06-21T09:27:49.070Z'],
			'reykjavikSummer getSofZmanTfilaMGA96Minutes' => ['reykjavikSummer', 'getSofZmanTfilaMGA96Minutes', '2026-06-21T09:25:49.070Z'],
			'reykjavikSummer getTzais120Minutes' => ['reykjavikSummer', 'getTzais120Minutes', '2026-06-22T02:02:54.349Z'],
			'reykjavikSummer getTzais50Minutes' => ['reykjavikSummer', 'getTzais50Minutes', '2026-06-22T00:52:54.349Z'],
			'reykjavikSummer getTzais60Minutes' => ['reykjavikSummer', 'getTzais60Minutes', '2026-06-22T01:02:54.349Z'],
			'reykjavikSummer getTzais72Minutes' => ['reykjavikSummer', 'getTzais72Minutes', '2026-06-22T01:14:54.349Z'],
			'reykjavikSummer getTzais90Minutes' => ['reykjavikSummer', 'getTzais90Minutes', '2026-06-22T01:32:54.349Z'],
			'reykjavikSummer getTzais96Minutes' => ['reykjavikSummer', 'getTzais96Minutes', '2026-06-22T01:38:54.349Z'],
			'reykjavikSummer getTzaisAteretTorah' => ['reykjavikSummer', 'getTzaisAteretTorah', '2026-06-22T00:42:54.349Z'],
			// anchorageSummer
			'anchorageSummer getAlos120Minutes' => ['anchorageSummer', 'getAlos120Minutes', '2026-06-21T10:20:19.017Z'],
			'anchorageSummer getAlos60Minutes' => ['anchorageSummer', 'getAlos60Minutes', '2026-06-21T11:20:19.017Z'],
			'anchorageSummer getAlos72Minutes' => ['anchorageSummer', 'getAlos72Minutes', '2026-06-21T11:08:19.017Z'],
			'anchorageSummer getAlos90Minutes' => ['anchorageSummer', 'getAlos90Minutes', '2026-06-21T10:50:19.017Z'],
			'anchorageSummer getAlos96Minutes' => ['anchorageSummer', 'getAlos96Minutes', '2026-06-21T10:44:19.017Z'],
			'anchorageSummer getBainHashmashosRT13Point5MinutesBefore7Point083Degrees' => ['anchorageSummer', 'getBainHashmashosRT13Point5MinutesBefore7Point083Degrees', null],
			'anchorageSummer getBainHashmashosRT58Point5Minutes' => ['anchorageSummer', 'getBainHashmashosRT58Point5Minutes', '2026-06-22T08:41:10.018Z'],
			'anchorageSummer getBainHashmashosYereim13Point5Minutes' => ['anchorageSummer', 'getBainHashmashosYereim13Point5Minutes', '2026-06-22T07:29:10.018Z'],
			'anchorageSummer getBainHashmashosYereim16Point875Minutes' => ['anchorageSummer', 'getBainHashmashosYereim16Point875Minutes', '2026-06-22T07:25:47.518Z'],
			'anchorageSummer getBainHashmashosYereim18Minutes' => ['anchorageSummer', 'getBainHashmashosYereim18Minutes', '2026-06-22T07:24:40.018Z'],
			'anchorageSummer getCandleLighting' => ['anchorageSummer', 'getCandleLighting', '2026-06-22T07:24:40.018Z'],
			'anchorageSummer getChatzosHayomAsHalfDay' => ['anchorageSummer', 'getChatzosHayomAsHalfDay', '2026-06-21T22:01:29.517Z'],
			'anchorageSummer getMinchaGedola16Point1Degrees' => ['anchorageSummer', 'getMinchaGedola16Point1Degrees', null],
			'anchorageSummer getMinchaGedola72Minutes' => ['anchorageSummer', 'getMinchaGedola72Minutes', '2026-06-21T22:55:55.392Z'],
			'anchorageSummer getMinchaGedolaAteretTorah' => ['anchorageSummer', 'getMinchaGedolaAteretTorah', '2026-06-21T22:18:18.930Z'],
			'anchorageSummer getMinchaGedolaBaalHatanya' => ['anchorageSummer', 'getMinchaGedolaBaalHatanya', '2026-06-21T22:50:57.620Z'],
			'anchorageSummer getMinchaGedolaGRA' => ['anchorageSummer', 'getMinchaGedolaGRA', '2026-06-21T22:49:55.392Z'],
			'anchorageSummer getMinchaGedolaGRAGreaterThan30' => ['anchorageSummer', 'getMinchaGedolaGRAGreaterThan30', '2026-06-21T22:49:55.392Z'],
			'anchorageSummer getMinchaKetana16Point1Degrees' => ['anchorageSummer', 'getMinchaKetana16Point1Degrees', null],
			'anchorageSummer getMinchaKetana72Minutes' => ['anchorageSummer', 'getMinchaKetana72Minutes', '2026-06-22T04:22:30.643Z'],
			'anchorageSummer getMinchaKetanaAteretTorah' => ['anchorageSummer', 'getMinchaKetanaAteretTorah', '2026-06-22T03:47:57.705Z'],
			'anchorageSummer getMinchaKetanaBaalHatanya' => ['anchorageSummer', 'getMinchaKetanaBaalHatanya', '2026-06-22T03:47:47.258Z'],
			'anchorageSummer getMinchaKetanaGRA' => ['anchorageSummer', 'getMinchaKetanaGRA', '2026-06-22T03:40:30.643Z'],
			'anchorageSummer getPlagHamincha120Minutes' => ['anchorageSummer', 'getPlagHamincha120Minutes', '2026-06-22T07:16:35.331Z'],
			'anchorageSummer getPlagHamincha60Minutes' => ['anchorageSummer', 'getPlagHamincha60Minutes', '2026-06-22T06:29:05.331Z'],
			'anchorageSummer getPlagHamincha72Minutes' => ['anchorageSummer', 'getPlagHamincha72Minutes', '2026-06-22T06:38:35.331Z'],
			'anchorageSummer getPlagHamincha90Minutes' => ['anchorageSummer', 'getPlagHamincha90Minutes', '2026-06-22T06:52:50.331Z'],
			'anchorageSummer getPlagHamincha96Minutes' => ['anchorageSummer', 'getPlagHamincha96Minutes', '2026-06-22T06:57:35.331Z'],
			'anchorageSummer getPlagHaminchaAteretTorah' => ['anchorageSummer', 'getPlagHaminchaAteretTorah', '2026-06-22T06:05:18.862Z'],
			'anchorageSummer getSamuchLeMinchaKetana16Point1Degrees' => ['anchorageSummer', 'getSamuchLeMinchaKetana16Point1Degrees', null],
			'anchorageSummer getSamuchLeMinchaKetana72Minutes' => ['anchorageSummer', 'getSamuchLeMinchaKetana72Minutes', '2026-06-22T03:28:04.768Z'],
			'anchorageSummer getSamuchLeMinchaKetanaGRA' => ['anchorageSummer', 'getSamuchLeMinchaKetanaGRA', '2026-06-22T02:52:04.768Z'],
			'anchorageSummer getShaahZmanis120Minutes' => ['anchorageSummer', 'getShaahZmanis120Minutes', 7011750],
			'anchorageSummer getShaahZmanis60Minutes' => ['anchorageSummer', 'getShaahZmanis60Minutes', 6411750],
			'anchorageSummer getShaahZmanis72Minutes' => ['anchorageSummer', 'getShaahZmanis72Minutes', 6531750],
			'anchorageSummer getShaahZmanis90Minutes' => ['anchorageSummer', 'getShaahZmanis90Minutes', 6711750],
			'anchorageSummer getShaahZmanis96Minutes' => ['anchorageSummer', 'getShaahZmanis96Minutes', 6771750],
			'anchorageSummer getShaahZmanisAteretTorah' => ['anchorageSummer', 'getShaahZmanisAteretTorah', 6592925],
			'anchorageSummer getSofZmanAchilasChametzMGA72Minutes' => ['anchorageSummer', 'getSofZmanAchilasChametzMGA72Minutes', null],
			'anchorageSummer getSofZmanBiurChametzMGA72Minutes' => ['anchorageSummer', 'getSofZmanBiurChametzMGA72Minutes', null],
			'anchorageSummer getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees' => ['anchorageSummer', 'getSofZmanShmaAlos16Point1DegreesToTzaisGeonim7Point083Degrees', null],
			'anchorageSummer getSofZmanShmaAlos16Point1ToSunset' => ['anchorageSummer', 'getSofZmanShmaAlos16Point1ToSunset', null],
			'anchorageSummer getSofZmanShmaAteretTorah' => ['anchorageSummer', 'getSofZmanShmaAteretTorah', '2026-06-21T15:53:43.692Z'],
			'anchorageSummer getSofZmanShmaBaalHatanya' => ['anchorageSummer', 'getSofZmanShmaBaalHatanya', '2026-06-21T17:04:39.710Z'],
			'anchorageSummer getSofZmanShmaGRA' => ['anchorageSummer', 'getSofZmanShmaGRA', '2026-06-21T17:10:54.267Z'],
			'anchorageSummer getSofZmanShmaMGA120Minutes' => ['anchorageSummer', 'getSofZmanShmaMGA120Minutes', '2026-06-21T16:10:54.267Z'],
			'anchorageSummer getSofZmanShmaMGA16Point1Degrees' => ['anchorageSummer', 'getSofZmanShmaMGA16Point1Degrees', null],
			'anchorageSummer getSofZmanShmaMGA18Degrees' => ['anchorageSummer', 'getSofZmanShmaMGA18Degrees', null],
			'anchorageSummer getSofZmanShmaMGA19Point8Degrees' => ['anchorageSummer', 'getSofZmanShmaMGA19Point8Degrees', null],
			'anchorageSummer getSofZmanShmaMGA72Minutes' => ['anchorageSummer', 'getSofZmanShmaMGA72Minutes', '2026-06-21T16:34:54.267Z'],
			'anchorageSummer getSofZmanShmaMGA72MinutesToFixedLocalChatzos' => ['anchorageSummer', 'getSofZmanShmaMGA72MinutesToFixedLocalChatzos', '2026-06-21T16:33:57.544Z'],
			'anchorageSummer getSofZmanShmaMGA72MinutesZmanis' => ['anchorageSummer', 'getSofZmanShmaMGA72MinutesZmanis', '2026-06-21T16:12:47.217Z'],
			'anchorageSummer getSofZmanShmaMGA90Minutes' => ['anchorageSummer', 'getSofZmanShmaMGA90Minutes', '2026-06-21T16:25:54.267Z'],
			'anchorageSummer getSofZmanShmaMGA90MinutesToFixedLocalChatzos' => ['anchorageSummer', 'getSofZmanShmaMGA90MinutesToFixedLocalChatzos', '2026-06-21T16:24:57.544Z'],
			'anchorageSummer getSofZmanShmaMGA90MinutesZmanis' => ['anchorageSummer', 'getSofZmanShmaMGA90MinutesZmanis', '2026-06-21T15:58:15.454Z'],
			'anchorageSummer getSofZmanShmaMGA96Minutes' => ['anchorageSummer', 'getSofZmanShmaMGA96Minutes', '2026-06-21T16:22:54.267Z'],
			'anchorageSummer getSofZmanShmaMGA96MinutesZmanis' => ['anchorageSummer', 'getSofZmanShmaMGA96MinutesZmanis', '2026-06-21T15:53:24.867Z'],
			'anchorageSummer getSofZmanTfilaAteretTorah' => ['anchorageSummer', 'getSofZmanTfilaAteretTorah', '2026-06-21T17:43:36.617Z'],
			'anchorageSummer getSofZmanTfilaMGA120Minutes' => ['anchorageSummer', 'getSofZmanTfilaMGA120Minutes', '2026-06-21T18:07:46.017Z'],
			'anchorageSummer getSofZmanTfilaMGA72Minutes' => ['anchorageSummer', 'getSofZmanTfilaMGA72Minutes', '2026-06-21T18:23:46.017Z'],
			'anchorageSummer getSofZmanTfilaMGA90Minutes' => ['anchorageSummer', 'getSofZmanTfilaMGA90Minutes', '2026-06-21T18:17:46.017Z'],
			'anchorageSummer getSofZmanTfilaMGA96Minutes' => ['anchorageSummer', 'getSofZmanTfilaMGA96Minutes', '2026-06-21T18:15:46.017Z'],
			'anchorageSummer getTzais120Minutes' => ['anchorageSummer', 'getTzais120Minutes', '2026-06-22T09:42:40.018Z'],
			'anchorageSummer getTzais50Minutes' => ['anchorageSummer', 'getTzais50Minutes', '2026-06-22T08:32:40.018Z'],
			'anchorageSummer getTzais60Minutes' => ['anchorageSummer', 'getTzais60Minutes', '2026-06-22T08:42:40.018Z'],
			'anchorageSummer getTzais72Minutes' => ['anchorageSummer', 'getTzais72Minutes', '2026-06-22T08:54:40.018Z'],
			'anchorageSummer getTzais90Minutes' => ['anchorageSummer', 'getTzais90Minutes', '2026-06-22T09:12:40.018Z'],
			'anchorageSummer getTzais96Minutes' => ['anchorageSummer', 'getTzais96Minutes', '2026-06-22T09:18:40.018Z'],
			'anchorageSummer getTzaisAteretTorah' => ['anchorageSummer', 'getTzaisAteretTorah', '2026-06-22T08:22:40.018Z'],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| THE GENERIC ENTRY POINTS ACCEPT A NULL START OR END OF DAY
	|--------------------------------------------------------------------------
	| Java's equivalents take an Instant and null-check inside, so a caller that passes an
	| uncomputable alos or tzais straight in gets a null back rather than an error.
	*/

	#[Test]
	#[DataProvider('genericEntryPointProvider')]
	public function genericEntryPointsReturnNullForANullBoundary(string $method): void
	{
		$calendar = $this->calendarFor('tromsoPolarNight');
		$sunrise = $calendar->getSeaLevelSunrise();
		$sunset = $calendar->getSeaLevelSunset();

		$this->assertNotNull($sunrise);
		$this->assertNotNull($sunset);

		$this->assertNull($calendar->$method(null, null));
		$this->assertNull($calendar->$method(null, $sunset));
		$this->assertNull($calendar->$method($sunrise, null));
		$this->assertNotNull($calendar->$method($sunrise, $sunset));
	}

	public static function genericEntryPointProvider(): array
	{
		return [
			'getChatzos' => ['getChatzos'],
			'getSofZmanShma' => ['getSofZmanShma'],
			'getSofZmanTfila' => ['getSofZmanTfila'],
			'getMinchaGedola' => ['getMinchaGedola'],
			'getMinchaKetana' => ['getMinchaKetana'],
			'getSamuchLeMinchaKetana' => ['getSamuchLeMinchaKetana'],
			'getPlagHamincha' => ['getPlagHamincha'],
		];
	}
}
