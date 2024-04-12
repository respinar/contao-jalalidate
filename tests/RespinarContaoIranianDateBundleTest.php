<?php

declare(strict_types=1);

/*
 * This file is part of Contao Jalali Date Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ContaoIranianDateBundle\Tests;

use Contao\ContaoIranianDateBundle\RespinarContaoIranianDateBundle;
use PHPUnit\Framework\TestCase;

class RespinarContaoIranianDateBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new RespinarContaoIranianDateBundle();

        $this->assertInstanceOf('Respinar\ContaoIranianDateBundle\RespinarContaoIranianDateBundle', $bundle);
    }
}