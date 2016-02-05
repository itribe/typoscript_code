<?php
namespace Itribe\TyposcriptCode\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  2014-2016 Anton Danilov <anton.danilov@i-tribe.de>, interactive tribe GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public $License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public $License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public $License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Configuration\TypoScript\ConditionMatching\ConditionMatcher;

/**
 * TyposcriptCode Content Controller
 *
 * @author     Anton Danilov, interactive tribe GmbH
 * @package    TyposcriptCode
 * @subpackage Controller
 */
class ContentController extends ActionController {

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
	public function indexAction() {
		$contentObject = $this->configurationManager->getContentObject();
		//TypoScript configuration given from tt_content record
		$configuration = $contentObject->data['bodytext'];

		$this->parser = $this->objectManager->get('TYPO3\\CMS\\Core\\TypoScript\\Parser\\TypoScriptParser');
		$this->matchCondition = $this->objectManager->get('TYPO3\\CMS\\Frontend\\Configuration\\TypoScript\\ConditionMatching\\ConditionMatcher');

		$setup = $this->scriptParser($configuration, self::RECURSIVE_LEVEL);
		$this->tryChangeExtType();

		return $this->configurationManager->getContentObject()->cObjGet($setup, 'typoscript_code_proc.');
	}

	/**
	 * Call back method for preg_replace_callback in substituteConstants
	 *
	 * @param $matches
	 * @return string Replacement
	 * @see substituteConstants()
	 */
	public function substituteConstantsCallBack($matches) {
		$s = $this->parser->getVal($matches[1], $this->parser->setup);
		return isset($s[0]) ? $s[0] : $matches[0];
	}

	/**
	 * Change ext type to USER_INT if necessary
	 * @return void
	 */
	protected function tryChangeExtType() {
		if (isset($this->parser->sections) && is_array($this->parser->sections) && count($this->parser->sections)) {
			$this->configurationManager->getContentObject()->convertToUserIntObject();
		}
	}

	/**
	 * Parse, and return conf - array
	 *
	 * @param string $script
	 * @param int    $recursiveLevel
	 * @return array TypoScript configuration array
	 */
	protected function scriptParser($script = '', $recursiveLevel) {
		$script = $this->parser->checkIncludeLines($script);

		// get constants
		$this->parser->parse(implode(PHP_EOL, $GLOBALS['TSFE']->tmpl->constants), $this->matchCondition);

		// recursive substitution of constants
		for ($i = 0; $i < $recursiveLevel; $i++) {
			$oldScript = $script;
			$script = preg_replace_callback('/\{\$(.[^}]*)\}/', array($this, 'substituteConstantsCallBack'), $script);
			if ($oldScript == $script) {
				break;
			}
		}

		foreach ($GLOBALS['TSFE']->tmpl->setup as $tsObjectKey => $tsObjectValue) {
			if ($tsObjectKey !== intval($tsObjectKey, 10)) {
				$this->parser->setup[$tsObjectKey] = $tsObjectValue;
			}
		}

		$this->parser->parse($script, $this->matchCondition);
		return $this->parser->setup;
	}

}