<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Admin;

use ilAdministrationGUI;
use ilRadioGroupInputGUI;
use ilTextInputGUI;
use ilYuiUtil;
use srag\Plugins\CtrlMainMenu\AdvancedSelectionListDropdown\ctrlmmEntryAdvancedSelectionListDropdownGUI;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ctrlmmEntryAdminGUI
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Admin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Timon Amstutz <timon.amstutz@ilub.unibe.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryAdminGUI extends ctrlmmEntryAdvancedSelectionListDropdownGUI {

	/**
	 *
	 */
	public function customizeAdvancedSelectionList() {
		$this->selection->setListTitle($this->entry->getTitle());
		$this->selection->setId('dd_adm');
		$this->selection->setAsynch(true);
		$this->selection->setAsynchUrl('ilias.php?baseClass=' . ilAdministrationGUI::class . '&cmd=getDropDown&cmdMode=asynch');
	}


	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);
		/**
		 * @var ilRadioGroupInputGUI $permission_type
		 * @var ilTextInputGUI       $item
		 */
		$permission_type = $this->form->getItemByPostVar('permission_type');
		$permission_type->setDisabled(true);
	}


	/**
	 * @param string $entry_div_id
	 *
	 * @return string
	 */
	public function renderEntry($entry_div_id = '') {
		unset($entry_div_id);
		ilYuiUtil::initConnection();

		return parent::renderEntry();
	}


	public function createEntry() {
		parent::createEntry();
		$this->entry->update();
	}
}
