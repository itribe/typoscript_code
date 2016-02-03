<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Content',
    'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:plugins.title',
    'EXT:' . $_EXTKEY . '/ext_icon.png'
);

if (TYPO3_MODE === 'BE') {
    if(version_compare(TYPO3_branch, '7.0', '<')) {
        \TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons(
            array(
                'content' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.png'
            ),
            $_EXTKEY
        );
    } else {
        /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $prefaIconRegistry */
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon('extensions-typoscript_code-content', \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:typoscript_code/ext_icon.png']);
    }
}