<?php

/**
 * Jalali date for Contao Open Source CMS
 *
 * Copyright (C) 2014-2017 Respinar
 *
 * @package    JalaliDate
 * @link       https://github.com/respinar/contao-jalalidate/
 * @license    https://opensource.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Namespace
 */
namespace Respinar\JalaliDate;

use jDateTime;

class JalaliDate
{
    /**
	 * Parse a date format string and translate textual representations
	 *
	 * @param string  $strFormat The date format string
	 * @param integer $intTstamp An optional timestamp
	 *
	 * @return string The textual representation of the date
	 */
	public static function parse($strReturn,$strFormat, $intTstamp=null)
	{

		if ($GLOBALS['TL_LANGUAGE'] == 'fa') {
			if ($intTstamp === null)
			{
				$strDate = jDateTime::date($strFormat);
			}
			else
			{
				$strDate = jDateTime::date($strFormat, $intTstamp);
			}
		} else {
			$strDate = $strReturn;
		}

		return $strDate;
	}

}
