<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Ctrl;

use ctrlmmEntryGUI;
use ilHiddenInputGUI;
use ilSelectInputGUI;
use ilTextInputGUI;
use srag\Plugins\CtrlMainMenu\Helper\ctrlmmMultiLineInputGUI;
use srag\Plugins\CtrlMainMenu\Helper\ctrlmmUserDataReplacer;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ctrlmmEntryCtrlGUI
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Ctrl
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmEntryCtrlGUI extends ctrlmmEntryGUI {

	/**
	 * @var ctrlmmEntryCtrl
	 */
	public $entry;


	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/templates/js/check.js');
		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/css/check.css');

		parent::initForm($mode);

		$te = new ilTextInputGUI(self::plugin()->translate('gui_class'), 'gui_class');
		$te->setRequired(true);
		$this->form->addItem($te);

		$te = new ilTextInputGUI(self::plugin()->translate('common_cmd'), 'my_cmd');
		$te->setRequired(false);
		$this->form->addItem($te);

		$te = new ilTextInputGUI(self::plugin()->translate('ref_id'), 'ref_id');
		$this->form->addItem($te);

		$te = new ilHiddenInputGUI('type_id');
		$te->setValue($this->entry->getTypeId());
		$this->form->addItem($te);

		$se = new ilSelectInputGUI(self::plugin()->translate('common_target'), 'target');
		$opt = array( '_top' => self::plugin()->translate('same_page'), '_blank' => self::plugin()->translate('new_page') );
		$se->setOptions($opt);
		$this->form->addItem($se);

		$get_params = new ctrlmmMultiLineInputGUI(self::plugin()->translate("get_parameters"), 'get_params');
		$get_params->setInfo(self::plugin()->translate('get_parameters_description'));
		$get_params->setTemplateDir(self::plugin()->directory());

		$get_params->addInput(new ilTextInputGUI(self::plugin()->translate('get_param_name'), ctrlmmEntryCtrl::PARAM_NAME));

		$get_params_options = new ilSelectInputGUI(self::plugin()->translate('get_param_value'), ctrlmmEntryCtrl::PARAM_VALUE);
		$get_params_options->setOptions(ctrlmmUserDataReplacer::getDropdownData());
		$get_params->addInput($get_params_options);

		$this->form->addItem($get_params);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['gui_class'] = $this->entry->getGuiClass();
		$values['my_cmd'] = $this->entry->getCmd();
		$values['additions'] = $this->entry->getAdditions();
		$values['ref_id'] = $this->entry->getRefId();
		$values['target'] = $this->entry->getTarget();
		$values['get_params'] = $this->entry->getGetParams();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();

		$this->entry->setGuiClass($this->form->getInput('gui_class'));
		$this->entry->setCmd($this->form->getInput('my_cmd'));
		$this->entry->setAdditions($this->form->getInput('additions'));
		$this->entry->setRefId($this->form->getInput('ref_id'));
		$this->entry->setTarget($this->form->getInput('target'));

		// remove duplicates
		$get_params = $this->form->getInput('get_params');
		$this->entry->setGetParams(array_intersect_key($get_params, array_unique(array_map('serialize', $get_params))));
		$this->entry->update();
	}
}
