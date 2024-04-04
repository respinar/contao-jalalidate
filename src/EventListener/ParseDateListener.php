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
        // If no format is provided, return the original formatted date
        if ($format === null) {
            return $formattedDate;
        }

        $container = System::getContainer();
        $requestStack = $container->get('request_stack');
        $scopeMatcher = $container->get('contao.routing.scope_matcher');

        $request = $requestStack->getCurrentRequest();
        if ($request && $scopeMatcher->isBackendRequest($request)) {
            return $formattedDate;
        }

        // Use $GLOBALS['objPage'] to reliably get the current page model
        $page = $GLOBALS['objPage'] ?? null;
        if (!$page instanceof PageModel) {
            return $formattedDate;
        }

        // Get the root page
        $rootPage = PageModel::findById($page->rootId);

        // Check if Iranian date is enabled in the root page settings
        if (!$rootPage || !$rootPage->useIranianDate) {
            return $formattedDate;
        }        

        return jDateTime::date($format, $timestamp ?? time());
    }
}