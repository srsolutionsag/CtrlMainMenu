<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryGUI.php');

/**
 * ctrlmmEntrySeparatorGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntrySeparatorGUI extends ctrlmmEntryGUI {

	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);
		/**
		 * @var $permission_type ilRadioGroupInputGUI
		 * @var $item            ilTextInputGUI
		 */
		$permission_type = $this->form->getItemByPostVar('permission_type');
		$permission_type->setDisabled(true);

		foreach (ctrlmmEntry::getAllLanguageIds() as $language) {
			$item = $this->form->getItemByPostVar('title_' . $language);
			$item->setDisabled(true);
			$item->setRequired(false);
		}
	}


	/**
	 * @param string $entry_div_id
	 * @return string
	 */
	public function renderEntry($entry_div_id = '') {
		unset($entry_div_id);
		$this->html = $this->pl->getTemplate('tpl.menu_separator.html', false, false);

		return $this->html->get();
	}
}

?>
