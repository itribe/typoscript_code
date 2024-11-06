<?php

use Itribe\TyposcriptCode\Controller\ContentController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

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
})();


