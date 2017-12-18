<?php
namespace B13\SeoBasics\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 KO-Web | Kai Ole Hartwig <mail@ko-web.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
use TYPO3\CMS\Backend\Tree\View\PageTreeView;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

class PageTreeService
{
    /**
     * @var \TYPO3\CMS\Core\Imaging\IconFactory
     */
    protected $iconFactory;

    /**
     * @param \TYPO3\CMS\Core\Imaging\IconFactory $iconFactory
     */
    public function injectIconFactory(IconFactory $iconFactory)
    {
        $this->iconFactory = $iconFactory;
    }

    /**
     * @return \TYPO3\CMS\Core\Imaging\IconFactory
     */
    public function getIconFactory()
    {
        if (!isset($this->iconFactory) || is_null($this->iconFactory)) {
            $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        }

        return $this->iconFactory;
    }

    /**
     * @todo Finds all log entries that match all given constraints.
     *
     * @param \B13\SeoBasics\Domain\Model\Constraint $constraint
     * @param array $sysLanguages
     * @param bool $generateSingleIcons
     * @return array
     */
    public function getPageTreeForConstraint(Constraint $constraint, array $sysLanguages, $generateSingleIcons = false)
    {
        if (!is_numeric($constraint->getPageId()) || $constraint->getPageId() < 0) {
            return [];
        }

        $row = BackendUtility::getRecordWSOL('pages', $constraint->getPageId());

        if (!is_array($row)) {
            return [];
        }

        /** @var $pageTree \TYPO3\CMS\Backend\Tree\View\PageTreeView */
        $pageTree = GeneralUtility::makeInstance(PageTreeView::class);
        $pageTree->init('AND ' . $GLOBALS['BE_USER']->getPagePermsClause(1));

        $pageTree->tree[] = [
            'row' => $row,
            'HTML' => is_array($row) ? $this->getIconFactory()->getIconForRecord(
                'pages',
                $row,
                Icon::SIZE_SMALL
            )->render() : '',
        ];

        // Check if we should get a whole tree of pages and not only a single page
        if ($constraint->getDepth() > 0) {
            $pageTree->addField('tx_seo_titletag');
            $pageTree->addField('keywords');
            $pageTree->addField('description');

            $pageTree->getTree($constraint->getPageId(), $constraint->getDepth());
        }

        // For later work (add URL path and translations if needed)
        $indexArray = [];
        $filteredIds = [];

        foreach ($pageTree->tree as $index => $treeNode) {
            $row = $treeNode['row'];
            $hiddenByConstraints = false;

            // filter checkbox selections
            if ($constraint->getHideDisabled() && $row['hidden'] == 1) {
                $hiddenByConstraints = true;
            } elseif ($constraint->getHideShortcuts() && (
                $row['doktype'] == PageRepository::DOKTYPE_LINK
                || $row['doktype'] == PageRepository::DOKTYPE_SHORTCUT
                || $row['doktype'] == PageRepository::DOKTYPE_SPACER
            )) {
                $hiddenByConstraints = true;
            } elseif ($constraint->getHideNotInMenu() && $row['nav_hide'] == 1) {
                $hiddenByConstraints = true;
            } elseif ($constraint->getHideSysFolders() &&
                $row['doktype'] == PageRepository::DOKTYPE_SYSFOLDER
            ) {
                $hiddenByConstraints = true;
            } elseif ($constraint->getHideExpired()) {
                $startTimeFieldName = $GLOBALS['TCA']['pages']['ctrl']['enablecolumns']['starttime'] ?? null;
                $endTimeFieldName = $GLOBALS['TCA']['pages']['ctrl']['enablecolumns']['endtime'] ?? null;

                if (!empty($startTimeFieldName) && (int)$row[$startTimeFieldName] > $GLOBALS['SIM_ACCESS_TIME']) {
                    $hiddenByConstraints = true;
                } elseif (!empty($endTimeFieldName) && (int)$row[$endTimeFieldName] !== 0
                    && (int)$row[$endTimeFieldName] <= $GLOBALS['SIM_ACCESS_TIME']
                ) {
                    $hiddenByConstraints = true;
                }
            }

            if (!$hiddenByConstraints) {
                // Save index for later work
                $indexArray[$treeNode['row']['uid']] = $index;
                $filteredIds[] = $treeNode['row']['uid'];

                if ($generateSingleIcons) {
                    $prefix = '<span class="treeline-icon treeline-icon-join"></span>';
                    $prefix2 = '<span class="treeline-icon treeline-icon-joinbottom"></span>';

                    if (strpos($treeNode['HTML'], $prefix) === 0) {
                        $pageTree->tree[$index]['CleanHTML'] = substr($treeNode['HTML'], strlen($prefix));
                    } elseif (strpos($treeNode['HTML'], $prefix2) === 0) {
                        $pageTree->tree[$index]['CleanHTML'] = substr($treeNode['HTML'], strlen($prefix2));
                    } else {
                        $pageTree->tree[$index]['CleanHTML'] = $treeNode['HTML'];
                    }
                }
            } else {
                unset($pageTree->tree[$index]);
            }
        }

        // Fetch language overlays
        if ($constraint->getLang() !== 0 && !empty($filteredIds)) {
            /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('pages_language_overlay');

            if (!$constraint->getHideDisabled()) {
                $queryBuilder
                    ->getRestrictions()
                    ->removeByType(HiddenRestriction::class);
            }

            if (!$constraint->getHideExpired()) {
                $queryBuilder
                    ->getRestrictions()
                    ->removeByType(StartTimeRestriction::class)
                    ->removeByType(EndTimeRestriction::class);
            }

            $queryBuilder
                ->select('*')
                ->from('pages_language_overlay')
                ->where(
                    $queryBuilder->expr()->in('pid', $filteredIds)
                );

            if ($constraint->getLang() > 0) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->eq(
                        'sys_language_uid',
                        $queryBuilder->createNamedParameter($constraint->getLang(), \PDO::PARAM_INT)
                    )
                );
            }

            $statement = $queryBuilder->execute();

            while (($row = $statement->fetch()) !== false) {
                if (isset($indexArray[$row['pid']]) && (int)$row['sys_language_uid'] > 0) {
                    $pageTree->tree[$indexArray[$row['pid']]]['languageOverlays'][(int)$row['sys_language_uid']]['row'] = $row;
                }
            }
        }

        if ($constraint->getLang() > 0) {
            $filteredIds = [];

            foreach ($pageTree->tree as $index => $treeNode) {
                if (!isset($treeNode['languageOverlays'])) {
                    unset($pageTree->tree[$index]);
                } else {
                    $filteredIds[] = $treeNode['row']['uid'];
                }
            }
        }

        // Fetch pretty URLs for the pages
        if (ExtensionManagementUtility::isLoaded('realurl') && !empty($filteredIds)) {
            $pathTable = version_compare(
                ExtensionManagementUtility::getExtensionVersion('realurl'),
                '2.0',
                '<'
            ) ? 'tx_realurl_pathcache' : 'tx_realurl_pathdata';

            /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable($pathTable);

            $queryBuilder
                ->getRestrictions()
                ->removeAll();

            $queryBuilder
                ->select('page_id', 'language_id', 'pagepath')
                ->from($pathTable)
                ->where(
                    $queryBuilder->expr()->in('page_id', $filteredIds)
                );

            if ($constraint->getLang() >= 0) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->eq(
                        'language_id',
                        $queryBuilder->createNamedParameter($constraint->getLang(), \PDO::PARAM_INT)
                    )
                );
            }

            $statement = $queryBuilder
                ->orderBy('language_id', 'ASC')
                ->addOrderBy('expire', 'ASC')
                ->execute();

            while (($row = $statement->fetch()) !== false) {
                if (isset($indexArray[$row['page_id']])) {
                    if ((int)$row['language_id'] === 0) {
                        $pageTree->tree[$indexArray[$row['page_id']]]['path'] = $row['pagepath'];
                    } elseif (isset($pageTree->tree[$indexArray[$row['page_id']]]['languageOverlays'][(int)$row['language_id']])) {
                        $pageTree->tree[$indexArray[$row['page_id']]]['languageOverlays'][(int)$row['language_id']]['path'] = $row['pagepath'];
                    }
                }
            }
        }

        return $pageTree->tree;
    }
}
