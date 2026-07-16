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

namespace PhpZmanim\Torah;

/**
 * An immutable page (daf) in a Daf Yomi cycle: a masechta (tractate) and a daf number.
 * The masechta is a Nameable enum (MasechtaBavli or MasechtaYerushalmi), so the daf carries
 * its own typed tractate rather than a bare index into a list.
 */
class Daf
{
	public function __construct(
		private readonly Nameable $masechta,
		private readonly int $daf
	) {}

	public function getMasechta(): Nameable
	{
		return $this->masechta;
	}

	public function getDaf(): int
	{
		return $this->daf;
	}
}
