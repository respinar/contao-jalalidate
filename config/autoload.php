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
 * Register PSR-0 namespaces
 */
if (class_exists('NamespaceClassLoader')) {
    NamespaceClassLoader::add('Respinar\JalaliDate', 'system/modules/jalali/library');
}