<?php
namespace B13\SeoBasics\Controller;

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

use B13\SeoBasics\Domain\Model\Constraint;
use B13\SeoBasics\Domain\Repository\SystemLanguageRepository;
use B13\SeoBasics\Service\PageTreeService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class AdministrationController extends ActionController
{
    /**
     * Page ID in page context
     *
     * @var int
     */
    protected $pageId = 0;

    /**
     * @var \B13\SeoBasics\Domain\Repository\SystemLanguageRepository
     */
    protected $systemLanguageRepository;

    /**
     * @var \B13\SeoBasics\Service\PageTreeService
     */
    protected $pageTreeService;

    /**
     * @param \B13\SeoBasics\Domain\Repository\SystemLanguageRepository $systemLanguageRepository
     */
    public function injectSystemLanguageRepository(SystemLanguageRepository $systemLanguageRepository)
    {
        $this->systemLanguageRepository = $systemLanguageRepository;
    }

    /**
     * @param \B13\SeoBasics\Service\PageTreeService $pageTreeService
     */
    public function injectPageTreeService(PageTreeService $pageTreeService)
    {
        $this->pageTreeService = $pageTreeService;
    }

    /**
     * Set page id before all actions
     */
    public function initializeAction()
    {
        $this->pageId = (int)GeneralUtility::_GP('id');
    }

    /**
     * @todo Show general information and the installed modules
     *
     * @param \B13\SeoBasics\Domain\Model\Constraint $constraint
     */
    public function indexAction(Constraint $constraint = null)
    {
        $this->handleConstraintObject($constraint);

        $systemLanguages = $this->systemLanguageRepository->findAll($constraint->getPageId());

        $this->view
            ->assign('pageDepths', $this->createPageDepthOptions())
            ->assign('systemLanguages', $systemLanguages)
            ->assign('constraint', $constraint)
            ->assign('pages', $this->pageTreeService->getPageTreeForConstraint(
                $constraint,
                $systemLanguages,
                !($this->settings['showTreeIcons'] ?? false) && ($this->settings['showPageIcon'] ?? true)
            ));
    }

    /**
     * @todo Show
     *
     * @param \B13\SeoBasics\Domain\Model\Constraint $constraint
     */
    public function editAllAction(Constraint $constraint)
    {
        $systemLanguages = $this->systemLanguageRepository->findAll($constraint->getPageId());

        $this->view
            ->assign('pageDepths', $this->createPageDepthOptions())
            ->assign('systemLanguages', $systemLanguages)
            ->assign('constraint', $constraint)
            ->assign('pages', $this->pageTreeService->getPageTreeForConstraint(
                $constraint,
                $systemLanguages,
                !($this->settings['showTreeIcons'] ?? false) && ($this->settings['showPageIcon'] ?? true)
            ))
            ->assign(
                'formToken',
                FormProtectionFactory::get()->generateToken('SEO fields', 'edit', $constraint->getPageId())
            );
    }

    /**
     * @todo
     *
     * @param \B13\SeoBasics\Domain\Model\Constraint $constraint
     * @param array $data
     */
    public function saveAllAction(Constraint $constraint, array $data)
    {
        if (FormProtectionFactory::get()->validateToken(
            GeneralUtility::_POST('formToken'),
            'SEO fields',
            'edit',
            $constraint->getPageId()
        )) {
            /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler */
            $dataHandler = $this->objectManager->get(DataHandler::class);
            $dataHandler->stripslashes_values = 0;

            $dataHandler->start($data, []);
            $dataHandler->process_datamap();
        }

        $this->redirect('index');
    }

    /**
     * @todo
     *
     * @param \B13\SeoBasics\Domain\Model\Constraint|null $constraint
     */
    protected function handleConstraintObject(&$constraint)
    {
        // Constraint object handling:
        // If there is none from GET, try to get it from BE user data, else create new
        if (is_null($constraint)) {
            $constraint = $this->getConstraintFromBeUserData();
            if (is_null($constraint)) {
                $constraint = new Constraint();
            }
        } else {
            $this->persistConstraintInBeUserData($constraint);
        }

        $constraint->setPageId($this->pageId);
    }

    /**
     * Get module state (the constraint object) from user data
     *
     * @return \B13\SeoBasics\Domain\Model\Constraint
     */
    protected function getConstraintFromBeUserData()
    {
        $moduleData = BackendUtility::getModuleData([], [], 'web_info');

        $constraint = new Constraint();

        $constraint->setDepth($moduleData['depth']);
        $constraint->setLang($moduleData['lang']);
        $constraint->setHideShortcuts($moduleData['hideShortcuts']);
        $constraint->setHideDisabled($moduleData['hideDisabled']);
        $constraint->setHideSysFolders($moduleData['hideSysFolders']);
        $constraint->setHideNotInMenu($moduleData['hideNotInMenu']);
        $constraint->setHideExpired($moduleData['hideExpired']);

        return $constraint;
    }

    /**
     * Save current constraint object in be user settings (uC)
     *
     * @param \B13\SeoBasics\Domain\Model\Constraint $constraint
     */
    protected function persistConstraintInBeUserData(Constraint $constraint)
    {
        $changedSettings = [
            'depth' => (string)$constraint->getdepth(),
            'lang' => (string)$constraint->getLang(),
            'hideShortcuts' => (string)$constraint->getHideShortcuts(),
            'hideDisabled' => (string)$constraint->getHideDisabled(),
            'hideSysFolders' => (string)$constraint->getHideSysFolders(),
            'hideNotInMenu' => (string)$constraint->getHideNotInMenu(),
            'hideExpired' => (string)$constraint->getHideExpired(),
        ];

        $modMenu = array_fill_keys(array_keys($changedSettings), '');

        BackendUtility::getModuleData($modMenu, $changedSettings, 'web_info');
    }

    /**
     * Create options for the 'depth of page levels' selector.
     *
     * @return array Key is depth identifier (1 = One level), value the localized select option label
     */
    protected function createPageDepthOptions()
    {
        return [
            0 => LocalizationUtility::translate('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.depth_0'),
            1 => LocalizationUtility::translate('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.depth_1'),
            2 => LocalizationUtility::translate('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.depth_2'),
            3 => LocalizationUtility::translate('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.depth_3'),
            4 => LocalizationUtility::translate('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.depth_4'),
            999 => LocalizationUtility::translate('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.depth_infi'),
        ];
    }
}
