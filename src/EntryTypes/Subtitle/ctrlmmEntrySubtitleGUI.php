<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Subtitle;

use ctrlmmEntryGUI;
use ilCheckboxInputGUI;
use ilRadioGroupInputGUI;
use ilTextInputGUI;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ctrlmmEntrySubtitleGUI
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Subtitle
 *
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @version 1.0.0
 *
 */
class ctrlmmEntrySubtitleGUI extends ctrlmmEntryGUI {

	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);

		$cb = new ilCheckboxInputGUI(self::plugin()->translate('show_with_no_children'), 'show_with_no_children');
		$this->form->addItem($cb);

		/**
		 * @var ilRadioGroupInputGUI $permission_type
		 * @var ilTextInputGUI       $item
		 */
		$permission_type = $this->form->getItemByPostVar('permission_type');
		foreach (ctrlmmEntry::getAllLanguageIds() as $language) {
			$item = $this->form->getItemByPostVar('title_' . $language);
			$item->setRequired(false);
		}
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['show_with_no_children'] = $this->entry->getShowWithNoChildren();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();
		$this->entry->setShowWithNoChildren($this->form->getInput('show_with_no_children'));
		$this->entry->update();
	}
}
