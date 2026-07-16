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

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PhpZmanim\Calculator\AstronomicalCalculator;
use PhpZmanim\Calculator\NoaaCalculator;
use PhpZmanim\Calculator\MeeusCalculator;
use PhpZmanim\Calculator\SunTimesCalculator;
use PhpZmanim\Calculator\SPACalculator;

class AstronomicalCalculatorTest extends TestCase
{
	/*
	|--------------------------------------------------------------------------
	| DATA PROVIDERS
	|--------------------------------------------------------------------------
	| Every concrete calculator inherits create()/getDefault() from the abstract
	| base, so create() must resolve to the right subclass via new static().
	*/

	public static function calculatorProvider(): array
	{
		return [
			'Noaa'     => [NoaaCalculator::class],
			'Meeus'    => [MeeusCalculator::class],
			'SunTimes' => [SunTimesCalculator::class],
			'SPA'      => [SPACalculator::class],
		];
	}

	/*
	|--------------------------------------------------------------------------
	| CREATE / GET DEFAULT
	|--------------------------------------------------------------------------
	*/

	#[Test]
	#[DataProvider('calculatorProvider')]
	public function createFactoryMatchesConstructor(string $class): void
	{
		$created = $class::create();
		$constructed = new $class();

		$this->assertInstanceOf($class, $created);
		$this->assertNotSame($constructed, $created);
		$this->assertEquals($constructed, $created);
	}

	#[Test]
	public function getDefaultReturnsNoaaCalculator(): void
	{
		$default = AstronomicalCalculator::getDefault();

		$this->assertInstanceOf(NoaaCalculator::class, $default);
		$this->assertEquals(NoaaCalculator::create(), $default);
	}

	/*
	|--------------------------------------------------------------------------
	| CLONEABLE
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function copyReturnsIndependentClone(): void
	{
		$calculator = NoaaCalculator::create()->setRefraction(0.5);
		$copy = $calculator->copy();

		$this->assertNotSame($calculator, $copy);
		$this->assertEquals($copy->getRefraction(), 0.5);

		$copy->setRefraction(0.9);
		$this->assertEquals($calculator->getRefraction(), 0.5);
		$this->assertEquals($copy->getRefraction(), 0.9);
	}
}
