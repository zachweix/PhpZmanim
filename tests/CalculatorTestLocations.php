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

/**
 * Shared ['lat', 'lon', 'elev'] fixtures used across the calculator tests. The
 * calculator suites all exercise the same set of locations, so they live here
 * once rather than being repeated inline in every data provider.
 */
trait CalculatorTestLocations
{
	const NJ = ['lat' => 41.1181036, 'lon' => -74.0840691, 'elev' => 167];
	const LA = ['lat' => 34.0201613, 'lon' => -118.6919095, 'elev' => 71];
	const JERUSALEM = ['lat' => 31.7962994, 'lon' => 35.1053185, 'elev' => 754];
	const NORWAY = ['lat' => 70.1498248, 'lon' => 9.1456867, 'elev' => 0];
	const SYDNEY = ['lat' => -33.8688, 'lon' => 151.2093, 'elev' => 58];
	const MACAPA = ['lat' => 0.0349, 'lon' => -51.0694, 'elev' => 15];
	const SUVA = ['lat' => -18.1416, 'lon' => 178.4419, 'elev' => 6];
	const USHUAIA = ['lat' => -54.8019, 'lon' => -68.3030, 'elev' => 23];
}
