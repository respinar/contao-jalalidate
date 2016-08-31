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
 * Register namespace if loaded from extension repository
 */
if (class_exists('NamespaceClassLoader')) {
    NamespaceClassLoader::add('JalaliDate', 'system/modules/jalalidate/library');
}