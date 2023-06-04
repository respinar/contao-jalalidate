<?php
// src/EventListener/ParseDateListener.php
namespace Respinar\ContaoJalalidate\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Respinar\ContaoJalalidate\Helper\jDateTime;

/**
 * @Hook("parseDate")
 */
class ParseDateListener
{
    public function __invoke(string $formattedDate, string $format, ?int $timestamp): string
    {
        if ($GLOBALS['TL_LANGUAGE'] !== 'fa') {
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