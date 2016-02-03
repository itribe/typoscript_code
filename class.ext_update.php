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
 * Class ext_update
 *
 * Performs update tasks for extension typoscript_code
 */
class ext_update {

	/**
	 * Array of flash messages (params) array[][status,title,message]
	 *
	 * @var array
	 */
	protected $messageArray = array();

	/**
	 * Called by the extension manager to determine if the update menu entry should by showed.
	 * @return boolean
	 */
	public function access() {
		/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $TYPO3_DB */
		global $TYPO3_DB;
		//count old content elements
		$res = $TYPO3_DB->exec_SELECTquery('*', 'tt_content', 'CType=\'list\' AND list_type=\'typoscript_code_pi1\' AND deleted = 0');
		$oldRecords = (bool)$TYPO3_DB->sql_num_rows($res);
		return $oldRecords;
	}

	/**
	 * Main method
	 * @return string
	 */
	public function main() {
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_POST('numberOfItems')) {
			$this->updateOldElements((int)\TYPO3\CMS\Core\Utility\GeneralUtility::_POST('numberOfItems'));
		}
		return $this->generateOutput();
	}

	/**
	 * @param int $limit
	 * @return void
	 */
	protected function updateOldElements($limit = 100) {
		/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $TYPO3_DB */
		global $TYPO3_DB;
		//get old records set
		$updated = 0;
		$dbErrors = array();
		$contentErrors = array();
		$res = $TYPO3_DB->exec_SELECTquery('*', 'tt_content', 'CType=\'list\' AND list_type=\'typoscript_code_pi1\' AND deleted = 0', '', 'uid ASC', $limit);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res)) {
			$flexform = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($row['pi_flexform']);
			if (is_array($flexform) &&
				is_array($flexform['data']) &&
				is_array($flexform['data']['code']) &&
				is_array($flexform['data']['code']['lDEF']) &&
				is_array($flexform['data']['code']['lDEF']['code_text']) &&
				isset($flexform['data']['code']['lDEF']['code_text']['vDEF'])
			) {

				$ts = (string)$flexform['data']['code']['lDEF']['code_text']['vDEF'];
				$data = array(
					'bodytext' => $ts,
					'CType' => 'typoscriptcode_content',
					'list_type' => '',
					'pi_flexform' => ''
				);
				$update = (bool)$TYPO3_DB->exec_UPDATEquery('tt_content', 'uid=' . $row['uid'], $data);
				if (!$update) {
					$dbErrors[] = $row['uid'];
				} else {
					$updated++;
				}
			} else {
				$contentErrors[] = $row['uid'];
			}
		}
		if (count($contentErrors)) {
			$this->messageArray[] = array(\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR, 'Error!', 'No TypoScript found in content elements ' . implode(',', $contentErrors));
		}
		if (count($dbErrors)) {
			$this->messageArray[] = array(\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR, 'Error!', 'Database update failed for content elements ' . implode(',', $dbErrors));
		}

		$this->messageArray[] = array(\TYPO3\CMS\Core\Messaging\FlashMessage::OK, 'Migration', $updated . ' content elements have been updated!');
	}

	/**
	 * Generates output by using flash messages
	 *
	 * @return string
	 */
	protected function generateOutput() {
		$output = '';
		/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $TYPO3_DB */
		global $TYPO3_DB;
		//count old content elements
		$res = $TYPO3_DB->exec_SELECTquery('*', 'tt_content', 'CType=\'list\' AND list_type=\'typoscript_code_pi1\'');
		$oldRecordsCount = (int)$TYPO3_DB->sql_num_rows($res);
		$output .= '<p>Not upgraded items found: ' . $oldRecordsCount . '</p>';

		foreach ($this->messageArray as $messageItem) {
			/** @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
			$flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
				'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
				$messageItem[2],
				$messageItem[1],
				$messageItem[0]);
			$output .= $flashMessage->render();
		}

		$output .= '<form action="' . \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REQUEST_URI') . '" method="POST">
						<label for="update-numberOfItems">
							Number of items at a time:
						</label>
						<select id="update-numberOfItems" name="numberOfItems">
							<option value="100">Update 100</option>
							<option value="500">Update 500</option>
							<option value="1000">Update 1000</option>
							<option value="5000">Update 5000</option>
						</select>

						<button class="btn " type="submit" name="migrate_submit">
							Update
						</button>
					</form>';

		return $output;
	}
}
