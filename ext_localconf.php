<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

/** @var string $_EXTKEY */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Itribe.' . $_EXTKEY,
	'Content',
	array(
		'Content' => 'index',
	),
	array(),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

if (TYPO3_MODE === 'BE') {
    // Apply PageTSconfig
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:typoscript_code/Configuration/PageTS/modWizards.ts">'
    );
    if (version_compare(TYPO3_branch, '7.6', '<')) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod.wizards.newContentElement.wizardItems.plugins.elements {
                typoscriptcode_content {
                    icon = ' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) .
                        'Resources/Public/Icons/wizard_icon.gif
                }
            }
            '
        );
    }
}