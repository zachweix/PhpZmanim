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

namespace PhpZmanim;

use Carbon\Carbon;
use DateTimeZone;
use InvalidArgumentException;

class GeoLocation
{
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	const DISTANCE = 0;
	const INITIAL_BEARING = 1;
	const FINAL_BEARING = 2;

	const MINUTE_MILLIS = 60 * 1000;
	const HOUR_MILLIS = self::MINUTE_MILLIS * 60;

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCTOR
	|--------------------------------------------------------------------------
	*/

	public function __construct(
		private float $latitude = 51.4772,
		private float $longitude = 0.0,
		private string $timezone = 'GMT',
		private float $elevation = 0.0,
		private ?string $locationName = null
	) {
		// These 3 need more validation
		$this->setLatitude($latitude);
		$this->setLongitude($longitude);
		$this->setElevation($elevation);
	}

	public static function create(float $latitude = 51.4772, float $longitude = 0.0, string $timezone = 'GMT', float $elevation = 0.0, ?string $locationName = null): self
	{
		return new static($latitude, $longitude, $timezone, $elevation, $locationName);
	}

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function getLatitude(): float
	{
		return $this->latitude;
	}

	public function setLatitude(float $latitude): self
	{
		if ($latitude > 90 || $latitude < -90 || is_nan($latitude)) {
			throw new InvalidArgumentException('Latitude must be between -90 and 90');
		}

		$this->latitude = $latitude;

		return $this;
	}

	public function getLongitude(): float
	{
		return $this->longitude;
	}

	public function setLongitude(float $longitude): self
	{
		if ($longitude > 180 || $longitude < -180 || is_nan($longitude)) {
			throw new InvalidArgumentException('Longitude must be between -180 and 180');
		}

		$this->longitude = $longitude;

		return $this;
	}

	public function getTimezone(): string
	{
		return $this->timezone;
	}

	public function setTimezone(string $timezone): self
	{
		$this->timezone = $timezone;

		return $this;
	}

	public function getElevation(): float
	{
		return $this->elevation;
	}

	public function setElevation(float $elevation): self
	{
		if ($elevation < 0) {
			throw new InvalidArgumentException('Elevation cannot be negative');
		}
		if (is_nan($elevation) || is_infinite($elevation)) {
			throw new InvalidArgumentException('Elevation cannot be NaN or infinite');
		}

		$this->elevation = $elevation;

		return $this;
	}

	public function getLocationName(): ?string
	{
		return $this->locationName;
	}

	public function setLocationName(?string $locationName): self
	{
		$this->locationName = $locationName;

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getLocalMeanTimeOffset(Carbon $datetime): float
	{
		$offset = (new DateTimeZone($this->timezone))->getOffset($datetime) * 1000;
		return $this->longitude * 4 * self::MINUTE_MILLIS - $offset;
	}

	public function getAntimeridianAdjustment(Carbon $datetime): int
	{
		$localHoursOffset = $this->getLocalMeanTimeOffset($datetime) / self::HOUR_MILLIS;

		if ($localHoursOffset >= 20) {
			return 1;
		} else if ($localHoursOffset <= -20) {
			return -1;
		}
		return 0;
	}

	/*
	|--------------------------------------------------------------------------
	| GEODESIC FORMULAS
	|--------------------------------------------------------------------------
	*/

	public function getGeodesicInitialBearing(GeoLocation $geoLocation): float
	{
		return $this->vincentyFormula($geoLocation, self::INITIAL_BEARING);
	}

	public function getGeodesicFinalBearing(GeoLocation $geoLocation): float
	{
		return $this->vincentyFormula($geoLocation, self::FINAL_BEARING);
	}

	public function getGeodesicDistance(GeoLocation $geoLocation): float
	{
		return $this->vincentyFormula($geoLocation, self::DISTANCE);
	}

	private function vincentyFormula(GeoLocation $geoLocation, int $formula): float
	{
		$a = 6378137;
		$b = 6356752.3142;
		$f = 1 / 298.257223563;
		$L = deg2rad($geoLocation->getLongitude() - $this->getLongitude());
		$U1 = atan((1 - $f) * tan(deg2rad($this->getLatitude())));
		$U2 = atan((1 - $f) * tan(deg2rad($geoLocation->getLatitude())));
		$sinU1 = sin($U1);
		$cosU1 = cos($U1);
		$sinU2 = sin($U2);
		$cosU2 = cos($U2);

		$lambda = $L;
		$lambdaP = 2 * M_PI;
		$iterLimit = 20;

		$sinLambda = 0;
		$cosLambda = 0;
		$sinSigma = 0;
		$cosSigma = 0;
		$sigma = 0;
		$sinAlpha = 0;
		$cosSqAlpha = 0;
		$cos2SigmaM = 0;

		while (abs($lambda - $lambdaP) > 1e-12 && --$iterLimit > 0) {
			$sinLambda = sin($lambda);
			$cosLambda = cos($lambda);
			$sinSigma = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda)
					+ ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) * ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));

			if ($sinSigma == 0) {
				return 0;
			}

			$cosSigma = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
			$sigma = atan2($sinSigma, $cosSigma);
			$sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
			$cosSqAlpha = 1 - $sinAlpha * $sinAlpha;
			$cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;

			if (is_nan($cos2SigmaM)) {
				$cos2SigmaM = 0;
			}

			$C = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
			$lambdaP = $lambda;
			$lambda = $L + (1 - $C) * $f * $sinAlpha
					* ($sigma + $C * $sinSigma * ($cos2SigmaM + $C * $cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));
		}

		if ($iterLimit == 0) {
			return NAN;
		}

		$uSq = $cosSqAlpha * ($a * $a - $b * $b) / ($b * $b);
		$A = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
		$B = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));
		$deltaSigma = $B
				* $sinSigma
				* ($cos2SigmaM + $B
						/ 4
						* ($cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM) - $B / 6 * $cos2SigmaM
								* (-3 + 4 * $sinSigma * $sinSigma) * (-3 + 4 * $cos2SigmaM * $cos2SigmaM)));
		$distance = $b * $A * ($sigma - $deltaSigma);


		$fwdAz = rad2deg(atan2($cosU2 * $sinLambda, $cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));
		$revAz = rad2deg(atan2($cosU1 * $sinLambda, -$sinU1 * $cosU2 + $cosU1 * $sinU2 * $cosLambda));

		if ($formula == self::DISTANCE) {
			return $distance;
		} else if ($formula == self::INITIAL_BEARING) {
			return $fwdAz;
		} else if ($formula == self::FINAL_BEARING) {
			return $revAz;
		} else {
			return NAN;
		}
	}

	public function getRhumbLineBearing(GeoLocation $geoLocation): float
	{
		$dLon = deg2rad($geoLocation->getLongitude() - $this->getLongitude());
		$dPhi = log(tan(deg2rad($geoLocation->getLatitude()) / 2 + M_PI / 4)
				/ tan(deg2rad($this->getLatitude()) / 2 + M_PI / 4));
		if (abs($dLon) > M_PI) {
			$dLon = $dLon > 0 ? -(2 * M_PI - $dLon) : (2 * M_PI + $dLon);
		}
		return rad2deg(atan2($dLon, $dPhi));
	}

	public function getRhumbLineDistance(GeoLocation $geoLocation): float
	{
		$earthRadius = 6378137;
		$dLat = deg2rad($geoLocation->getLatitude()) - deg2rad($this->getLatitude());
		$dLon = abs(deg2rad($geoLocation->getLongitude()) - deg2rad($this->getLongitude()));
		$dPhi = log(tan(deg2rad($geoLocation->getLatitude()) / 2 + M_PI / 4)
				/ tan(deg2rad($this->getLatitude()) / 2 + M_PI / 4));
		$q = $dLat / $dPhi;

		if (!is_finite($q)) {
			$q = cos(deg2rad($this->getLatitude()));
		}

		if ($dLon > M_PI) {
			$dLon = 2 * M_PI - $dLon;
		}
		$d = sqrt($dLat * $dLat + $q * $q * $dLon * $dLon);
		return $d * $earthRadius;
	}

	/*
	|--------------------------------------------------------------------------
	| CLONEABLE
	|--------------------------------------------------------------------------
	*/

	public function copy(): self
	{
		return clone $this;
	}
}
