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

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PhpZmanim\JewishDate;
use PhpZmanim\Torah\YomTov;

/**
 * Coverage for the holiday / melacha / rosh-chodesh predicate surface of PhpZmanim\JewishDate
 * (Java's JewishCalendar). Every expectation is ground truth from the current KosherJava, driven
 * over the full cycle of Jewish year 5784 (a LEAP year, with a short Kislev and a nidche Fast of
 * Esther) for both in- and out-of-Israel.
 *
 * NOTE on getYomTov(): the PHP YomTov enum values are contiguous (0-37) while KosherJava's int
 * constants have gaps, so the numeric indexes are NOT comparable across the two ports. The case
 * NAMES are identical, so getYomTov() is asserted by ->name. Plain Rosh Chodesh is deliberately
 * NONE here (Java's getYomTovIndex() returns -1 for it; it is surfaced only via isRoshChodesh()).
 */
class HolidaysTest extends TestCase
{
	/*
	|--------------------------------------------------------------------------
	| HELPERS
	|--------------------------------------------------------------------------
	*/

	private function jd(int $jy, int $jm, int $jd, bool $inIsrael = false, bool $useModern = false): JewishDate
	{
		$date = JewishDate::create($jy, $jm, $jd, $inIsrael);
		$date->setUseModernHolidays($useModern);

		return $date;
	}

	/*
	|--------------------------------------------------------------------------
	| getYomTov() across the whole calendar (+ inIsrael / useModernHolidays branching)
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('yomTovProvider')]
	public function yomTov(int $jy, int $jm, int $jd, bool $inIsrael, bool $useModern, string $expected): void
	{
		$yomTov = $this->jd($jy, $jm, $jd, $inIsrael, $useModern)->getYomTov();

		$this->assertInstanceOf(YomTov::class, $yomTov);
		$this->assertSame($expected, $yomTov->name);
	}

	public static function yomTovProvider(): array
	{
		return [
			// Tishrei
			'Rosh Hashana 1'          => [5784, 7,  1,  false, false, 'ROSH_HASHANA'],
			'Fast of Gedalyah'        => [5784, 7,  3,  false, false, 'FAST_OF_GEDALYAH'],
			'Erev Yom Kippur'         => [5784, 7,  9,  false, false, 'EREV_YOM_KIPPUR'],
			'Yom Kippur'              => [5784, 7,  10, false, false, 'YOM_KIPPUR'],
			'Erev Succos'             => [5784, 7,  14, false, false, 'EREV_SUCCOS'],
			'Succos 1'                => [5784, 7,  15, false, false, 'SUCCOS'],
			'Succos 2 (diaspora)'     => [5784, 7,  16, false, false, 'SUCCOS'],
			'Succos 2 (Israel=CH"M)'  => [5784, 7,  16, true,  false, 'CHOL_HAMOED_SUCCOS'],
			'Chol Hamoed Succos'      => [5784, 7,  18, false, false, 'CHOL_HAMOED_SUCCOS'],
			'Hoshana Rabba'           => [5784, 7,  21, false, false, 'HOSHANA_RABBA'],
			'Shemini Atzeres'         => [5784, 7,  22, false, false, 'SHEMINI_ATZERES'],
			'Simchas Torah (diaspora)' => [5784, 7, 23, false, false, 'SIMCHAS_TORAH'],
			'23 Tishrei (Israel=Isru)' => [5784, 7, 23, true,  false, 'ISRU_CHAG'],
			'Isru Chag (diaspora)'    => [5784, 7,  24, false, false, 'ISRU_CHAG'],
			'24 Tishrei (Israel=none)' => [5784, 7, 24, true,  false, 'NONE'],
			// Kislev / Teves / Shvat
			'Chanukah 1'              => [5784, 9,  25, false, false, 'CHANUKAH'],
			'Asara BeTeves'           => [5784, 10, 10, false, false, 'TENTH_OF_TEVES'],
			'Tu BiShvat'              => [5784, 11, 15, false, false, 'TU_BESHVAT'],
			// Adar I (leap) / Adar II
			'Purim Katan'             => [5784, 12, 14, false, false, 'PURIM_KATAN'],
			'Shushan Purim Katan'     => [5784, 12, 15, false, false, 'SHUSHAN_PURIM_KATAN'],
			'Fast of Esther (nidche)' => [5784, 13, 11, false, false, 'FAST_OF_ESTHER'],
			'13 Adar II (Shabbos=none)' => [5784, 13, 13, false, false, 'NONE'],
			'Purim'                   => [5784, 13, 14, false, false, 'PURIM'],
			'Shushan Purim'           => [5784, 13, 15, false, false, 'SHUSHAN_PURIM'],
			// Nissan - Pesach
			'Erev Pesach'             => [5784, 1,  14, false, false, 'EREV_PESACH'],
			'Pesach 1'                => [5784, 1,  15, false, false, 'PESACH'],
			'Pesach 2 (diaspora)'     => [5784, 1,  16, false, false, 'PESACH'],
			'Pesach 2 (Israel=CH"M)'  => [5784, 1,  16, true,  false, 'CHOL_HAMOED_PESACH'],
			'Chol Hamoed Pesach'      => [5784, 1,  18, false, false, 'CHOL_HAMOED_PESACH'],
			'Pesach 7'                => [5784, 1,  21, false, false, 'PESACH'],
			'Pesach 8 (diaspora)'     => [5784, 1,  22, false, false, 'PESACH'],
			'22 Nissan (Israel=Isru)' => [5784, 1,  22, true,  false, 'ISRU_CHAG'],
			// Iyar
			'Pesach Sheni'            => [5784, 2,  14, false, false, 'PESACH_SHENI'],
			'Lag BaOmer'              => [5784, 2,  18, false, false, 'LAG_BAOMER'],
			// Sivan - Shavuos
			'Erev Shavuos'            => [5784, 3,  5,  false, false, 'EREV_SHAVUOS'],
			'Shavuos 1'               => [5784, 3,  6,  false, false, 'SHAVUOS'],
			'Shavuos 2 (diaspora)'    => [5784, 3,  7,  false, false, 'SHAVUOS'],
			'7 Sivan (Israel=Isru)'   => [5784, 3,  7,  true,  false, 'ISRU_CHAG'],
			// Summer fasts / Tu BeAv
			'17 Tammuz'               => [5784, 4,  17, false, false, 'SEVENTEEN_OF_TAMMUZ'],
			'Tisha BeAv'              => [5784, 5,  9,  false, false, 'TISHA_BEAV'],
			'Tu BeAv'                 => [5784, 5,  15, false, false, 'TU_BEAV'],
			// Ordinary weekday + plain Rosh Chodesh (both NONE)
			'Ordinary weekday'        => [5784, 8,  5,  false, false, 'NONE'],
			'Rosh Chodesh (=none)'    => [5784, 8,  1,  false, false, 'NONE'],
			// Modern holidays (useModernHolidays = true), observed dates in 5784
			'Yom HaShoah'             => [5784, 1,  28, false, true,  'YOM_HASHOAH'],
			'Yom HaZikaron'           => [5784, 2,  5,  false, true,  'YOM_HAZIKARON'],
			'Yom HaAtzmaut'           => [5784, 2,  6,  false, true,  'YOM_HAATZMAUT'],
			'Yom Yerushalayim'        => [5784, 2,  28, false, true,  'YOM_YERUSHALAYIM'],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| getDayOfOmer() - value during sefira, -1 otherwise
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('omerProvider')]
	public function dayOfOmer(int $jy, int $jm, int $jd, int $expected): void
	{
		$this->assertSame($expected, $this->jd($jy, $jm, $jd)->getDayOfOmer());
	}

	public static function omerProvider(): array
	{
		return [
			'15 Nissan (before)' => [5784, 1, 15, -1],
			'16 Nissan (omer 1)' => [5784, 1, 16, 1],
			'17 Nissan (omer 2)' => [5784, 1, 17, 2],
			'21 Nissan (omer 6)' => [5784, 1, 21, 6],
			'5 Iyar (omer 20)'   => [5784, 2, 5,  20],
			'18 Iyar (omer 33)'  => [5784, 2, 18, 33],
			'4 Sivan (omer 48)'  => [5784, 3, 4,  48],
			'5 Sivan (omer 49)'  => [5784, 3, 5,  49],
			'6 Sivan (after)'    => [5784, 3, 6,  -1],
			'Ordinary (none)'    => [5784, 8, 5,  -1],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| getDayOfChanukah() - value during Chanukah, -1 otherwise
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('chanukahProvider')]
	public function dayOfChanukah(int $jy, int $jm, int $jd, int $expected): void
	{
		$this->assertSame($expected, $this->jd($jy, $jm, $jd)->getDayOfChanukah());
	}

	public static function chanukahProvider(): array
	{
		// 5784 has a short Kislev, so Chanukah runs 25-29 Kislev then 1-3 Teves.
		return [
			'25 Kislev (day 1)' => [5784, 9,  25, 1],
			'26 Kislev (day 2)' => [5784, 9,  26, 2],
			'29 Kislev (day 5)' => [5784, 9,  29, 5],
			'1 Teves (day 6)'   => [5784, 10, 1,  6],
			'2 Teves (day 7)'   => [5784, 10, 2,  7],
			'3 Teves (day 8)'   => [5784, 10, 3,  8],
			'10 Teves (none)'   => [5784, 10, 10, -1],
			'Ordinary (none)'   => [5784, 8,  5,  -1],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| isTaanis() / isTaanisBechoros()
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('taanisProvider')]
	public function taanis(int $jy, int $jm, int $jd, bool $taanis, bool $bechoros): void
	{
		$date = $this->jd($jy, $jm, $jd);

		$this->assertSame($taanis, $date->isTaanis());
		$this->assertSame($bechoros, $date->isTaanisBechoros());
	}

	public static function taanisProvider(): array
	{
		return [
			//                             jy    jm  jd  taanis bechoros
			'Fast of Gedalyah'        => [5784, 7,  3,  true,  false],
			'Yom Kippur'              => [5784, 7,  10, true,  false],
			'Asara BeTeves'           => [5784, 10, 10, true,  false],
			'Fast of Esther (nidche)' => [5784, 13, 11, true,  false],
			'17 Tammuz'               => [5784, 4,  17, true,  false],
			'Tisha BeAv'              => [5784, 5,  9,  true,  false],
			'Erev Pesach (bechoros)'  => [5784, 1,  14, false, true],
			'13 Adar II (no fast)'    => [5784, 13, 13, false, false],
			'Yom Tov (none)'          => [5784, 7,  15, false, false],
			'Ordinary (none)'         => [5784, 8,  5,  false, false],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| Melacha family - isAssurBemelacha / isYomTovAssurBemelacha / hasCandleLighting /
	| isTomorrowShabbosOrYomTov / isErevYomTovSheni / isTonightMutarBemelacha
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('melachaProvider')]
	public function melacha(
		int $jy, int $jm, int $jd, bool $inIsrael,
		bool $assur, bool $ytAssur, bool $candle, bool $tomorrowSYT, bool $erevYTSheni, bool $tonightMutar
	): void
	{
		$date = $this->jd($jy, $jm, $jd, $inIsrael);

		$this->assertSame($assur, $date->isAssurBemelacha(), 'isAssurBemelacha');
		$this->assertSame($ytAssur, $date->isYomTovAssurBemelacha(), 'isYomTovAssurBemelacha');
		$this->assertSame($candle, $date->hasCandleLighting(), 'hasCandleLighting');
		$this->assertSame($tomorrowSYT, $date->isTomorrowShabbosOrYomTov(), 'isTomorrowShabbosOrYomTov');
		$this->assertSame($erevYTSheni, $date->isErevYomTovSheni(), 'isErevYomTovSheni');
		$this->assertSame($tonightMutar, $date->isTonightMutarBemelacha(), 'isTonightMutarBemelacha');
	}

	public static function melachaProvider(): array
	{
		return [
			//                              jy    jm  jd  isr    assur  ytAss  candle tomSYT erevYTS tonMutar
			'Rosh Hashana 1 (Shabbos)' => [5784, 7,  1,  false, true,  true,  true,  true,  true,   false],
			'Rosh Hashana 2 (Sunday)'  => [5784, 7,  2,  false, true,  true,  false, false, false,  true],
			'Yom Kippur'               => [5784, 7,  10, false, true,  true,  false, false, false,  true],
			'Erev Succos'              => [5784, 7,  14, false, false, false, true,  true,  false,  false],
			'Succos 2 (diaspora)'      => [5784, 7,  16, false, true,  true,  false, false, false,  true],
			'Succos 2 (Israel CH"M)'   => [5784, 7,  16, true,  false, false, false, false, false,  false],
			'Hoshana Rabba'            => [5784, 7,  21, false, false, false, true,  true,  false,  false],
			'Ordinary Erev Shabbos'    => [5784, 8,  5,  false, false, false, true,  true,  false,  false],
			'Chanukah on Shabbos'      => [5784, 9,  26, false, true,  false, false, false, false,  true],
			'Pesach 1'                 => [5784, 1,  15, false, true,  true,  true,  true,  true,   false],
			'Pesach 2 (diaspora)'      => [5784, 1,  16, false, true,  true,  false, false, false,  true],
			'Shavuos 1'                => [5784, 3,  6,  false, true,  true,  true,  true,  true,   false],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| Rosh Chodesh family - isRoshChodesh / isErevRoshChodesh / isMacharChodesh /
	| isShabbosMevorchim / isYomKippurKatan / isBeHaB
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('roshChodeshProvider')]
	public function roshChodesh(
		int $jy, int $jm, int $jd,
		bool $rc, bool $erc, bool $mc, bool $sm, bool $ykk, bool $bhb
	): void
	{
		$date = $this->jd($jy, $jm, $jd);

		$this->assertSame($rc, $date->isRoshChodesh(), 'isRoshChodesh');
		$this->assertSame($erc, $date->isErevRoshChodesh(), 'isErevRoshChodesh');
		$this->assertSame($mc, $date->isMacharChodesh(), 'isMacharChodesh');
		$this->assertSame($sm, $date->isShabbosMevorchim(), 'isShabbosMevorchim');
		$this->assertSame($ykk, $date->isYomKippurKatan(), 'isYomKippurKatan');
		$this->assertSame($bhb, $date->isBeHaB(), 'isBeHaB');
	}

	public static function roshChodeshProvider(): array
	{
		return [
			//                                jy    jm  jd  rc     erc    mc     sm     ykk    bhb
			'29 Tishrei (erev+machar+mev)' => [5784, 7,  29, false, true,  true,  true,  false, false],
			'30 Tishrei (RC day 1)'        => [5784, 7,  30, true,  false, false, false, false, false],
			'1 Cheshvan (RC day 2)'        => [5784, 8,  1,  true,  false, false, false, false, false],
			'BeHaB (Mon Cheshvan)'         => [5784, 8,  8,  false, false, false, false, false, true],
			'Shabbos Mevorchim'            => [5784, 8,  27, false, false, false, true,  false, false],
			'Erev RC + Yom Kippur Katan'   => [5784, 8,  29, false, true,  false, false, true,  false],
			'1 Kislev (RC)'                => [5784, 9,  1,  true,  false, false, false, false, false],
			'29 Adar I (erev+machar+mev)'  => [5784, 12, 29, false, true,  true,  true,  false, false],
			'30 Sivan (RC + machar)'       => [5784, 3,  30, true,  false, true,  false, false, false],
			'BeHaB (Iyar)'                 => [5784, 2,  5,  false, false, false, false, false, true],
			'Ordinary (all false)'         => [5784, 8,  5,  false, false, false, false, false, false],
		];
	}
}
