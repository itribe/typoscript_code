<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(function () {
    $typesConfig = [
        'showitem' => '
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
    ',
        'columnsOverrides' => []
    ];

    // Activate t3editor for tt_content type typoscriptcode_content if this type exists and t3editor is loaded
    if (ExtensionManagementUtility::isLoaded('t3editor')
    ) {
        $typesConfig['columnsOverrides'] = [
            'bodytext' => [
                'label' => 'LLL:EXT:typoscript_code/Resources/Private/Language/locallang.xlf:bodytext',
                'config' => [
                    'renderType' => 't3editor',
                    'wrap' => 'off',
                    'format' => 'typoscript',
                ],
            ],
        ];
    }
    // Register the plugin
    $plugin = ExtensionUtility::registerPlugin(
        'TyposcriptCode',
        'Content',
        'LLL:EXT:typoscript_code/Resources/Private/Language/locallang.xlf:plugins.title',
        'extensions-typoscript_code-content'
    );
    ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['tt_content'], [
        'ctrl' => [
            // Add type icon class
            'typeicon_classes' => [
                $plugin => 'extensions-typoscript_code-content',
            ],
        ],
        'types' => [
            $plugin => $typesConfig,
        ],
    ]);
});
