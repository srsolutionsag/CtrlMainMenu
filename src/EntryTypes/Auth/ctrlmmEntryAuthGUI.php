<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Auth;

use ctrlmmEntryGUI;
use srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ctrlmmEntryAuthGUI
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Auth
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryAuthGUI extends ctrlmmEntryGUI {

	/**
	 * @var ctrlmmEntryAuth
	 */
	public $entry;


	/**
	 * @param string $entry_div_id
	 *
	 * @return string
	 */
	public function renderEntry($entry_div_id = '') {
		unset($entry_div_id);
		self::dic()->template()->addCss(self::plugin()->directory() . '/templates/css/login.css');
		$this->html = self::plugin()->template('tpl.ctrl_menu_entry.html', true, true);
		$this->html->setVariable('TITLE', $this->entry->getTitle());
		$this->html->setVariable('CSS_ID', 'ctrl_mm_e_' . $this->entry->getId());
		$this->html->setVariable('LINK', $this->entry->getLink());

		$this->html->setVariable('CSS_PREFIX', ctrlmmMenu::getCssPrefix());
		$this->html->setVariable('TARGET', $this->entry->getTarget());
		$this->html->setVariable('STATE', ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_INACTIVE));
		$this->html->setVariable('CTRLMM_CLASS', $this->entry->isLoggedIn() ? 'ctrlMMLoggedIn' : 'ctrlMMLoggedout');

		if ($this->entry->isLoggedIn()) {
			$this->html->setVariable('NONLINK', $this->entry->getUsername());
		} else {
			$this->html->setVariable('NONLINK', self::dic()->language()->txt('not_logged_in'));
		}

		return $this->html->get();
	}
}
