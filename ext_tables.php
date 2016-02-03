<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

/** @noinspection PhpUndefinedVariableInspection */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
	array(
		'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:plugins.title',
		'typoscriptcode_content',
		'EXT:' . $_EXTKEY . '/ext_icon.gif'
	),
	'CType'
);

if (TYPO3_MODE === 'BE') {

	$TCA['tt_content']['ctrl']['typeicon_classes']['typoscriptcode_content'] =  'extensions-'.$_EXTKEY.'-type-typoscript_code';
	if (TYPO3_MODE == 'BE') {
		$icons = array(
			'type-typoscript_code' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif',
		);
		\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons($icons, $_EXTKEY);
	}

	if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('t3editor')) {
		// Add the t3editor wizard on the bodytext field of tt_content
		$TCA['tt_content']['columns']['bodytext']['config']['wizards']['typoscript_t3editor'] = array(
			'enableByTypeConfig' => 1,
			'type' => 'userFunc',
			'userFunc' => 'TYPO3\\CMS\\T3editor\\FormWizard->main',
			'title' => 'typoscript_t3editor',
			'icon' => 'wizard_table.gif',
			'script' => '',
			'params' => array(
				'format' => 'ts'
			)
		);
		if(isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['compat_version'])) {
			if((float)$GLOBALS['TYPO3_CONF_VARS']['SYS']['compat_version'] < 6.2) {
				$TCA['tt_content']['columns']['bodytext']['config']['wizards']['typoscript_t3editor']['userFunc'] =
					'EXT:t3editor/Classes/class.tx_t3editor_tceforms_wizard.php:TYPO3\\CMS\\T3editor\\FormWizard->main';
			}
		}
	}

	$GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['showitem'] =
		'--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.general;general,
					--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.header;header,
					bodytext;LLL:EXT:typoscript_code/Resources/Private/Language/locallang.xlf:tt_content.bodytext;;nowrap:wizards[typoscript_t3editor],
				--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.appearance,
					--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.frames;frames,
					--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.table_layout;tablelayout,
				--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,
					--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.visibility;visibility,
					--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.access;access,
				--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended';

	//Wizard icon generator
	$GLOBALS['TBE_MODULES_EXT']['xMOD_db_new_content_el']['addElClasses']['WizardIcon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Wizard/WizardIcon.php';
}