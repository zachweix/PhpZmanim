<?php

/**
 * Zmanim PHP API
 * Copyright (C) 2019-2023 Zachary Weixelbaum
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

namespace PhpZmanim\Calculator;

use Carbon\Carbon;
use InvalidArgumentException;
use PhpZmanim\GeoLocation;

class SPACalculator extends AstronomicalCalculator
{
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	const JULIAN_DAY_JAN_1_2000 = 2451545.0;
	const JULIAN_DAYS_PER_CENTURY = 36525.0;

	const SUNRISE = 0;
	const SUNSET = 1;
	const NOON = 2;
	const MIDNIGHT = 3;

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTOR
	|--------------------------------------------------------------------------
	*/

	public function __construct(
		private bool $applyDeltaT = true,
		private ?float $deltaTOverride = null,
		private float $pressure = 1013.25,
		private float $temperature = 10.0,
	) {
		parent::__construct();
	}

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function getApplyDeltaT(): bool
	{
		return $this->applyDeltaT;
	}

	public function setApplyDeltaT(bool $applyDeltaT): self
	{
		$this->applyDeltaT = $applyDeltaT;

		return $this;
	}

	public function getDeltaTOverride(): ?float
	{
		return $this->deltaTOverride;
	}

	public function setDeltaTOverride(?float $deltaTOverride): self
	{
		$this->deltaTOverride = $deltaTOverride;

		return $this;
	}

	public function getPressure(): float
	{
		return $this->pressure;
	}

	public function setPressure(float $pressure): self
	{
		$this->pressure = $pressure;

		return $this;
	}

	public function getTemperature(): float
	{
		return $this->temperature;
	}

	public function setTemperature(float $temperature): self
	{
		$this->temperature = $temperature;

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getUTCSunrise(Carbon $date, GeoLocation $geo, float $zenith, bool $adjustForElevation): float
	{
		return $this->getUTCSunRiseSet($date, $geo, $zenith, $adjustForElevation, self::SUNRISE);
	}

	public function getUTCSunset(Carbon $date, GeoLocation $geo, float $zenith, bool $adjustForElevation): float
	{
		return $this->getUTCSunRiseSet($date, $geo, $zenith, $adjustForElevation, self::SUNSET);
	}

	public function getUTCNoon(Carbon $date, GeoLocation $geo): float
	{
		$noon = $this->solveNoonMidnight(self::getJulianDay($date), -$geo->getLongitude(), self::NOON) / 60;
		return fmod(fmod($noon, 24) + 24, 24);
	}

	public function getUTCMidnight(Carbon $date, GeoLocation $geo): float
	{
		$midnight = $this->solveNoonMidnight(self::getJulianDay($date), -$geo->getLongitude(), self::MIDNIGHT) / 60;
		return fmod(fmod($midnight, 24) + 24, 24);
	}

	public function getTimeAtAzimuth(Carbon $date, GeoLocation $geo, float $azimuth): float
	{
		if ($azimuth != 90.0 && $azimuth != 270.0) {
			throw new InvalidArgumentException('The azimuth must be 90 or 270. Other azimuth values are not supported');
		}

		$julianDay = self::getJulianDay($date);
		$solarNoonBase = 0.5 - ($geo->getLongitude() / 360.0);
		$dateTime = $solarNoonBase + (($azimuth == 90.0) ? 0.25 : 0.75);

		for ($i = 0; $i < 4; $i++) {
			$jd = $julianDay + $dateTime;
			$decl = $this->solarCoords($jd)[1];
			$ratio = tan(deg2rad($decl)) / tan(deg2rad($geo->getLatitude()));

			if (is_nan($ratio) || $ratio > 1.0 || $ratio < -1.0) {
				return NAN;
			}

			$offset = (($azimuth == 90.0) ? -1.0 : 1.0) * (rad2deg(acos($ratio)) / 360.0);
			$dateTime = $solarNoonBase + $offset - ($this->equationOfTime($jd) / 1440.0);
		}

		$timeUTC = $dateTime * 24.0;
		return fmod(fmod($timeUTC, 24) + 24, 24);
	}

	public function getSolarElevation(Carbon $datetime, GeoLocation $geo): float
	{
		$jd = self::julianDayFromInstant($datetime);
		$topo = $this->topocentric($jd, $geo->getLatitude(), $geo->getLongitude(), $geo->getElevation());
		return $topo[1];
	}

	public function getSolarAzimuth(Carbon $datetime, GeoLocation $geo): float
	{
		$jd = self::julianDayFromInstant($datetime);
		$topo = $this->topocentric($jd, $geo->getLatitude(), $geo->getLongitude(), $geo->getElevation());
		return $topo[2];
	}

	/*
	|--------------------------------------------------------------------------
	| STATIC FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	private static function getJulianDay(Carbon $date): float
	{
		$year = $date->year;
		$month = $date->month;
		$day = $date->day;

		if ($month <= 2) {
			$year -= 1;
			$month += 12;
		}

		$a = (int) ($year / 100);
		$b = (int) (2 - $a + (int) ($a / 4));

		return floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $b - 1524.5;
	}

	private static function julianDayFromInstant(Carbon $datetime): float
	{
		$utc = $datetime->copy()->utc();
		$fractionalDay = $utc->secondsSinceMidnight() / 86400.0;
		return self::getJulianDay($utc) + $fractionalDay;
	}

	private static function meanObliquity(float $jme): float
	{
		$u = $jme / 10.0;
		$seconds = 84381.448 - 4680.93 * $u - 1.55 * $u * $u + 1999.25 * pow($u, 3) - 51.38 * pow($u, 4)
				- 249.67 * pow($u, 5) - 39.05 * pow($u, 6) + 7.12 * pow($u, 7) + 27.87 * pow($u, 8)
				+ 5.79 * pow($u, 9) + 2.45 * pow($u, 10);
		return $seconds / 3600.0;
	}

	private static function nutation(float $jce): array
	{
		$t = $jce;
		$omega = 125.04452 - 1934.136261 * $t + 0.0020708 * $t * $t + $t * $t * $t / 450000.0;
		$lSun = 280.4665 + 36000.7698 * $t;
		$lMoon = 218.3165 + 481267.8813 * $t;
		$deltaPsiArcsec = -17.20 * sin(deg2rad($omega)) - 1.32 * sin(deg2rad(2 * $lSun))
				- 0.23 * sin(deg2rad(2 * $lMoon)) + 0.21 * sin(deg2rad(2 * $omega));
		$deltaEpsArcsec = 9.20 * cos(deg2rad($omega)) + 0.57 * cos(deg2rad(2 * $lSun))
				+ 0.10 * cos(deg2rad(2 * $lMoon)) - 0.09 * cos(deg2rad(2 * $omega));
		return [$deltaPsiArcsec / 3600.0, $deltaEpsArcsec / 3600.0];
	}

	private static function estimateDeltaT(float $julianDay): float
	{
		$year = 2000.0 + ($julianDay - self::JULIAN_DAY_JAN_1_2000) / 365.25;

		if ($year >= 2005 && $year < 2050) {
			$t = $year - 2000;
			return 62.92 + 0.32217 * $t + 0.005589 * $t * $t;
		} else if ($year >= 1986 && $year < 2005) {
			$t = $year - 2000;
			return 63.86 + 0.3345 * $t - 0.060374 * $t * $t + 0.0017275 * $t * $t * $t
					+ 0.000651814 * pow($t, 4) + 0.00002373599 * pow($t, 5);
		} else if ($year >= 1961 && $year < 1986) {
			$t = $year - 1975;
			return 45.45 + 1.067 * $t - $t * $t / 260.0 - $t * $t * $t / 718.0;
		} else if ($year >= 2050 && $year < 2150) {
			return -20 + 32 * pow(($year - 1820) / 100.0, 2) - 0.5628 * (2150 - $year);
		} else {
			$u = ($year - 1820) / 100.0;
			return -20 + 32 * $u * $u;
		}
	}

	private static function sumSeries(array $series, float $tau): float
	{
		$total = 0.0;
		$tauPow = 1.0;
		foreach ($series as $terms) {
			$sum = 0.0;
			foreach ($terms as $term) {
				$sum += $term[0] * cos($term[1] + $term[2] * $tau);
			}
			$total += $sum * $tauPow;
			$tauPow *= $tau;
		}
		return $total / 1.0e8;
	}

	/*
	|--------------------------------------------------------------------------
	| CALCULATIONS
	|--------------------------------------------------------------------------
	*/

	private function getUTCSunRiseSet(Carbon $date, GeoLocation $geo, float $zenith, bool $adjustForElevation, $solarEvent): float
	{
		$elevation = $adjustForElevation ? $geo->getElevation() : 0;
		$adjustedZenith = $this->adjustZenith($zenith, $elevation, $date);
		$riseSet = $this->solveRiseSet($date, $geo, $adjustedZenith, $solarEvent);
		if (is_nan($riseSet)) {
			return NAN;
		}
		$riseSet = $riseSet / 60;
		return fmod(fmod($riseSet, 24) + 24, 24);
	}

	private function solveRiseSet(Carbon $date, GeoLocation $geo, float $adjustedZenith, $solarEvent): float
	{
		$jdDay = self::getJulianDay($date);
		$lonWest = -$geo->getLongitude();
		$lat = $geo->getLatitude();
		$elevation = $geo->getElevation();

		$noonMin = $this->solveNoonMidnight($jdDay, $lonWest, self::NOON);
		$noonCoords = $this->solarCoords($jdDay + $noonMin / 1440.0);
		$declNoon = $noonCoords[1];
		$eot = $this->equationOfTime($jdDay + $noonMin / 1440.0);
		$cosH0 = (cos(deg2rad($adjustedZenith)) - sin(deg2rad($lat)) * sin(deg2rad($declNoon)))
				/ (cos(deg2rad($lat)) * cos(deg2rad($declNoon)));
		if ($cosH0 < -1.0 || $cosH0 > 1.0) {
			return NAN;
		}
		$h0 = rad2deg(acos($cosH0));
		$signed = ($solarEvent == self::SUNRISE) ? $h0 : -$h0;
		$guess = 720 + 4 * ($lonWest - $signed) - $eot;

		$t0 = $guess;
		$t1 = $guess + 0.5;
		$f0 = $this->topocentricTrueZenith($jdDay + $t0 / 1440.0, $lat, $geo->getLongitude(), $elevation) - $adjustedZenith;
		$f1 = $this->topocentricTrueZenith($jdDay + $t1 / 1440.0, $lat, $geo->getLongitude(), $elevation) - $adjustedZenith;
		for ($i = 0; $i < 12 && abs($f1) > 1e-9; $i++) {
			$denom = ($f1 - $f0);
			if ($denom == 0) {
				break;
			}
			$t2 = $t1 - $f1 * ($t1 - $t0) / $denom;
			$t0 = $t1;
			$f0 = $f1;
			$t1 = $t2;
			$f1 = $this->topocentricTrueZenith($jdDay + $t1 / 1440.0, $lat, $geo->getLongitude(), $elevation) - $adjustedZenith;
		}
		return $t1;
	}

	private function solveNoonMidnight(float $julianDay, float $lonWest, $solarEvent): float
	{
		$lonEast = -$lonWest;
		$targetHourAngle = ($solarEvent == self::NOON) ? 0.0 : 180.0;
		$dayFraction = (($solarEvent == self::NOON) ? 0.5 : 1.0) - $lonEast / 360.0;
		for ($i = 0; $i < 3; $i++) {
			$sc = $this->solarCoords($julianDay + $dayFraction);
			$alpha = $sc[0];
			$nu = $sc[3];
			$hourAngle = fmod(fmod($nu + $lonEast - $alpha - $targetHourAngle, 360) + 540, 360) - 180;
			$dayFraction -= $hourAngle / 360.0;
		}
		return $dayFraction * 1440.0;
	}

	private function solarCoords(float $julianDayUT): array
	{
		$deltaTSeconds = $this->applyDeltaT ? (!is_null($this->deltaTOverride) ? $this->deltaTOverride : self::estimateDeltaT($julianDayUT)) : 0.0;
		$jde = $julianDayUT + $deltaTSeconds / 86400.0;
		$jce = ($jde - self::JULIAN_DAY_JAN_1_2000) / self::JULIAN_DAYS_PER_CENTURY;
		$jc = ($julianDayUT - self::JULIAN_DAY_JAN_1_2000) / self::JULIAN_DAYS_PER_CENTURY;
		$jme = $jce / 10.0;

		$earthL = rad2deg(self::sumSeries(self::EARTH_L, $jme));
		$earthB = rad2deg(self::sumSeries(self::EARTH_B, $jme));
		$radius = self::sumSeries(self::EARTH_R, $jme);

		$theta = fmod($earthL + 180.0, 360.0);
		$beta = -$earthB;

		$nut = self::nutation($jce);
		$epsilon = self::meanObliquity($jme) + $nut[1];
		$aberration = -20.4898 / (3600.0 * $radius);
		$lambda = $theta + $nut[0] + $aberration;

		$nu0 = 280.46061837 + 360.98564736629 * ($julianDayUT - self::JULIAN_DAY_JAN_1_2000)
				+ 0.000387933 * $jc * $jc - $jc * $jc * $jc / 38710000.0;
		$nu0 = fmod(fmod($nu0, 360) + 360, 360);
		$nu = $nu0 + $nut[0] * cos(deg2rad($epsilon));

		$alpha = rad2deg(atan2(
				sin(deg2rad($lambda)) * cos(deg2rad($epsilon)) - tan(deg2rad($beta)) * sin(deg2rad($epsilon)),
				cos(deg2rad($lambda))));
		$alpha = fmod(fmod($alpha, 360) + 360, 360);
		$delta = rad2deg(asin(sin(deg2rad($beta)) * cos(deg2rad($epsilon))
				+ cos(deg2rad($beta)) * sin(deg2rad($epsilon)) * sin(deg2rad($lambda))));

		return [$alpha, $delta, $epsilon, $nu, $radius, $lambda];
	}

	private function topocentric(float $julianDayUT, float $latitude, float $longitude, float $elevationMeters): array
	{
		$sc = $this->solarCoords($julianDayUT);
		$alpha = $sc[0];
		$delta = $sc[1];
		$nu = $sc[3];
		$radius = $sc[4];

		$h = fmod($nu + $longitude - $alpha, 360.0);
		$h = fmod($h + 360, 360);

		$xi = 8.794 / (3600.0 * $radius);
		$u = atan(0.99664719 * tan(deg2rad($latitude)));
		$x = cos($u) + ($elevationMeters / 6378140.0) * cos(deg2rad($latitude));
		$y = 0.99664719 * sin($u) + ($elevationMeters / 6378140.0) * sin(deg2rad($latitude));

		$deltaAlpha = rad2deg(atan2(-$x * sin(deg2rad($xi)) * sin(deg2rad($h)),
				cos(deg2rad($delta)) - $x * sin(deg2rad($xi)) * cos(deg2rad($h))));
		$deltaPrime = rad2deg(atan2(
				(sin(deg2rad($delta)) - $y * sin(deg2rad($xi))) * cos(deg2rad($deltaAlpha)),
				cos(deg2rad($delta)) - $x * sin(deg2rad($xi)) * cos(deg2rad($h))));
		$hPrime = $h - $deltaAlpha;

		$e0 = rad2deg(asin(sin(deg2rad($latitude)) * sin(deg2rad($deltaPrime))
				+ cos(deg2rad($latitude)) * cos(deg2rad($deltaPrime)) * cos(deg2rad($hPrime))));

		$deltaE = $this->refractionCorrection($e0);
		$e = $e0 + $deltaE;

		$gamma = rad2deg(atan2(sin(deg2rad($hPrime)),
				cos(deg2rad($hPrime)) * sin(deg2rad($latitude)) - tan(deg2rad($deltaPrime)) * cos(deg2rad($latitude))));
		$azimuth = fmod($gamma + 180.0, 360.0);
		$azimuth = fmod($azimuth + 360, 360);

		return [$e0, $e, $azimuth];
	}

	private function topocentricTrueZenith(float $julianDayUT, float $latitude, float $longitude, float $elevationMeters): float
	{
		return 90.0 - $this->topocentric($julianDayUT, $latitude, $longitude, $elevationMeters)[0];
	}

	private function refractionCorrection(float $trueElevation): float
	{
		$atmosRefract = $this->getRefraction();
		$sunRadius = $this->getSolarRadius();
		if ($trueElevation < -1.0 * ($sunRadius + $atmosRefract)) {
			return 0.0;
		}
		return ($this->pressure / 1010.0) * (283.0 / (273.0 + $this->temperature))
				* 1.02 / (60.0 * tan(deg2rad($trueElevation + 10.3 / ($trueElevation + 5.11))));
	}

	private function equationOfTime(float $julianDayUT): float
	{
		$sc = $this->solarCoords($julianDayUT);
		$alpha = $sc[0];
		$epsilon = $sc[2];
		$deltaTSeconds = $this->applyDeltaT ? (!is_null($this->deltaTOverride) ? $this->deltaTOverride : self::estimateDeltaT($julianDayUT)) : 0.0;
		$jme = (($julianDayUT + $deltaTSeconds / 86400.0) - self::JULIAN_DAY_JAN_1_2000) / self::JULIAN_DAYS_PER_CENTURY / 10.0;
		$l0 = 280.4664567 + $jme * (360007.6982779 + $jme * (0.03032028
				+ $jme * (1.0 / 49931.0 - $jme * (1.0 / 15300.0 + $jme / 2000000.0))));
		$l0 = fmod(fmod($l0, 360) + 360, 360);
		$deltaPsi = self::nutation((($julianDayUT + $deltaTSeconds / 86400.0) - self::JULIAN_DAY_JAN_1_2000) / self::JULIAN_DAYS_PER_CENTURY)[0];
		$e = $l0 - 0.0057183 - $alpha + $deltaPsi * cos(deg2rad($epsilon));
		$e = fmod(fmod($e + 180, 360) + 360, 360) - 180;
		return $e * 4.0;
	}

	/*
	|--------------------------------------------------------------------------
	| VSOP87 SERIES
	|--------------------------------------------------------------------------
	*/

	const EARTH_L = [
		[
			[175347046, 0, 0], [3341656, 4.6692568, 6283.07585], [34894, 4.6261, 12566.1517],
			[3497, 2.7441, 5753.3849], [3418, 2.8289, 3.5231], [3136, 3.6277, 77713.7715],
			[2676, 4.4181, 7860.4194], [2343, 6.1352, 3930.2097], [1324, 0.7425, 11506.7698],
			[1273, 2.0371, 529.6910], [1199, 1.1096, 1577.3435], [990, 5.233, 5884.927],
			[902, 2.045, 26.298], [857, 3.508, 398.149], [780, 1.179, 5223.694],
			[753, 2.533, 5507.553], [505, 4.583, 18849.228], [492, 4.205, 775.523],
			[357, 2.920, 0.067], [317, 5.849, 11790.629], [284, 1.899, 796.298],
			[271, 0.315, 10977.079], [243, 0.345, 5486.778], [206, 4.806, 2544.314],
			[205, 1.869, 5573.143], [202, 2.458, 6069.777], [156, 0.833, 213.299],
			[132, 3.411, 2942.463], [126, 1.083, 20.775], [115, 0.645, 0.980],
			[103, 0.636, 4694.003], [102, 0.976, 15720.839], [102, 4.267, 7.114],
			[99, 6.21, 2146.17], [98, 0.68, 155.42], [86, 5.98, 161000.69],
			[85, 1.30, 6275.96], [85, 3.67, 71430.70], [80, 1.81, 17260.15],
			[79, 3.04, 12036.46], [75, 1.76, 5088.63], [74, 3.50, 3154.69],
			[74, 4.68, 801.82], [70, 0.83, 9437.76], [62, 3.98, 8827.39],
			[61, 1.82, 7084.90], [57, 2.78, 6286.60], [56, 4.39, 14143.50],
			[56, 3.47, 6279.55], [52, 0.19, 12139.55], [52, 1.33, 1748.02],
			[51, 0.28, 5856.48], [49, 0.49, 1194.45], [41, 5.37, 8429.24],
			[41, 2.40, 19651.05], [39, 6.17, 10447.39], [37, 6.04, 10213.29],
			[37, 2.57, 1059.38], [36, 1.71, 2352.87], [36, 1.78, 6812.77],
			[33, 0.59, 17789.85], [30, 0.44, 83996.85], [30, 2.74, 1349.87],
			[25, 3.16, 4690.48]
		],
		[
			[628331966747.0, 0, 0], [206059, 2.678235, 6283.07585], [4303, 2.6351, 12566.1517],
			[425, 1.590, 3.523], [119, 5.796, 26.298], [109, 2.966, 1577.344],
			[93, 2.59, 18849.23], [72, 1.14, 529.69], [68, 1.87, 398.15],
			[67, 4.41, 5507.55], [59, 2.89, 5223.69], [56, 2.17, 155.42],
			[45, 0.40, 796.30], [36, 0.47, 775.52], [29, 2.65, 7.11],
			[21, 5.34, 0.98], [19, 1.85, 5486.78], [19, 4.97, 213.30],
			[17, 2.99, 6275.96], [16, 0.03, 2544.31], [16, 1.43, 2146.17],
			[15, 1.21, 10977.08], [12, 2.83, 1748.02], [12, 3.26, 5088.63],
			[12, 5.27, 1194.45], [12, 2.08, 4694.00], [11, 0.77, 553.57],
			[10, 1.30, 6286.60], [10, 4.24, 1349.87], [9, 2.70, 242.73],
			[9, 5.64, 951.72], [8, 5.30, 2352.87], [6, 2.65, 9437.76],
			[6, 4.67, 4690.48]
		],
		[
			[52919, 0, 0], [8720, 1.0721, 6283.0758], [309, 0.867, 12566.152],
			[27, 0.05, 3.52], [16, 5.19, 26.30], [16, 3.68, 155.42],
			[10, 0.76, 18849.23], [9, 2.06, 77713.77], [7, 0.83, 775.52],
			[5, 4.66, 1577.34], [4, 1.03, 7.11], [4, 3.44, 5573.14],
			[3, 5.14, 796.30], [3, 6.05, 5507.55], [3, 1.19, 242.73],
			[3, 6.12, 529.69], [3, 0.31, 398.15], [3, 2.28, 553.57],
			[2, 4.38, 5223.69], [2, 3.75, 0.98]
		],
		[
			[289, 5.844, 6283.076], [35, 0, 0], [17, 5.49, 12566.15],
			[3, 5.20, 155.42], [1, 4.72, 3.52], [1, 5.30, 18849.23], [1, 5.97, 242.73]
		],
		[
			[114, 3.142, 0], [8, 4.13, 6283.08], [1, 3.84, 12566.15]
		],
		[
			[1, 3.14, 0]
		]
	];

	const EARTH_B = [
		[
			[280, 3.199, 84334.662], [102, 5.422, 5507.553], [80, 3.88, 5223.69],
			[44, 3.70, 2352.87], [32, 4.00, 1577.34]
		],
		[
			[9, 3.90, 5507.55], [6, 1.73, 5223.69]
		]
	];

	const EARTH_R = [
		[
			[100013989, 0, 0], [1670700, 3.0984635, 6283.07585], [13956, 3.05525, 12566.1517],
			[3084, 5.1985, 77713.7715], [1628, 1.1739, 5753.3849], [1576, 2.8469, 7860.4194],
			[925, 5.453, 11506.770], [542, 4.564, 3930.210], [472, 3.661, 5884.927],
			[346, 0.964, 5507.553], [329, 5.900, 5223.694], [307, 0.299, 5573.143],
			[243, 4.273, 11790.629], [212, 5.847, 1577.344], [186, 5.022, 10977.079],
			[175, 3.012, 18849.228], [110, 5.055, 5486.778], [98, 0.89, 6069.78],
			[86, 5.69, 15720.84], [86, 1.27, 161000.69], [65, 0.27, 17260.15],
			[63, 0.92, 529.69], [57, 2.01, 83996.85], [56, 5.24, 71430.70],
			[49, 3.25, 2544.31], [47, 2.58, 775.52], [45, 5.54, 9437.76],
			[43, 6.01, 6275.96], [39, 5.36, 4694.00], [38, 2.39, 8827.39],
			[37, 0.83, 19651.05], [37, 4.90, 12139.55], [36, 1.67, 12036.46],
			[35, 1.84, 2942.46], [33, 0.24, 7084.90], [32, 0.18, 5088.63],
			[32, 1.78, 398.15], [28, 1.21, 6286.60], [28, 1.90, 6279.55],
			[26, 4.59, 10447.39]
		],
		[
			[103019, 1.10749, 6283.07585], [1721, 1.0644, 12566.1517], [702, 3.142, 0],
			[32, 1.02, 18849.23], [31, 2.84, 5507.55], [25, 1.32, 5223.69],
			[18, 1.42, 1577.34], [10, 5.91, 10977.08], [9, 1.42, 6275.96], [9, 0.27, 5486.78]
		],
		[
			[4359, 5.7846, 6283.0758], [124, 5.579, 12566.152], [12, 3.14, 0],
			[9, 3.63, 77713.77], [6, 1.87, 5573.14], [3, 5.47, 18849.23]
		],
		[
			[145, 4.273, 6283.076], [7, 3.92, 12566.15]
		],
		[
			[4, 2.56, 6283.08]
		]
	];
}
