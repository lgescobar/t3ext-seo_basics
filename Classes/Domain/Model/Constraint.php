<?php
namespace B13\SeoBasics\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * NOTE: Based on EXT:belog
 *
 * Constraints for pages
 */
class Constraint extends AbstractEntity
{
    /**
     * Selected page ID in page context
     *
     * @var int
     */
    protected $pageId = 0;

    /**
     * Page level depth
     *
     * @var int
     */
    protected $depth = 0;

    /**
     * @var int
     */
    protected $lang = 0;

    /**
     * @var int
     */
    protected $hideShortcuts = 0;

    /**
     * @var int
     */
    protected $hideDisabled = 0;

    /**
     * @var int
     */
    protected $hideSysFolders = 0;

    /**
     * @var int
     */
    protected $hideNotInMenu = 0;

    /**
     * @var int
     */
    protected $hideExpired = 0;

    /**
     * added to prevent the deprecation message
     * in Extbase\DomainObject\AbstractDomainObject
     *
     * @todo the constraints model needs another way of storing
     * persisted search data than serialisation
     */
    public function __wakeup()
    {
    }

    /**
     * Set page id
     *
     * @param int $id
     */
    public function setPageId($id)
    {
        $this->pageId = (int)$id;
    }

    /**
     * Get page id
     *
     * @return int
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Set page level depth
     *
     * @param int $depth
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    /**
     * Get page level depth
     *
     * @return int
     */
    public function getDepth()
    {
        return (int)$this->depth;
    }

    /**
     * @return int
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param int $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return int
     */
    public function getHideShortcuts()
    {
        return $this->hideShortcuts;
    }

    /**
     * @param int $hideShortcuts
     */
    public function setHideShortcuts($hideShortcuts)
    {
        $this->hideShortcuts = $hideShortcuts;
    }

    /**
     * @return int
     */
    public function getHideDisabled()
    {
        return $this->hideDisabled;
    }

    /**
     * @param int $hideDisabled
     */
    public function setHideDisabled($hideDisabled)
    {
        $this->hideDisabled = $hideDisabled;
    }

    /**
     * @return int
     */
    public function getHideSysFolders()
    {
        return $this->hideSysFolders;
    }

    /**
     * @param int $hideSysFolders
     */
    public function setHideSysFolders($hideSysFolders)
    {
        $this->hideSysFolders = $hideSysFolders;
    }

    /**
     * @return int
     */
    public function getHideNotInMenu()
    {
        return $this->hideNotInMenu;
    }

    /**
     * @param int $hideNotInMenu
     */
    public function setHideNotInMenu($hideNotInMenu)
    {
        $this->hideNotInMenu = $hideNotInMenu;
    }

    /**
     * @return int
     */
    public function getHideExpired()
    {
        return $this->hideExpired;
    }

    /**
     * @param int $hideExpired
     */
    public function setHideExpired($hideExpired)
    {
        $this->hideExpired = $hideExpired;
    }
}
