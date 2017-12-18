<?php
namespace B13\SeoBasics\Module;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;

class BackendSeoModuleBootstrap
{
    /**
     * Dummy method, called by SCbase external object handling
     */
    public function init()
    {
    }

    /**
     * Dummy method, called by SCbase external object handling
     */
    public function checkExtObj()
    {
    }

    /**
     * Bootstrap extbase and jump to SEO administration controller
     *
     * @return string
     */
    public function main()
    {
        $configuration = [
            'extensionName' => 'SeoBasics',
            'pluginName' => 'SeoAdministration',
            'vendorName' => 'B13',
        ];

        // NOTE: Based on EXT:belog

        // Yeah, this is ugly. But currently, there is no other direct way
        // in extbase to force a specific controller in backend mode.
        // Overwriting $_GET was the most simple solution here until extbase
        // provides a clean way to solve this.
        $_GET['tx_seobasics_seoadministration']['controller'] = 'Administration';

        /** @var $extbaseBootstrap \TYPO3\CMS\Extbase\Core\Bootstrap */
        $extbaseBootstrap = GeneralUtility::makeInstance(Bootstrap::class);

        return $extbaseBootstrap->run('', $configuration);
    }
}
