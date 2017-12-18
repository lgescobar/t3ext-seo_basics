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

// adding th tx_seo_titletag to the pageOverlayFields so it is recognized when fetching the overlay fields
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageOverlayFields'] .= ',tx_seo_titletag,tx_seo_canonicaltag,tx_seo_robots';

call_user_func(function () {

    /** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
    $configurationUtility = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility::class
    );

    $extensionConfiguration = $configurationUtility->getCurrentConfiguration('seo_basics');

    /*
     * registering sitemap.xml for each hierachy of configuration to realurl (meaning to every website
     * in a multi-site installation)
     */
    if ($extensionConfiguration['xmlSitemap'] == '1') {
        $realUrlConf = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'];
        $hooks = ['encodeSpURL_postProc', 'decodeSpURL_preProc', 'getHost'];

        if (is_array($realUrlConf)) {
            foreach ($realUrlConf as $host => $cnf) {
                // we won't do anything with string pointer (e.g. example.org => www.example.org)
                // also ignore realurl hooks
                if (!is_array($realUrlConf[$host]) || in_array($host, $hooks, true)) {
                    continue;
                }

                if (!isset($realUrlConf[$host]['fileName'])) {
                    $realUrlConf[$host]['fileName'] = [];
                }

                $realUrlConf[$host]['fileName']['index']['sitemap.xml']['keyValues']['type'] = 776;
            }

            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'] = $realUrlConf;
        }
    }
});
