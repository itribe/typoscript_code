<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

/** @noinspection PhpUndefinedVariableInspection */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Itribe.' . $_EXTKEY,
	'Content',
	array(
		'Content' => 'index',
	),
	array(),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);