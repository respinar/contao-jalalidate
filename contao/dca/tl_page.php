<?php

declare(strict_types=1);

/*
 * This file is part of Contao Jalali Date Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// Add the 'useShamsiDate' field to the 'root' and 'rootfallback' palettes after 'global_legend'
PaletteManipulator::create()
    ->addLegend('shamsi_legend', 'global_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('useShamsiDate', 'shamsi_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page')
    ->applyToPalette('rootfallback', 'tl_page');

// Define the field
$GLOBALS['TL_DCA']['tl_page']['fields']['useShamsiDate'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['useShamsiDate'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50 clr'],
    'sql'       => "char(1) NOT NULL default ''"
];