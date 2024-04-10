<?php

declare(strict_types=1);

/*
 * This file is part of Contao Jalali Date Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ContaoJalaliDateBundle\Tests;

use Contao\ContaoJalaliDateBundle\RespinarJalaliDateBundle;
use PHPUnit\Framework\TestCase;

class RespinarJalaliDateBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new RespinarJalaliDateBundle();

        $this->assertInstanceOf('Respinar\ContaoJalaliDateBundle\RespinarJalaliDateBundle', $bundle);
    }
}