<?php

call_user_func(function () {
    $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['showitem'] = '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header;header,bodytext,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ';

    // Add type icon class
    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['typoscriptcode_content'] = 'extensions-typoscript_code-content';
    // Activate t3editor for tt_content type typoscriptcode_content if this type exists and t3editor is loaded
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('t3editor')
        && isset($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content'])
        && is_array($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content'])
    ) {
        if (!isset($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides'])
            || !is_array($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides'])) {
            $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides'] = [];
        }
        if (!isset($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext'])
            || !is_array($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext'])) {
            $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext'] = [];
        }
        if (!isset($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['config'])
            || !is_array($GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['config'])) {
            $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['config'] = [];
        }
        $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['config']['renderType'] = 't3editor';
        $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['config']['wrap'] = 'off';
        $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['config']['format'] = 'typoscript';
        $GLOBALS['TCA']['tt_content']['types']['typoscriptcode_content']['columnsOverrides']['bodytext']['label'] = 'LLL:EXT:typoscript_code/Resources/Private/Language/locallang.xlf:bodytext';
    }
    // Register the plugin
    // @extensionScannerIgnoreLine
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'TyposcriptCode',
        'Content',
        'LLL:EXT:typoscript_code/Resources/Private/Language/locallang.xlf:plugins.title',
        'extensions-typoscript_code-content'
    );
});
