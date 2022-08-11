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

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Frontend\Configuration\TypoScript\ConditionMatching\ConditionMatcher;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        // @extensionScannerIgnoreLine
        $contentObject = $this->configurationManager->getContentObject();
        //TypoScript configuration given from tt_content record
        $configuration = $contentObject->data['bodytext'];

        $this->parser = GeneralUtility::makeInstance(TypoScriptParser::class);
        $this->matchCondition = GeneralUtility::makeInstance(ConditionMatcher::class);

        $setup = $this->scriptParser($configuration);
        if (isset($this->parser->sections) && is_array($this->parser->sections) && count($this->parser->sections)) {
            $contentObject->convertToUserIntObject();
        }

        return $this->htmlResponse($contentObject->cObjGet($setup, 'typoscript_code_proc.'));
    }

    /**
     * Call back method for preg_replace_callback in substituteConstants
     *
     * @param $matches
     * @return string Replacement
     * @see \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService::substituteConstants()
     */
    protected function substituteConstantsCallBack($matches): string
    {
        $s = $this->parser->getVal($matches[1], $this->parser->setup);
        return $s[0] ?? $matches[0];
    }

    /**
     * Parse, and return conf - array
     *
     * @param string $script
     * @param int $recursiveLevel
     * @return array TypoScript configuration array
     */
    protected function scriptParser(string $script, int $recursiveLevel = self::RECURSIVE_LEVEL): array
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
            if ($tsObjectKey !== (integer)$tsObjectKey) {
                $this->parser->setup[$tsObjectKey] = $tsObjectValue;
            }
        }

        $this->parser->parse($script, $this->matchCondition);
        return $this->parser->setup;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}