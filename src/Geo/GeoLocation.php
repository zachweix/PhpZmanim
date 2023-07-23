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

namespace PhpZmanim\Geo;

use DateTime;
use DateTimeZone;

class GeoLocation {
	/*
	|--------------------------------------------------------------------------
	| CLASS PROPERTIES AND CONSTANTS
	|--------------------------------------------------------------------------
	*/

	private $latitude;
	private $longitude; 
	private $locationName;
	private $timeZone;
	private $elevation;

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

	public function __construct($locationName = null, $latitude = 51.4772, $longitude = 0.0, $elevation = 0.0, $timeZone = "GMT") {
		$this->setLocationName($locationName);
		$this->setLatitude($latitude);
		$this->setLongitude($longitude);
		$this->setElevation($elevation);
		$this->setTimeZone($timeZone);
	}

	/*
	|--------------------------------------------------------------------------
	| ELEVATION
	|--------------------------------------------------------------------------
	*/

	public function getElevation() {
		return $this->elevation;
	}

	public function setElevation($elevation) {
		if ($elevation < 0) {
			throw new \Exception("Elevation cannot be negative");
		}
		$this->elevation = $elevation;
	}

	/*
	|--------------------------------------------------------------------------
	| GETTERS AND SETTERS
	|--------------------------------------------------------------------------
	*/

	public function setLatitude($latitude) {
		if ($latitude > 90.0 || $latitude < -90.0) {
			throw new \Exception("Latitude must be between -90 and 90");
		}

		$this->latitude = $latitude;
	}

	public function setLatitudeFromDegrees($degrees, $minutes, $seconds, $direction) {
		$tempLat = $degrees + (($minutes + ($seconds / 60.0)) / 60.0);

		if ($tempLat > 90 || $tempLat < 0) {
			throw new \Exception("Latitude must be between 0 and  90. Use direction of S instead of negative.");
		}

		if ($direction == "S") {
			$tempLat *= -1;
		} else if ($direction != "N") {
			throw new IllegalArgumentException("Latitude direction must be N or S");
		}

		$this->latitude = $tempLat;
	}

	public function getLatitude() {
		return $this->latitude;
	}

	public function setLongitude($longitude) {
		if ($longitude > 180.0 || $longitude < -180.0) {
			throw new \Exception("Longitude must be between -180 and 180");
		}

		$this->longitude = $longitude;
	}

	public function setLongitudeFromDegrees($degrees, $minutes, $seconds, $direction) {
		$longTemp = $degrees + (($minutes + ($seconds / 60.0)) / 60.0);

		if ($longTemp > 180 || $longTemp < 0) {
			throw new \Exception("Latitude must be between 0 and  180. Use direction of W instead of negative.");
		}

		if ($direction == "W") {
			$longTemp *= -1;
		} else if ($direction != "E") {
			throw new IllegalArgumentException("Logitude direction must be E or W");
		}

		$this->longitude = $longTemp;
	}

	public function getLongitude() {
		return $this->longitude;
	}

	public function getLocationName() {
		return $this->locationName;
	}

	public function setLocationName($locationName) {
		$this->locationName = $locationName;
	}

	public function getTimeZone() {
		return $this->timeZone;
	}

	public function setTimeZone($timeZone) {
		$this->timeZone = $timeZone;
	}

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	public function getLocalMeanTimeOffset() {
		$timeZone = new DateTimeZone($this->getTimeZone());
		$utc = new DateTimeZone("UTC");
		$utc_date = new DateTime("now", $utc);
		$offset = $timeZone->getOffset($utc_date) * 1000;
		return $this->getLongitude() * 4 * self::MINUTE_MILLIS - $offset;
	}

	public function getAntimeridianAdjustment() {
		$localHoursOffset = $this->getLocalMeanTimeOffset() / self::HOUR_MILLIS;

		if ($localHoursOffset >= 20){
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

	public function getGeodesicInitialBearing(GeoLocation $geoLocation) {
		return $this->vincentyFormula($geoLocation, self::INITIAL_BEARING);
	}

	public function getGeodesicFinalBearing(GeoLocation $geoLocation) {
		return $this->vincentyFormula($geoLocation, self::FINAL_BEARING);
	}

	public function getGeodesicDistance(GeoLocation $geoLocation) {
		return $this->vincentyFormula($geoLocation, self::DISTANCE);
	}

	private function vincentyFormula(GeoLocation $geoLocation, $formula) {
		$a = 6378137; // Equitorial Radius
		$b = 6356752.3142; // Polar Radius
		$f = 1 / 298.257223563; // WGS-84 ellipsiod
		$L = deg2rad($geoLocation->getLongitude() - $this->getLongitude());
		$U1 = atan((1 - $f) * tan(deg2rad($this->getLatitude())));
		$U2 = atan((1 - $f) * tan(deg2rad($geoLocation->getLatitude())));
		$sinU1 = sin($U1);
		$cosU1 = cos($U1);
		$sinU2 = sin($U2);
		$cosU2 = cos($U2);

		$lambda = $L;
		$lambdaP = 2 * pi();
		$iterLimit = 20;

		$sinLambda = 0;
		$cosLambda = 0;
		$sinSigma = 0;
		$cosSigma = 0;
		$sigma = 0;
		$sinAlpha = 0;
		$cosSqAlpha = 0;
		$cos2SigmaM = 0;

		$C;

		while (abs($lambda - $lambdaP) > 1e-12 && --$iterLimit > 0) {
			$sinLambda = sin($lambda);
			$cosLambda = cos($lambda);
			$sinSigma = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda)
					+ ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) * ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));

			if ($sinSigma == 0) {
				return 0; // co-incident points
			}

			$cosSigma = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
			$sigma = atan2($sinSigma, $cosSigma);
			$sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
			$cosSqAlpha = 1 - $sinAlpha * $sinAlpha;
			$cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;

			if (is_nan($cos2SigmaM)) {
				$cos2SigmaM = 0; // equatorial line: cosSqAlpha=0 (6)
			}

			$C = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
			$lambdaP = $lambda;
			$lambda = $L + (1 - $C) * $f * $sinAlpha
					* ($sigma + $C * $sinSigma * ($cos2SigmaM + $C * $cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));
		}

		if ($iterLimit == 0) {
			return false; // formula failed to converge
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

		// initial bearing
		$fwdAz = rad2deg(atan2($cosU2 * $sinLambda, $cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));
		// final bearing
		$revAz = rad2deg(atan2($cosU1 * $sinLambda, -$sinU1 * $cosU2 + $cosU1 * $sinU2 * $cosLambda));

		if ($formula == self::DISTANCE) {
			return $distance;
		} else if ($formula == self::INITIAL_BEARING) {
			return $fwdAz;
		} else if ($formula == self::FINAL_BEARING) {
			return $revAz;
		} else { // should never happpen
			return false;
		}
	}

	public function getRhumbLineBearing(GeoLocation $geoLocation) {
		$dLon = deg2rad($geoLocation->getLongitude() - $this->getLongitude());
		$dPhi = log(tan(deg2rad($geoLocation->getLatitude()) / 2 + pi() / 4)
				/ tan(deg2rad($this->getLatitude()) / 2 + pi() / 4));
		if (abs($dLon) > pi()) {
			$dLon = $dLon > 0 ? -(2 * pi() - $dLon) : (2 * pi() + $dLon);
		}
		return rad2deg(atan2($dLon, $dPhi));
	}

	public function getRhumbLineDistance(GeoLocation $geoLocation) {
		$earthRadius = 6378137; // Earth's radius in meters (WGS-84)
		$dLat = deg2rad($geoLocation->getLatitude()) - deg2rad($this->getLatitude());
		$dLon = abs(deg2rad($geoLocation->getLongitude()) - deg2rad($this->getLongitude()));
		$dPhi = log(tan(deg2rad($geoLocation->getLatitude()) / 2 + pi() / 4)
				/ tan(deg2rad($this->getLatitude()) / 2 + pi() / 4));
		$q = $dLat / $dPhi;

		if (!is_finite($q)) {
			$q = cos(deg2rad($this->getLatitude()));
		}
		// if $dLon over 180° take shorter rhumb across 180° meridian:
		if ($dLon > pi()) {
			$dLon = 2 * pi() - $dLon;
		}
		$d = sqrt($dLat * $dLat + $q * $q * $dLon * $dLon);
		return $d * $earthRadius;
	}

	/*
	|--------------------------------------------------------------------------
	| HELPER METHODS
	|--------------------------------------------------------------------------
	*/

	public function equals($geoLocation) {
		if ($this == $geoLocation) {
			return true;
		}
		if (!($object instanceof GeoLocation)) {
			return false;
		}

		return $this->latitude == $geoLocation->latitude
				&& $this->longitude == $geoLocation->longitude
				&& $this->elevation == $geoLocation->elevation
				&& $this->locationName == $geoLocation->locationName
				&& $this->timeZone == $geoLocation->timeZone;
	}

	/*
	|--------------------------------------------------------------------------
	| CLONEABLE
	|--------------------------------------------------------------------------
	*/

	public function copy() {
		return clone $this;
	}
}