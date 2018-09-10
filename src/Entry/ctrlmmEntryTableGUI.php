<?php

namespace srag\Plugins\CtrlMainMenu\Entry;

use ilAdvancedSelectionListGUI;
use ilCtrl;
use ilCtrlMainMenuConfigGUI;
use ilCtrlMainMenuPlugin;
use ilLinkButton;
use ilTable2GUI;
use ilTabsGUI;
use srag\DIC\DICTrait;
use srag\Plugins\CtrlMainMenu\EntryInstaceFactory\ctrlmmEntryInstaceFactory;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu;

/**
 * TableGUI ctrlmmEntryTableGUI
 *
 * @package srag\Plugins\CtrlMainMenu\Entry
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryTableGUI extends ilTable2GUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;


	/**
	 * @param ilCtrlMainMenuConfigGUI $a_parent_obj
	 * @param string                  $a_parent_cmd
	 * @param int                     $parent_id
	 */
	public function __construct(ilCtrlMainMenuConfigGUI $a_parent_obj, $a_parent_cmd, $parent_id = 0) {
		$this->setId('mm_entry_list');
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->setRowTemplate('tpl.entry_list.html', self::plugin()->directory());
		$this->setTitle(self::plugin()->translate('list_title'));
		//
		// Columns
		$this->addColumn('', 'position', '20px');
		$this->addColumn(self::plugin()->translate('common_title'), 'title', 'auto');
		$this->addColumn(self::plugin()->translate('common_type'), 'type', 'auto');
		$this->addColumn(self::plugin()->translate('common_actions'), 'actions', '100px');
		// ...
		// Header
		$button = ilLinkButton::getInstance();
		$button->setCaption(self::plugin()->translate('add_new'), false);
		$button->setUrl(self::dic()->ctrl()->getLinkTarget($a_parent_obj, ilCtrlMainMenuConfigGUI::CMD_SELECT_ENTRY_TYPE));
		self::dic()->toolbar()->addButtonInstance($button);
		$this->setFormAction(self::dic()->ctrl()->getFormAction($a_parent_obj));
		$this->addCommandButton(ilCtrlMainMenuConfigGUI::CMD_SAVE_SORTING, self::plugin()->translate('save_sorting'));
		// $this->setExternalSorting(true);
		// $this->setExternalSegmentation(true);s
		$this->setLimit(500);

		ctrlmmMenu::includeAllTypes();
		$this->setData(ctrlmmEntryInstaceFactory::getAllChildsForIdAsArray($parent_id));
	}


	/**
	 * @var int
	 */
	protected static $num = 1;


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		/**
		 * @var ctrlmmEntry $obj
		 */
		$obj = ctrlmmEntryInstaceFactory::getInstanceByEntryId($a_set['id'])->getObject();

		if ($obj->getTypeId() == ctrlmmMenu::TYPE_SEPARATOR) {
			$this->tpl->setVariable('CLASS', 'ctrlmmSeparator');
		}

		$this->tpl->setVariable('TITLE', $obj->getTitleInAdministration() . ' ' . ($obj->checkPermission() ? '' : '*'));
		$this->tpl->setVariable('TYPE', ctrlmmEntryInstaceFactory::getClassAppendForValue($obj->getTypeId()));
		self::dic()->ctrl()->setParameter($this->parent_obj, 'entry_id', $obj->getId());
		$this->tpl->setVariable('ID_NEW', $obj->getId());
		if (!$obj->getPlugin()) {

			$actions = new ilAdvancedSelectionListGUI();
			$actions->setId('actions_' . $obj->getId());
			$actions->setListTitle(self::plugin()->translate('common_actions'));
			if ($obj->getTypeId() != ctrlmmMenu::TYPE_SEPARATOR) {
				$actions->addItem(self::plugin()->translate('common_edit'), 'edit', self::dic()->ctrl()->getLinkTarget($this->parent_obj, ilCtrlMainMenuConfigGUI::CMD_EDIT_ENTRY));
				//				$actions->addItem(self::plugin()->translate('common_edit'), 'edit', self::dic()->ctrl()->getLinkTargetByClass(ctrlmmEntryGUI::class, ilCtrlMainMenuConfigGUI::CMD_EDIT_ENTRY)); FSX TODO: REFACTORING
			}
			if ($obj->getTypeId() != ctrlmmMenu::TYPE_ADMIN) {
				$actions->addItem(self::plugin()->translate('common_delete'), 'delete', self::dic()->ctrl()->getLinkTarget($this->parent_obj, ilCtrlMainMenuConfigGUI::CMD_DELETE_ENTRY));
			}
			if ($obj->getTypeId() == ctrlmmMenu::TYPE_DROPDOWN) {
				$actions->addItem(self::plugin()->translate('edit_childs'), 'edit_childs', self::dic()->ctrl()->getLinkTarget($this->parent_obj, ilCtrlMainMenuConfigGUI::CMD_EDIT_CHILDS));
			}
			$this->tpl->setVariable('ACTIONS', $actions->getHTML());
		}
	}
}
