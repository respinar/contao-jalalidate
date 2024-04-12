<?php

declare(strict_types=1);

/*
 * This file is part of Contao Jalali Date Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ContaoIranianDateBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Respinar\ContaoIranianDateBundle\Helper\jDateTime;

#[AsHook('replaceInsertTags')]
class InsertTagListener
{
    public function __invoke(string $tag): string|false
    {
        // Split the tag into parts (e.g., "iranian_date::j F Y" for custom format)
        $parts = explode('::', $tag);

        // Check if the tag is "iranian_date"
        if ($parts[0] !== 'iranian_date') {
            return false; // Let Contao handle other tags
        }

        // Get the format: If not provided, use global settings
        $format = $parts[1] ?? $GLOBALS['objPage']->dateFormat ?? $GLOBALS['TL_CONFIG']['dateFormat'] ?? '';

        // Get the timestamp, if provided
        $timestamp = $parts[2] ?? time();

        // Convert timestamp from string to integer (if it's a number)
        if (is_numeric($timestamp)) {
            $timestamp = (int) $timestamp;
        }

        return jDateTime::date($format, $timestamp);
    }
}
