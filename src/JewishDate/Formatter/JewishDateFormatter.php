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

namespace PhpZmanim\JewishDate\Formatter;

use PhpZmanim\JewishDate;

/**
 * The entry point for formatting a JewishDate. Holds the date immutably and hands
 * off to an immutable, language-specific formatter:
 *
 *     $date->format()->hebrew()->date();     // כ״ה טבת תשע״א
 *     $date->format()->english()->yomTov();  // Pesach
 *
 * kviah() lives only on Hebrew, so it is reachable only via hebrew().
 */
class JewishDateFormatter
{
	public function __construct(
		private readonly JewishDate $date
	) {}

	public function hebrew(array $options = []): Hebrew
	{
		return new Hebrew($this->date, $options);
	}

	public function english(array $options = []): English
	{
		return new English($this->date, $options);
	}
}
