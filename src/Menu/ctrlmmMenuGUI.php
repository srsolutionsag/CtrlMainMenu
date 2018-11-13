<?php

namespace srag\Plugins\CtrlMainMenu\Menu;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
//MST 20131130: I commented out the following line because of problems with ILIAS Modules which use include instead of include_once
use ilCtrlMainMenuPlugin;
use ilTemplate;
use srag\DIC\CtrlMainMenu\DICTrait;
use srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\Plugins\CtrlMainMenu\EntryInstaceFactory\ctrlmmEntryInstaceFactory;

/**
 * User interface hook class
 *
 * @package           srag\Plugins\CtrlMainMenu\Menu
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @author            Martin Studer <ms@studer-raimann.ch>
 * @version           2.0.02
 * @ingroup           ServicesUIComponent
 *
 */
class ctrlmmMenuGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;
	const SIDE_LEFT = 1;
	const SIDE_RIGHT = 2;
	/**
	 * @var ilTemplate
	 */
	protected $html;
	/**
	 * @var int
	 */
	protected $side = self::SIDE_LEFT;
	protected $css_id = '';
	/**
	 * @var ctrlmmMenu
	 */
	protected $object;


	/**
	 * @return mixed
	 */
	public function getCssId() {
		return $this->css_id;
	}


	/**
	 * @param mixed $css_id
	 */
	public function setCssId($css_id) {
		$this->css_id = $css_id;
	}


	/**
	 * @param int $id
	 */
	public function __construct($id = 0) {
		$this->object = new ctrlmmMenu($id);

		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/css/ctrlmm.css');
		if (ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_PREFIX) == 'fb') {
			self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/css/fb.css');
		}
		if (ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_SIMPLE_FORM_VALIDATION)) {
			self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/css/forms.css');
			self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/templates/js/forms.js');
		}
		if (ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_DOUBLECLICK_PREVENTION)) {
			self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/css/click.css');
			self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/templates/js/click.js');
		}
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->html = self::plugin()->template('tpl.ctrl_menu.html');
		$entry_before_html = '';
		$entry_after_html = '';
		$replace_full = ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_REPLACE_FULL_HEADER);
		/**
		 * @var ctrlmmEntry $entry
		 */

		foreach ($this->object->getEntries() as $k => $entry) {
			//			var_dump($entry->getType());
			//			var_dump($this->object->getAfterSeparator());
			if ($entry->getTypeId() == ctrlmmMenu::TYPE_SEPARATOR) {
				//				if ($replace_full) {
				$this->object->setAfterSeparator(true);
				//				}
				continue;
			}
			if ($this->object->getAfterSeparator() AND $this->getSide() == self::SIDE_LEFT && $replace_full) {
				continue;
			}

			if (!$this->object->getAfterSeparator() AND $this->getSide() == self::SIDE_RIGHT && $replace_full) {
				continue;
			}

			if ($entry->checkPermission()) {
				if ($entry->getId() == 0) {
					$gui_class = ctrlmmEntryInstaceFactory::getInstanceByTypeId($entry->getTypeId())->getGUIObjectClass();
					$entryGui = new $gui_class($entry, $this);
				} else {
					$entryGui = ctrlmmEntryInstaceFactory::getInstanceByEntryId($entry->getId())->getGUIObject();
				}

				if (!$this->object->getAfterSeparator()) {
					$entry_before_html .= $entryGui->prepareAndRenderEntry('ctrl_mm_e_' . $entry->getParent() . '_' . $k);
				} else {
					$entry_after_html .= $entryGui->prepareAndRenderEntry('ctrl_mm_e_' . $entry->getParent() . '_' . $k);
				}
			}
		}

		$this->html->setVariable('BEFORE_ENTRIES', $entry_before_html);
		$this->html->setVariable('AFTER_ENTRIES', $entry_after_html);
		$this->html->setVariable('CSS_PREFIX', ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_PREFIX));
		$this->html->setVariable('ID', $this->css_id);

		return $this->html->get();
	}


	/**
	 * @param int $side
	 */
	public function setSide($side) {
		$this->side = $side;
	}


	public function setLeft() {
		$this->setSide(self::SIDE_LEFT);
	}


	public function setRight() {
		$this->setSide(self::SIDE_RIGHT);
	}


	/**
	 * @return int
	 */
	public function getSide() {
		return $this->side;
	}
}
