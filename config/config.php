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
 * Hooks
 */
if (TL_MODE == 'FE') {
	$GLOBALS['TL_HOOKS']['parseDate'][] = array('Respinar\JalaliDate\JalaliDate', 'parse');
}
