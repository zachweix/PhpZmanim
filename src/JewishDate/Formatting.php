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

use InvalidArgumentException;
use PhpZmanim\JewishDate\Formatter\JewishDateFormatter;
use PhpZmanim\JewishDate\Formatter\LanguageFormatter;

trait Formatting
{
	public function format(?string $language = null, array $options = []): JewishDateFormatter|LanguageFormatter
	{
		if (!is_null($language)) {
			if (!is_subclass_of($language, LanguageFormatter::class)) {
				throw new InvalidArgumentException(sprintf(
					'The language must be a %s subclass, e.g. Hebrew::class; got "%s".',
					LanguageFormatter::class,
					$language
				));
			}

			return new $language($this, $options);
		}

		return new JewishDateFormatter($this);
	}
}
