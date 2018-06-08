<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ctrlmmEntrySearchGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntrySearchGUI extends ctrlmmEntryGUI {

	/**
	 * @param string $entry_div_id
	 *
	 * @return string
	 */
	public function renderEntry($entry_div_id = '') {
		unset($entry_div_id);
		$main_search = new ilMainMenuSearchGUI();
		$this->html = $this->pl->getTemplate('tpl.search_entry.html', false, false);
		$this->html->setVariable('DROPDOWN', str_ireplace(ilMainMenuSearch::class, ctrlmmMenu::getCssPrefix()
			. ilMainMenuSearch::class, $main_search->getHTML()));
		$this->html->setVariable('CSS_PREFIX', ctrlmmMenu::getCssPrefix());

		return $this->html->get();
	}
}


