<?php
// src/EventListener/ParseDateListener.php
namespace Respinar\JalaliDateBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;   
use Respinar\JalaliDateBundle\Helper\jDateTime;

#[AsHook('parseDate')]
class ParseDateListener
{
    public function __construct()
    {
    }

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