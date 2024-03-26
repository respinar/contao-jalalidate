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
use Contao\PageModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Respinar\JalaliDateBundle\Helper\jDateTime;

#[AsHook('parseDate')]
class ParseDateListener
{
    public function __invoke(string $formattedDate, ?string $format, ?int $timestamp): string
    {
        $requestStack = System::getContainer()->get('request_stack');
        $scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');

        $request = $requestStack->getCurrentRequest();
        $isBackend = $request && $scopeMatcher->isBackendRequest($request);

        if ($isBackend) {
            return $formattedDate;
        }

        // Get the current page and its root page
        $page = $request ? $request->attributes->get('pageModel') : null;
        if ($page instanceof PageModel) {
            $rootPage = PageModel::findById($page->rootId);
        } else {
            $rootPage = null;
        }

        // Check if Jalali date is enabled in the root page settings
        if (!$rootPage || !$rootPage->useIranianDate) {
            return $formattedDate;
        }

        // If no format is provided, return the original formatted date
        if ($format === null) {
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