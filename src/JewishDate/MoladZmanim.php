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

namespace PhpZmanim\JewishDate;

use Carbon\Carbon;
use PhpZmanim\GeoLocation;

trait MoladZmanim
{
	// The following are from JewishCalendar

	public function getMoladAsCarbon(): Carbon
	{
		$molad = $this->getMolad();

		$locationName = "Jerusalem, Israel";
		$latitude = 31.778;
		$longitude = 35.2354;
		$yerushalayimStandardTZ = "GMT+2";

		$geo = new GeoLocation($latitude, $longitude, 0.0, $yerushalayimStandardTZ, $locationName);

		$moladSeconds = $molad->getMoladChalakim() * 10 / 3;

		$cal = Carbon::create(
			$molad->getGregorianYear(), $molad->getGregorianMonth(), $molad->getGregorianDayOfMonth(),
			$molad->getMoladHours(), $molad->getMoladMinutes(), (int) $moladSeconds,
			$yerushalayimStandardTZ
		);
		$cal->milliseconds = (int) (($moladSeconds - (int) $moladSeconds) * 1000);

		$cal->add(-1 * (int) $geo->getLocalMeanTimeOffset($cal), "milliseconds");

		return $cal;
	}

	public function getTchilasZmanKidushLevana3Days(): Carbon
	{
		return $this->getMoladAsCarbon()->addHours(72);
	}

	public function getTchilasZmanKidushLevana7Days(): Carbon
	{
		return $this->getMoladAsCarbon()->addHours(168);
	}

	public function getSofZmanKidushLevanaBetweenMoldos(): Carbon
	{
		return $this->getMoladAsCarbon()
			->addHours((24 * 14) + 18)
			->addMinutes(22)
			->addSeconds(1)
			->add(666, "milliseconds");
	}

	public function getSofZmanKidushLevana15Days(): Carbon
	{
		return $this->getMoladAsCarbon()->addHours(24 * 15);
	}
}
