<?php

namespace Itribe\TyposcriptCode\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Frontend\Configuration\TypoScript\ConditionMatching\ConditionMatcher;

/**
 * TyposcriptCode Content Controller
 *
 * @author     Anton Danilov, interactive tribe GmbH
 * @package    TyposcriptCode
 * @subpackage Controller
 */
class ContentController extends ActionController
{

    const RECURSIVE_LEVEL = 10;

    /**
     * main parse class
     *
     * @var TypoScriptParser
     */
    protected $parser;

    /**
     * Matching TypoScript conditions
     *
     * @var ConditionMatcher
     */
    protected $matchCondition;

    /**
     * Render content
     *
     * @return string
     */
    public function indexAction()
    {
        // @extensionScannerIgnoreLine
        $contentObject = $this->configurationManager->getContentObject();
        //TypoScript configuration given from tt_content record
        $configuration = $contentObject->data['bodytext'];

        $this->parser = GeneralUtility::makeInstance(TypoScriptParser::class);
        $this->matchCondition = GeneralUtility::makeInstance(ConditionMatcher::class);

        $setup = $this->scriptParser($configuration, self::RECURSIVE_LEVEL);
        $this->tryChangeExtType();

        return $contentObject->cObjGet($setup, 'typoscript_code_proc.');
    }

    /**
     * Call back method for preg_replace_callback in substituteConstants
     *
     * @param $matches
     * @return string Replacement
     * @see \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService::substituteConstants()
     */
    protected function substituteConstantsCallBack($matches)
    {
        $s = $this->parser->getVal($matches[1], $this->parser->setup);
        return isset($s[0]) ? $s[0] : $matches[0];
    }

    /**
     * Change ext type to USER_INT if necessary
     * @return void
     */
    protected function tryChangeExtType()
    {
        if (isset($this->parser->sections) && is_array($this->parser->sections) && count($this->parser->sections)) {
            // @extensionScannerIgnoreLine
            $this->configurationManager->getContentObject()->convertToUserIntObject();
        }
    }

    /**
     * Parse, and return conf - array
     *
     * @param string $script
     * @param int $recursiveLevel
     * @return array TypoScript configuration array
     */
    protected function scriptParser($script, $recursiveLevel)
    {
        $script = $this->parser->checkIncludeLines($script);

        // get constants
        $this->parser->parse(
            implode(PHP_EOL, $this->getTypoScriptFrontendController()->tmpl->constants), $this->matchCondition
        );

        // recursive substitution of constants
        for ($i = 0; $i < $recursiveLevel; $i++) {
            $oldScript = $script;
            $script = preg_replace_callback('/\{\$(.[^}]*)\}/', [$this, 'substituteConstantsCallBack'], $script);
            if ($oldScript == $script) {
                break;
            }
        }

        foreach ($this->getTypoScriptFrontendController()->tmpl->setup as $tsObjectKey => $tsObjectValue) {
            if ($tsObjectKey !== intval($tsObjectKey, 10)) {
                $this->parser->setup[$tsObjectKey] = $tsObjectValue;
            }
        }

        $this->parser->parse($script, $this->matchCondition);
        return $this->parser->setup;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}