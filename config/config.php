<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   JalaliDate
 * @author    Hamid Abbaszadeh
 * @license   LGPL-3.0+
 * @copyright 2014-2016 Respinar
 */


/**
 * Hooks
 */
if (TL_MODE == 'FE') {
	$GLOBALS['TL_HOOKS']['parseDate'][] = array('JalaliDate', 'parse');
}
