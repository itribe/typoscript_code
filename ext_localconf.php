<?php
defined('TYPO3') or die();

use \Itribe\TyposcriptCode\Controller\ContentController;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(static function () {

    ExtensionUtility::configurePlugin(
        'TyposcriptCode',
        'Content',
        [
            ContentController::class => 'index',
        ],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    // Apply PageTSconfig
    ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:typoscript_code/Configuration/PageTS/modWizards.tsconfig">'
    );
})();


