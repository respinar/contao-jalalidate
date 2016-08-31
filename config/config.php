<?php

/**
 * Jalli date for Contao Open Source CMS
 *
 * Copyright (C) 2014-2016 Respinar
 *
 * @package    JalaliDate
 * @link       http://github.com/respinar/contao-jalalidate/
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Hooks
 */
if (TL_MODE == 'FE') {
	$GLOBALS['TL_HOOKS']['parseDate'][] = array('JalaliDate\jDateTime', 'parse');
}
