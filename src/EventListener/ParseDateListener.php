<?php
// src/EventListener/ParseDateListener.php
namespace Respinar\JalaliDateBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;   
use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Respinar\JalaliDateBundle\Helper\jDateTime;

/**
 * @Hook("parseDate")
 */
#[AsHook('parseDate')]
class ParseDateListener
{
    public function __construct()
    {
    }

    public function __invoke(string $formattedDate, string $format, ?int $timestamp): string
    {
        //$request = $this->requestStack->getCurrentRequest();

       // \var_dump($request);

        // if (!$request || !$this->scopeMatcher->isBackendRequest($request)) {
        //     return;
        // }

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