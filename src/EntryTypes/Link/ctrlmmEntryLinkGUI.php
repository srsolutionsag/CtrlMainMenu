<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Link;

use ctrlmmEntryGUI;
use ilSelectInputGUI;
use ilTextInputGUI;
use srag\Plugins\CtrlMainMenu\Helper\ctrlmmMultiLineInputGUI;
use srag\Plugins\CtrlMainMenu\Helper\ctrlmmUserDataReplacer;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ctrlmmEntryLinkGUI
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Link
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryLinkGUI extends ctrlmmEntryGUI {

	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);

		$te = new ilTextInputGUI(self::plugin()->translate('common_link'), 'url');
		$te->setRequired(true);
		$this->form->addItem($te);

		$se = new ilSelectInputGUI(self::plugin()->translate('common_target'), 'target');
		$opt = array( '_top' => self::plugin()->translate('same_page'), '_blank' => self::plugin()->translate('new_page') );
		$se->setOptions($opt);
		$this->form->addItem($se);

		$get_params = new ctrlmmMultiLineInputGUI(self::plugin()->translate("get_parameters"), 'get_params');
		$get_params->setInfo(self::plugin()->translate('get_parameters_description'));
		$get_params->setTemplateDir(self::plugin()->directory());

		$get_params->addInput(new ilTextInputGUI(self::plugin()->translate('get_param_name'), ctrlmmEntryLink::PARAM_NAME));

		$get_params_options = new ilSelectInputGUI(self::plugin()->translate('get_param_value'), ctrlmmEntryLink::PARAM_VALUE);
		$get_params_options->setOptions(ctrlmmUserDataReplacer::getDropdownData());
		$get_params->addInput($get_params_options);

		$this->form->addItem($get_params);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values['url'] = $this->entry->getUrl();
		$values['target'] = $this->entry->getTarget();
		$values['get_params'] = $this->entry->getGetParams();

		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();

		$this->entry->setUrl($this->form->getInput('url'));
		$this->entry->setTarget($this->form->getInput('target'));

		// remove duplicates
		$get_params = $this->form->getInput('get_params');
		$this->entry->setGetParams(array_intersect_key($get_params, array_unique(array_map('serialize', $get_params))));
		$this->entry->update();
	}
}
