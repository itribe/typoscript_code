<?php

/***************************************************************
 *  Copyright notice
 *
 *  2014 Anton Danilov <anton.danilov@i-tribe.de>, interactive tribe GmbH
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

/**
 * Class, containing function for adding an element to the content element wizard.
 *
 * @author     Anton Danilov, interactive tribe GmbH
 * @package    TyposcriptCode
 */
class WizardIcon {

	protected $extKey = 'typoscript_code';

	/**
	 * Processing the wizard-item array from db_new_content_el.php
	 *
	 * @param array $wizardItems Wizard item array
	 * @return array    Wizard item array, processed (adding a plugin)
	 */
	function proc($wizardItems) {
		/** @var \TYPO3\CMS\Lang\LanguageService $LANG */
		global $LANG;

		$wizardItems['plugins_typoscriptcode_content'] = array(
			'icon' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($this->extKey) . 'Resources/Public/Icons/wizard_icon.gif',
			'title' => $LANG->sL('LLL:EXT:' . $this->extKey . '/Resources/Private/Language/locallang.xlf:plugins.title'),
			'description' => $LANG->sL('LLL:EXT:' . $this->extKey . '/Resources/Private/Language/locallang.xlf:plugins.description'),
			'params' => '&defVals[tt_content][CType]=typoscriptcode_content'
		);

		return $wizardItems;
	}
}
