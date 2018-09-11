<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Ctrl;

use ilTextInputGUI;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntryFormGUI;

/**
 * Class ctrlmmEntryCtrlFormGUI
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Ctrl
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmEntryCtrlFormGUI extends ctrlmmEntryFormGUI {

	const F_REF_ID = 'ref_id';
	const F_CMD = 'cmd';
	const F_GUI_CLASS = 'gui_class';
	const F_ADDITIONS = 'additions';
	/**
	 * @var ctrlmmEntryCtrl
	 */
	protected $entry;


	public function addFields() {
		self::dic()->template()->addJavaScript(self::plugin()->directory() . '/templates/js/check.js');
		self::dic()->template()->addCss(self::plugin()->directory() . '/templates/css/check.css');

		$te = new ilTextInputGUI(self::plugin()->translate(self::F_GUI_CLASS), self::F_GUI_CLASS);
		$te->setRequired(true);
		$this->addItem($te);

		$te = new ilTextInputGUI(self::plugin()->translate(self::F_CMD), 'my_cmd');
		$te->setRequired(false);
		$this->addItem($te);

		$te = new ilTextInputGUI(self::plugin()->translate(self::F_REF_ID), self::F_REF_ID);
		$this->addItem($te);
	}


	/**
	 * @return array
	 */
	public function returnValuesAsArray() {
		$values = array(
			self::F_CMD => $this->entry->getCmd(),
			self::F_REF_ID => $this->entry->getRefId(),
			self::F_GUI_CLASS => $this->entry->getGuiClass(),
			self::F_ADDITIONS => $this->entry->getAdditions(),
		);

		return $values;
	}
}
