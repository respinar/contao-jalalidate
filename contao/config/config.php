<?php

/*
 * This file is part of Contao Jalali Date Bundle.
 *
 * (c) Hamid Peywasti 2023 <hamid@respinar.com>
 *
 * @license MIT
 */

use Respinar\JalaliDateBundle\EventListener\ParseDateListener;

$GLOBALS['TL_HOOKS']['parseDate'][] = [ParseDateListener::class, '__invoke'];