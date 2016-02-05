<?php
defined('TYPO3_MODE') or die();

// configure an CType element "TypoScript code"
$GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['showitem'] = '
    --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.general;general,
    --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.header;header,bodytext,
    --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.appearance,
    --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.frames;frames,
    --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.table_layout;tablelayout,
    --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,
    --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.visibility;visibility,
    --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.access;access,
    --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended';

//Add type icon class
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['typoscriptcode_content'] = 'extensions-typoscript_code-content';

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('t3editor')) {
    // @see Breaking: #67229 - FormEngine related classes
    if (version_compare(TYPO3_branch, '7.3', '<')) {
        // Add the t3editor wizard on the bodytext field of tt_content
        $GLOBALS['TCA']['tt_content']['columns']['bodytext']['config']['wizards']['typoscript_t3editor'] = array(
            'enableByTypeConfig' => 1,
            'type' => 'userFunc',
            'userFunc' => 'TYPO3\\CMS\\T3editor\\FormWizard->main',
            'params' => array(
                'format' => 'ts',
                'style' => 'width:98%;'
            )
        );
        // Activate the t3editor wizard
        $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['showitem'] =
            str_replace(
                'bodytext,',
                'bodytext;LLL:EXT:typoscript_code/Resources/Private/Language/locallang.xlf:bodytext;;nowrap:wizards[typoscript_t3editor],',
                $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['showitem']
            );
    } else if (is_array($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content'])) {
        // Activate t3editor for tt_content type typoscriptcode_content if this type exists
        if (!is_array($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides'])) {
            $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides'] = array();
        }
        if (!is_array($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext'])) {
            $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext'] = array();
        }
        $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['defaultExtras'] = 'nowrap:wizards[t3editor]';
        if (!is_array($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['config'])) {
            $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['config'] = array();
        }
        $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['config']['renderType'] = 't3editor';
        $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['config']['format'] = \TYPO3\CMS\T3editor\T3editor::MODE_TYPOSCRIPT;
        $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['label'] =
            'LLL:EXT:typoscript_code/Resources/Private/Language/locallang.xlf:bodytext';
    }
}
