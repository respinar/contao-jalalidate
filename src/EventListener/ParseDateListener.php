<?php

declare(strict_types=1);

/*
 * This file is part of Contao Jalali Date Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\JalaliDateBundle\EventListener;

use Contao\System;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Respinar\JalaliDateBundle\Helper\jDateTime;

#[AsHook('parseDate')]
class ParseDateListener
{
    public function __invoke(string $formattedDate, ?string $format, ?int $timestamp): string
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();
		$isBackend = $request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request);

		if ($isBackend)
		{
			return $formattedDate;
		}

        if (!\in_array($GLOBALS['TL_LANGUAGE'], array('fa','fa-IR'), true)) {
            return $formattedDate;
        }

        if ($timestamp === null) {
            $strDate = jDateTime::date($format);
        } else {
            $strDate = jDateTime::date($format, $timestamp);
        }

		return $strDate;
    }
}