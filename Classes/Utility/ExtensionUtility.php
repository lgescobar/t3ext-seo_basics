<?php
namespace B13\SeoBasics\Utility;

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

class ExtensionUtility extends \TYPO3\CMS\Extbase\Utility\ExtensionUtility
{
    /**
     * Add auto-generated TypoScript to configure the Extbase Dispatcher (module counterpart to plugin configuration)
     * FOR USE IN ext_tables.php FILES
     *
     * @see \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin()
     *
     * @param string $extensionName The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
     * @param string $moduleName must be a unique id for your module in UpperCamelCase (the string length of the
     *     extension key added to the length of the plugin name should be less than 32!)
     * @param array $controllerActions is an array of allowed combinations of controller and action stored in an array
     *     (controller name as key and a comma separated list of action names as value, the first controller and its
     *     first action is chosen as default)
     *
     * @throws \InvalidArgumentException
     */
    public static function configureModule($extensionName, $moduleName, array $controllerActions)
    {
        self::checkExtensionNameFormat($extensionName);

        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$extensionName]['modules'][$moduleName])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$extensionName]['modules'][$moduleName] = [];
        }

        foreach ($controllerActions as $controllerName => $actions) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$extensionName]['modules'][$moduleName]['controllers'][$controllerName] = [
                'actions' => GeneralUtility::trimExplode(',', $actions),
            ];
        }
    }
}
