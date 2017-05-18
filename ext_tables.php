<?php
defined('TYPO3_MODE') or die();

// Adding Web>Info module for SEO management
if (TYPO3_MODE === 'BE') {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::insertModuleFunction(
		'web_info',
		'B13\\SeoBasics\\BackendModule\\SeoModule',
		'',
		'LLL:EXT:seo_basics/Resources/Private/Language/db.xml:module.title',
		'function'
	);
}


