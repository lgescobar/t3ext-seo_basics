<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 KO-Web | Kai Ole Hartwig <mail@ko-web.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Basic SEO Features',
    'description' => 'Adds a separate field for the title-tag per page, easy and SEO-friendly keywords and '
        . 'description editing in a new module as well as a flexible Google Sitemap (XML).',
    'category' => 'fe',

    'author' => 'Benni Mack',
    'author_email' => 'benni@typo3.org',

    'state' => 'stable',
    'version' => '1.0.0',

    'uploadfolder' => false,
    'clearcacheonload' => true,
    'createDirs' => '',

    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'realurl' => '0.0.0-0.0.0',
            'cooluri' => '0.0.0-0.0.0',
        ],
    ],
];
