<?php

declare(strict_types=1);

/*
 * This file is part of Contao Jalali Date Bundle.
 *
 * (c) Hamid Peywasti [abbaszadeh.h@gmail.com]
 *
 * @license MIT
 */

namespace Respinar\JalaliDateBundle\Tests;

use Contao\JalaliDateBundle\RespinarJalaliDateBundle;
use PHPUnit\Framework\TestCase;

class RespinarJalaliDateBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new RespinarJalaliDateBundle();

        $this->assertInstanceOf('Respinar\JalaliDateBundle\RespinarJalaliDateBundle', $bundle);
    }
}