<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Michael Schulze <m.schulze@elsigno.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Energy Formula Entry' for the 'formulae' extension.
 *
 * @author	Michael Schulze <m.schulze@elsigno.de>
 * @package	TYPO3
 * @subpackage	tx_formulae
 */
class tx_formulae_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_formulae_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_formulae_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'formulae';	// The extension key.
	var $pi_checkCHash = true;
	
	var $formulasTable = 'tx_formulae_formulas';

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->init($conf);

		switch($this->view) {
			case 'new':
				$content = $this->newAction();
				break;
			case 'create':
				$content = $this->createAction();
				break;
			case 'list':
				$content = $this->listAction();
				break;
			case 'vote':
				$content = $this->voteAction();
				break;
			default:
				$content = $this->listAction();
				break;
		}
	
		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Initialize some stuff like vars and definitions for required fields
	 *
	 * @param array configuration array
	 * @return void
	 */
	private function init($conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults(); // Set default piVars from TS
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
		$this->pi_loadLL();

		$this->view = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'view', 'sDEF');

		$this->local_cObj = t3lib_div::makeInstance('tslib_cObj'); // Local cObj.
		
		$this->validation = array(
			'firstname' => array(
				'errorCheck' => 5,				// min 5 letters and must not be empty
				'errorMessage' => $this->pi_getLL('error_prename')
			),					
			'lastname' => array(
				'errorCheck' => 5,				// min 5 letters and must not be empty
				'errorMessage' => $this->pi_getLL('error_lastname')
			),
			'title' => array(
				'errorCheck' => 1,				// must not be empty
				'errorMessage' => $this->pi_getLL('error_lastname')
			),
			'email' => array(
				'errorCheck' => 'email',	// validate as email with @ and tld ending and must not be empty
				'errorMessage' => $this->pi_getLL('error_email')
			),
			'formula' => array(
				'errorCheck' => 15,				// min 15 letters and must not be empty
				'errorMessage' => $this->pi_getLL('error_formula')
			),
			'street' => array(
				'errorCheck' => 5,				// min 5 letters and must not be empty
				'errorMessage' => $this->pi_getLL('error_street')
			),
			'city' => array(
				'errorCheck' => 5,				// min 5 letters and must not be empty
				'errorMessage' => $this->pi_getLL('error_city')
			),
			'gtc' => array(
				'errorCheck' => 1,				// must not be empty
				'errorMessage' => $this->pi_getLL('error_city')
			)
		);
		
		$this->postvars = t3lib_div::_POST();
		$this->getvars = t3lib_div::_GET();
		
		// define view
		if (is_array($this->postvars) && count($this->postvars) > 0) {
			$this->view = 'create';
		} else if ($this->getvars['action'] == vote && $this->getvars['formula-uid']) {
			$this->view = 'vote';
		}
	}

	/**
	 * Shows the form for a new entry
	 *
	 * @return string HTML output for frontend
	 */
	protected function newAction() {
		return $this->showForm();
	}
	
	/**
	 * Validate and creates a new object
	 *
	 * @return string HTML output for frontend
	 */
	protected function createAction() {
		if ($this->validateForm() == true) {
			$date = new DateTime();

			if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$client_ip = $_SERVER['REMOTE_ADDR'];
			}
			else {
				$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}

			$saveData = $this->postvars;
			$saveData['pid'] = $this->conf['storagePid'];
			$saveData['hidden'] = $this->conf['saveHidden'];
			$saveData['votes'] = 1;
			$saveData['finalvotes'] = 1;
			$saveData['tstamp'] = $date->getTimestamp();
			$saveData['crdate'] = $date->getTimestamp();
			$saveData['ipaddress'] = $client_ip;
			$insert = $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->formulasTable, $saveData);
		} else {
			return $this->showForm($errors = true);
		}
	}
	
	/**
	 * Shows all Formulas
	 *
	 * @return string HTML output for frontend
	 */
	protected function listAction() {
		$formulas = $this->getFormulas();
		$content = $this->showList($formulas);
		return $content;
	}
	
	/**
	 * Check and do voting
	 *
	 * @return string HTML output for the frontend
	 */
	protected function voteAction() {
		if ($this->checkCookie() == false) {
			$uid = $this->getvars['formula-uid'];
			
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('votes', $this->formulasTable, 'uid='.$uid, '', '');
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
				$votes = $row['votes'];
			}
			echo $votes;
			$fields = array(
				'votes' => $votes+1
			);
			$update = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->formulasTable,'uid='.$uid,$fields);
		} else {
			// no voting
		}
		
		return $this->showThanks4Vote();
	}

	/**
	 * Shows the form
	 *
	 * @param boolean true if errors should be displayed (only after validation)
	 * @return string build form as HTML
	 */
	private function showForm($errors = false) {	
		$this->errors = ($errors) ? true : false;
		
		$form = '<form action="" method="post" class="yform columnar">';
		$form .= '<fieldset>';

		// adding formula
		$form .= '
			<div class="type-text '.$this->isError('formula').'">
				<label for="formula">Energie-Formel</label>
				<textarea id="formula" name="formula" cols="30" rows="7">'.$this->isValue('formula').'</textarea>
			</div>
		</fieldset>';
		
		// adding contact informations
		$form .= '<fieldset>';
		$form .= '
			<div class="type-check '.$this->isError('title').'">
				<p>Anrede</p>
				<input type="radio" id="title-1" name="title" value="0" '.$this->isChecked('title', 0).'/>
				<label for="title-1">Herr</label>
				<input type="radio" id="title-2" name="title" value="1" '.$this->isChecked('title', 1).'/>
				<label for="title-2">Frau</label>
			</div>
			<div class="type-text '.$this->isError('firstname').'">
				<label for="firstname">Vorname</label>
				<input id="firstname" name="firstname" type="text" value="'.$this->isValue('firstname').'" />
			</div>
			<div class="type-text '.$this->isError('lastname').'">
				<label for="lastname">Nachname</label>
				<input id="lastname" name="lastname" type="text" value="'.$this->isValue('lastname').'" />
			</div>
			<div class="type-text '.$this->isError('company').'">
				<label for="company">Firma</label>
				<input id="company" name="company" type="text" value="'.$this->isValue('company').'" />
			</div>
			<div class="type-text '.$this->isError('street').'">
				<label for="street">Strasse</label>
				<input id="street" name="street" type="text" value="'.$this->isValue('street').'" />
			</div>
			<div class="type-text '.$this->isError('city').'">
				<label for="city">Stadt</label>
				<input id="city" name="city" type="text" value="'.$this->isValue('city').'" />
			</div>
			<div class="type-text '.$this->isError('email').'">
				<label for="email">E-Mail</label>
				<input id="email" name="email" type="text" value="'.$this->isValue('email').'" />
			</div>
			<div class="type-check '.$this->isError('gtc').'">
				<label for="gtc">AGB</label>
				<input id="gtc" name="gtc" type="checkbox" value="1" '.$this->isChecked('gtc', 1).'/>
			</div>
		';
		$form .= '</fieldset>';

		// adding submit button
		$form .= '
			<div class="type-button">
				<input type="submit" class="submit" value="absenden"/>
			</div>';
		$form .= '</form>';
			
		return $form;
	}
	
	/**
	 * Creates the list view with links for voting
	 *
	 * @param array all formulas to display
	 * @return string HTML with list view
	 */
	private function showList($formulas = array()) {
		$list = '<div class="formulae-list">';
		
		foreach ($formulas as $formula) {
			$urlParameters=array(
				'action' => 'vote',
				'formula-uid' => $formula['uid']
			);
			$list .= '<div class="formula">
				<p>'.$formula['formula'].'</p>'
				.$this->pi_linkTP('Voten',$urlParameters,0,$GLOBALS["TSFE"]->id)
				.'</div>';
		}
		$list .= '</div>';
		
		return $list;
	}
	
	/**
	 * Shows the "thank you" message after voting
	 *
	 * @return string HTML thank you message
	 */
	private function showThanks4Vote() {
		return "Vielen Dank für die Stimme";
	}

	/**
	 * Gets all formulas as an array
	 *
	 * @return array all formulas
	 */
	private function getFormulas() {
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,formula,firstname,lastname,tstamp,crdate', $this->formulasTable, 'hidden=0 AND deleted=0 AND gtc=1', '', 'crdate');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$formulas[] = $row;
		}
		
		return $formulas;
	}
	
	/**
	 * Checks if a cookie is set
	 *
	 * @return boolean true if cookie is set and false if not
	 */
	private function checkCookie() {
		return false;
	}

	/**
	 * Validates the form
	 *
	 * @param array validation fields
	 * @param array form fields
	 * @return boolean true if validation is successful and false if not
	 */
	private function validateForm() {
		$valid = true;
		foreach ($this->validation as $fieldName => $fieldCheck) {
			// check length
			if (strlen($this->postvars[$fieldName]) >= $fieldCheck['errorCheck'] && $fieldCheck['errorCheck'] != 'email'){

			} else if ($fieldCheck['errorCheck'] == 'email' && t3lib_div::validEmail($this->postvars[$fieldName]) == true) {

			} else {
				$this->validation[$fieldName]['isError'] = true;
				$valid = false;
			}
		}
		
		return $valid;
	}

	/**
	 * Checks if a field is error and returns the error CSS class
	 *
	 * @param string fieldname that should be checked
	 * @return string css error class
	 */
	private function isError($field) {
		if ($this->errors && $this->validation[$field]['isError']) {
			return $this->conf['errorClassName'];
		} else {
			return '';
		}
	}
	
	/**
	 * Refills the form field that already was filled out
	 *
	 * @param string fieldname
	 * @return string value given by postvars
	 */
	private function isValue($field) {
		return $this->postvars[$field];
	}
	
	/**
	 * Check the checkbox or radiobutton which was checked
	 *
	 * @param string fieldname of checkbox or radiobutton
	 * @param int the value of this checkbox / radiobutton
	 * @return string checked or empty
	 */
	private function isChecked($fieldname, $value) {
		return (isset($this->postvars[$fieldname]) && $this->postvars[$fieldname] == $value) ? 'checked="checked"' : '';
	}

	/**
	 * The default content created by the extension kickstarter
	 *
	 * @return string content to be displayed
	 */
	private function kickstarterContent() {
		$content = '
			<strong>This is a few paragraphs:</strong><br />
			<p>This is line 1</p>
			<p>This is line 2</p>
	
			<h3>This is a form:</h3>
			<form action="'.$this->pi_getPageLink($GLOBALS['TSFE']->id).'" method="POST">
				<input type="text" name="'.$this->prefixId.'[input_field]" value="'.htmlspecialchars($this->piVars['input_field']).'">
				<input type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL('submit_button_label')).'">
			</form>
			<br />
			<p>You can click here to '.$this->pi_linkToPage('get to this page again',$GLOBALS['TSFE']->id).'</p>
		';

		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formulae/pi1/class.tx_formulae_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formulae/pi1/class.tx_formulae_pi1.php']);
}

?>