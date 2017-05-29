<?php
/***************************************************************
 *
 * Extension Manager/Repository config file for ext "typoscript_code".
 *
 ***************************************************************/

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = array(
	'title' => 'TypoScript code',
	'description' => 'This extension allows you to insert any TypoScript code to a page as a normal content element.',
	'category' => 'plugin',
	'version' => '6.0.0-dev',
	'state' => 'stable',
	'author' => 'Alexey Gafiulov, Anton Danilov',
	'author_email' => 'alexey.gafiulov@i-tribe.de, anton.danilov@i-tribe.de',
	'author_company' => 'interactive tribe GmbH',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-7.6.99',
		),
		'conflicts' => array(),
		'suggests' => array('t3editor' => '')
	),
);