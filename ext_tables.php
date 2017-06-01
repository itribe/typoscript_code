<?php
defined('TYPO3_MODE') or die();

/** @var string $_EXTKEY */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Content',
    'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:plugins.title',
    'EXT:' . $_EXTKEY . '/Resources/Public/Icons/tt_content_ts.png'
);

if (TYPO3_MODE === 'BE') {
	/** @noinspection PhpUndefinedClassInspection */
	/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $prefaIconRegistry */
	$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Imaging\\IconRegistry');
	$iconRegistry->registerIcon('extensions-typoscript_code-content', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider',
		array('source' => 'EXT:typoscript_code/Resources/Public/Icons/tt_content_ts.png'));
}