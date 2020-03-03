<?php
defined('TYPO3_MODE') or die();

// @extensionScannerIgnoreLine
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'TyposcriptCode',
    'Content',
    [
        \Itribe\TyposcriptCode\Controller\ContentController::class => 'index',
    ],
    [],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

// Apply PageTSconfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:typoscript_code/Configuration/PageTS/modWizards.tsconfig">'
);