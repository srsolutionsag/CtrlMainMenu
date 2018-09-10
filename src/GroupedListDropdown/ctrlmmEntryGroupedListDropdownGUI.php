<?php

namespace srag\Plugins\CtrlMainMenu\GroupedListDropdown;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

use ctrlmmEntryGUI;
use ilGroupedListGUI;
use ilOverlayGUI;
use ilTemplate;
use ilUtil;
use srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig;

/**
 * ctrlmmEntryGroupedListDropdownGUI
 *
 * @package srag\Plugins\CtrlMainMenu\GroupedListDropdown
 *
 * @author  Timon Amstutz <timon.amstutz@ilub.unibe.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
abstract class ctrlmmEntryGroupedListDropdownGUI extends ctrlmmEntryGUI {

	/**
	 * @var bool
	 */
	protected $show_arrow = true;
	/**
	 * @var ilGroupedListGUI
	 */
	protected $gl = NULL;
	/**
	 * @var ilOverlayGUI
	 */
	protected $ov = NULL;
	/**
	 * @var ilTemplate
	 */
	protected $html;


	/**
	 * @param string $entry_div_id
	 *
	 * @return string
	 */
	public function renderEntry($entry_div_id = '') {
		unset($entry_div_id);

		$this->gl = new ilGroupedListGUI();

		$this->gl->setAsDropDown(true);

		$this->setGroupedListContent();

		$this->html = self::plugin()->template('tpl.grouped_list_dropdown.html');

		$this->html->setVariable('TXT_TITLE', $this->entry->getTitle());
		$this->html->setVariable('PREFIX', ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_PREFIX));
		if ($this->show_arrow) {
			$this->html->setVariable('ARROW_IMG', ilUtil::getImagePath('mm_down_arrow.png'));
		}
		if ($this->entry->getIcon()) {
			$this->html->setVariable('ICON', $this->entry->getIcon());
		}

		$this->html->setVariable('CONTENT', $this->getContent());
		$this->html->setVariable('ENTRY_ID', $this->getDropdownId());
		$this->html->setVariable('OVERLAY_ID', $this->getDropdownId('ov'));
		$this->html->setVariable('TARGET_REPOSITORY', '_top');

		$list_id = ($this->entry->getListId() != '') ? ' id="' . $this->entry->getListId() . '"' : '';
		$this->html->setVariable('LIST_ID', $list_id);

		if ($this->entry->isActive()) {
			$this->html->setVariable('MM_CLASS', ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_ACTIVE));
			$this->html->setVariable('SEL', '<span class=\'ilAccHidden\'>(' . self::dic()->language()->txt('stat_selected') . ')</span>');
		} else {
			$this->html->setVariable('MM_CLASS', ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_INACTIVE));
		}

		$this->accessKey();

		$html = $this->html->get();

		return $html;
	}


	public function getDropdownId($post_fix = 'tr') {
		return 'mm_' . $this->entry->getId() . '_' . $post_fix;
	}


	/**
	 * Render main menu entry
	 *
	 * @param
	 *
	 * @return string html
	 */
	abstract protected function setGroupedListContent();


	protected function accessKey() {
	}


	/**
	 * @return string
	 */
	protected function getContent() {
		return $this->gl->getHTML();
	}
}
