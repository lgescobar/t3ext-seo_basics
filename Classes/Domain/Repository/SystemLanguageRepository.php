<?php
namespace B13\SeoBasics\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Kai Ole Hartwig <o.hartwig@ko-web.net>
 *
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

use B13\SeoBasics\Domain\Model\SystemLanguage;
use TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * The SystemLanguage repository
 */
class SystemLanguageRepository extends Repository
{
    /**
     * @var \TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider
     */
    protected $translationConfigurationProvider;

    /**
     * @param \TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider $translationConfigurationProvider
     */
    public function injectTranslationConfigurationProvider(
        TranslationConfigurationProvider $translationConfigurationProvider
    ) {
        $this->translationConfigurationProvider = $translationConfigurationProvider;
    }

    /**
     * Fetches all system languages and adds the "all languages" pseudo-record and the default language.
     *
     * @param int $pageId Page id to get the defined default language name and its flag
     * @return array
     */
    public function findAll($pageId = 0)
    {
        $modSharedTSconfig = BackendUtility::getModTSconfig($pageId, 'mod.SHARED');

        $systemLanguages = [
            -1 => $this->getWildcardLanguage(),
            0 => $this->getDefaultLanguage($modSharedTSconfig),
        ];

        /** @var \B13\SeoBasics\Domain\Model\SystemLanguage $systemLanguage */
        foreach ($this->createQuery()->execute() as $systemLanguage) {
            $systemLanguages[$systemLanguage->getUid()] = $systemLanguage;
        }

        return $systemLanguages;
    }

    /**
     * Constructs the default language
     *
     * @param array $modSharedTSconfig
     * @return \B13\SeoBasics\Domain\Model\SystemLanguage
     */
    protected function getDefaultLanguage(array $modSharedTSconfig)
    {
        $defaultSystemLanguage = new SystemLanguage();
        $defaultSystemLanguage->_setProperty('uid', 0);
        $defaultSystemLanguage->setTitle(
            $this->getDefaultLanguageLabel($modSharedTSconfig)
        );

        $defaultLanguageFlag = $this->getDefaultLanguageFlag($modSharedTSconfig);
        if (strpos($defaultLanguageFlag, 'flags-') === 0) {
            $defaultLanguageFlag = substr($defaultLanguageFlag, strlen('flags-'));
        }

        $defaultSystemLanguage->setFlag($defaultLanguageFlag);

        return $defaultSystemLanguage;
    }

    /**
     * Constructs the "all languages" pseudo-record
     *
     * @return \B13\SeoBasics\Domain\Model\SystemLanguage
     */
    protected function getWildcardLanguage()
    {
        $multipleLanguagesLabelKey =
            'LLL:EXT:lang/Resources/Private/Language/locallang_mod_web_list.xlf:multipleLanguages';

        $defaultSystemLanguage = new SystemLanguage();
        $defaultSystemLanguage->_setProperty('uid', -1);
        $defaultSystemLanguage->setTitle(
            LocalizationUtility::translate($multipleLanguagesLabelKey)
        );
        $defaultSystemLanguage->setFlag('multiple');

        return $defaultSystemLanguage;
    }

    /**
     * Get the defined flag for default language in TypoScript configuration
     *
     * @param array $modSharedTSconfig
     * @return string
     */
    protected function getDefaultLanguageFlag(array $modSharedTSconfig)
    {
        if (strlen($modSharedTSconfig['properties']['defaultLanguageFlag'])) {
            $defaultLanguageFlag = 'flags-' . $modSharedTSconfig['properties']['defaultLanguageFlag'];
        } else {
            $defaultLanguageFlag = 'empty-empty';
        }
        return $defaultLanguageFlag;
    }

    /**
     * Get the defined label for default language in TypoScript configuration
     *
     * @param array $modSharedTSconfig
     * @return string
     */
    protected function getDefaultLanguageLabel(array $modSharedTSconfig)
    {
        $defaultLanguageLabelKey = 'LLL:EXT:lang/Resources/Private/Language/locallang_mod_web_list.xlf:defaultLanguage';

        $defaultLanguageLabel = LocalizationUtility::translate($defaultLanguageLabelKey);

        if (strlen($modSharedTSconfig['properties']['defaultLanguageLabel'])) {
            $defaultLanguageLabel = $modSharedTSconfig['properties']['defaultLanguageLabel']
                . ' (' . $defaultLanguageLabel . ')';
        }

        return $defaultLanguageLabel;
    }
}
