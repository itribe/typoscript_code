<?php
/***************************************************************
 *
 * Extension Manager/Repository config file for ext "typoscript_code".
 *
 ***************************************************************/

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = [
    'title' => 'TypoScript code',
    'description' => 'This extension allows you to insert any TypoScript code to a page as a normal content element.',
    'category' => 'plugin',
    'version' => '6.2.0',
    'state' => 'stable',
    'author' => 'Alexey Gafiulov, Anton Danilov',
    'author_email' => 'alexey.gafiulov@i-tribe.de, anton.danilov@i-tribe.de',
    'author_company' => 'interactive tribe GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '10.3.0-10.9.99',
        ],
        'conflicts' => [],
        'suggests' => ['t3editor' => '']
    ],
];