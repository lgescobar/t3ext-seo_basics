<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

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

call_user_func(function ($extKey, $table) {
    $lll = 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:';

    // Adding title tag field to pages TCA
    $additionalColumns = [
        'tx_seo_titletag' => [
            'exclude' => true,
            'label' => $lll . 'pages.titletag',
            'config' => [
                'type' => 'input',
                'size' => 70,
                'eval' => 'trim',
            ],
        ],
        'tx_seo_canonicaltag' => [
            'exclude' => true,
            'label' => $lll . 'pages.canonicaltag',
            'config' => [
                'type' => 'input',
                'size' => 70,
                'eval' => 'trim',
            ],
        ],
        'tx_seo_robots' => [
            'exclude' => true,
            'label' => $lll . 'pages.tx_seo_robots',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'minitems' => 1,
                'maxitems' => 1,
                'size' => 1,
                'items' => [
                    [$lll . 'pages.tx_seo_robots.I.0', '0'],
                    [$lll . 'pages.tx_seo_robots.I.1', '1'],
                    [$lll . 'pages.tx_seo_robots.I.2', '2'],
                    [$lll . 'pages.tx_seo_robots.I.3', '3'],
                ],
            ],
        ],
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $additionalColumns);

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        $table,
        'metatags',
        'tx_seo_titletag, --linebreak--',
        'before:keywords'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        $table,
        'metatags',
        '--linebreak--, tx_seo_canonicaltag, --linebreak--, tx_seo_robots',
        'after:description'
    );

    $GLOBALS['TCA'][$table]['interface']['showRecordFieldList'] .= ',tx_seo_titletag, tx_seo_canonicaltag, tx_seo_robots';
}, 'seo_basics', 'pages_language_overlay');
