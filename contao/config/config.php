<?php

/*
 * This file is part of Contao Jalali Date Bundle.
 *
 * (c) Hamid Peywasti 2023 <abbaszadeh.h@gmail.com>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/respinar/contao-jalalidate
 */

use Respinar\JalaliDateBundle\EventListener\ParseDateListener;

$GLOBALS['TL_HOOKS']['parseDate'][] = [ParseDateListener::class, '__invoke'];