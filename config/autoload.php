<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Persiandate
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes

	'Contao\Date'        => 'system/modules/persiandate/classes/MyDate.php',
	//'Contao\PersianDate' => 'system/modules/persiandate/classes/PersianDate.php',

));
