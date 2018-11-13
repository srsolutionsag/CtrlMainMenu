<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\CtrlMainMenu\DICTrait;
use srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntryTableGUI;
use srag\Plugins\CtrlMainMenu\EntryInstaceFactory\ctrlmmEntryInstaceFactory;
use srag\Plugins\CtrlMainMenu\EntryTypes\Ctrl\ctrlmmEntryCtrlFormGUI;
use srag\Plugins\CtrlMainMenu\EntryTypes\Ctrl\ctrlmmEntryCtrlGUI;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu;

/**
 * CtrlMainMenu Configuration
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Michael Herren <mh@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ilCtrlMainMenuConfigGUI extends ilPluginConfigGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;
	const CMD_ADD_ENTRY = 'addEntry';
	const CMD_CACHE_SETTINGS = 'cacheSettings';
	const CMD_CLEAR_CACHE = 'clearCache';
	const CMD_CSS_SETTINGS = 'cssSettings';
	const CMD_CONFIGURE = 'configure';
	const CMD_CREATE_ENTRY = 'createEntry';
	const CMD_CREATE_OBJECT = 'createObject';
	const CMD_CREATE_OBJECT_AND_STAY = 'createObjectAndStay';
	const CMD_DELETE_OBJECT = 'deleteObject';
	const CMD_EDIT_CHILDS = 'editChilds';
	const CMD_EDIT_ENTRY = 'editEntry';
	const CMD_DELETE_ENTRY = 'deleteEntry';
	const CMD_RESET_PARENT = 'resetParent';
	const CMD_SAVE = 'save';
	const CMD_SAVE_SORTING = 'saveSorting';
	const CMD_SAVE_SORTING_OLD = 'saveSortingOld';
	const CMD_SELECT_ENTRY_TYPE = 'selectEntryType';
	const CMD_UPDATE_CACHE_SETTINGS = 'updateCacheSettings';
	const CMD_UPDATE_OBJECT = 'updateObject';
	const CMD_UPDATE_OBJECT_AND_STAY = 'updateObjectAndStay';
	const TAB_CACHE = 'cache';
	const TAB_CSS = 'css';
	const TAB_DROPDOWN = 'child_admin';
	const TAB_MAIN = 'mm_admin';
	/**
	 *
	 * @var array
	 */
	protected $fields = array();
	/**
	 * @var ilPropertyFormGUI
	 */
	protected $form;


	public function __construct() {
		if ($_GET['rl']) {
			self::plugin()->getPluginObject()->updateLanguages();
		}
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/templates/js/sortable.js');

		ctrlmmMenu::includeAllTypes();
	}


	public function executeCommand() {
		$next_class = self::dic()->ctrl()->getNextClass();
		switch (strtolower($next_class)) {
			case strtolower(ctrlmmEntryGUI::class):
				$entrygui = ctrlmmEntryInstaceFactory::getInstanceByEntryId($_GET['entry_id'])->getGUIObject($this);
				self::dic()->ctrl()->forwardCommand($entrygui);
				break;
			default:
				parent::executeCommand();
				break;
		}
	}


	/**
	 * @return array
	 */
	public function getFields() {
		$this->fields = array(
			'css_prefix' => array(
				'type' => ilTextInputGUI::class,
			),
			'css_active' => array(
				'type' => ilTextInputGUI::class,
			),
			'css_inactive' => array(
				'type' => ilTextInputGUI::class,
			),
			'doubleclick_prevention' => array(
				'type' => ilCheckboxInputGUI::class,
			),
			'simple_form_validation' => array(
				'type' => ilCheckboxInputGUI::class,
			),
			'replace_full_header' => array(
				'type' => ilCheckboxInputGUI::class,
			),
		);

		return $this->fields;
	}


	/**
	 * Handles all commmands, default is self::CMD_CONFIGURE
	 */
	function performCommand($cmd) {
		self::dic()->ctrl()->setParameter($this, 'parent_id', $_GET['parent_id'] ? $_GET['parent_id'] : 0);
		if ($_GET['parent_id'] > 0) {
			self::dic()->tabs()->addTab(self::TAB_MAIN, self::plugin()->translate('back_to_main'), self::dic()->ctrl()
				->getLinkTarget($this, self::CMD_RESET_PARENT));
			self::dic()->tabs()->addTab(self::TAB_DROPDOWN, self::plugin()->translate('tabs_title_childs'), self::dic()->ctrl()
				->getLinkTarget($this, self::CMD_CONFIGURE));
			self::dic()->tabs()->activateTab(self::TAB_DROPDOWN);
		} else {
			self::dic()->tabs()->addTab(self::TAB_MAIN, self::plugin()->translate('tab_main'), self::dic()->ctrl()
				->getLinkTarget($this, self::CMD_CONFIGURE));
			self::dic()->tabs()->activateTab(self::TAB_MAIN);
		}
		self::dic()->tabs()->addTab(self::TAB_CSS, self::plugin()->translate('css_settings'), self::dic()->ctrl()
			->getLinkTarget($this, self::CMD_CSS_SETTINGS));
		self::dic()->tabs()->addTab(self::TAB_CACHE, self::plugin()->translate('cache_settings'), self::dic()->ctrl()
			->getLinkTarget($this, self::CMD_CACHE_SETTINGS));
		switch ($cmd) {
			case self::CMD_CONFIGURE:
			case self::CMD_SAVE:
			case self::CMD_SAVE_SORTING:
			case self::CMD_ADD_ENTRY:
			case self::CMD_CREATE_ENTRY:
			case self::CMD_SELECT_ENTRY_TYPE:
			case self::CMD_CLEAR_CACHE:
			case self::CMD_RESET_PARENT:
			case self::CMD_CSS_SETTINGS:
			case self::CMD_EDIT_ENTRY:
			case self::CMD_DELETE_ENTRY:
			case self::CMD_EDIT_CHILDS:
			case self::CMD_UPDATE_CACHE_SETTINGS:
			case self::CMD_CREATE_OBJECT:
			case self::CMD_CREATE_OBJECT_AND_STAY:
			case self::CMD_UPDATE_OBJECT:
			case self::CMD_UPDATE_OBJECT_AND_STAY:
			case self::CMD_DELETE_OBJECT:
				$this->$cmd();
				break;
			default:
				$this->$cmd();
				break;
		}
	}


	public function clearCache() {
		ilGlobalCache::flushAll();
		ilUtil::sendInfo(self::plugin()->translate('cache_cleared'), true);
		self::dic()->ctrl()->redirect($this, self::CMD_CACHE_SETTINGS);
	}


	protected function cacheSettings() {
		$button = ilLinkButton::getInstance();
		$button->setCaption(self::plugin()->translate('clear_cache'), false);
		$button->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_CLEAR_CACHE));
		self::dic()->toolbar()->addButtonInstance($button);
		self::dic()->tabs()->activateTab(self::TAB_CACHE);
		$form = new ilPropertyFormGUI();
		$form->setTitle(self::plugin()->translate('cache_settings'));
		$form->setFormAction(self::dic()->ctrl()->getFormAction($this));

		$cb = new ilCheckboxInputGUI(self::plugin()->translate('activate_cache'), 'activate_cache');
		$cb->setInfo(self::plugin()->translate('activate_cache_info'));
		$form->addItem($cb);
		$form->setValuesByArray(array( 'activate_cache' => ilCtrlMainMenuConfig::getConfigValue('activate_cache') ));
		$form->addCommandButton(self::CMD_UPDATE_CACHE_SETTINGS, self::plugin()->translate('update_cache_settings'));

		self::dic()->mainTemplate()->setContent($form->getHTML());
	}


	protected function updateCacheSettings() {
		ilCtrlMainMenuConfig::set('activate_cache', $_POST['activate_cache']);
		ilUtil::sendInfo(self::plugin()->translate('cache_settings_updated'), true);
		self::dic()->ctrl()->redirect($this, self::CMD_CACHE_SETTINGS);
	}


	protected function cssSettings() {
		self::dic()->tabs()->activateTab(self::TAB_CSS);
		$this->initConfigurationForm();
		$this->getValues();
		self::dic()->mainTemplate()->setContent($this->form->getHTML());
	}


	public function editChilds() {
		self::dic()->ctrl()->setParameter($this, 'parent_id', $_GET['entry_id']);
		self::dic()->ctrl()->redirect($this, self::CMD_CONFIGURE);
	}


	public function configure() {
		$table = new ctrlmmEntryTableGUI($this, self::CMD_CONFIGURE, $_GET['parent_id'] ? $_GET['parent_id'] : 0);
		self::dic()->mainTemplate()->setContent($table->getHTML());
	}


	public function resetParent() {
		self::dic()->ctrl()->setParameter($this, 'parent_id', 0);
		self::dic()->ctrl()->redirect($this, self::CMD_CONFIGURE);
	}


	public function saveSorting() {
		foreach ($_POST['position'] as $k => $v) {
			$obj = ctrlmmEntryInstaceFactory::getInstanceByEntryId($v)->getObject();
			if ($obj instanceof ctrlmmEntry) {
				$obj->setPosition($k);
				$obj->update();
			}
		}
		ilUtil::sendSuccess(self::plugin()->translate('sorting_saved'));
		self::dic()->ctrl()->redirect($this);
	}


	public function saveSortingOld() {
		foreach ($_POST['id'] as $k => $v) {
			$obj = ctrlmmEntryInstaceFactory::getInstanceByEntryId($k)->getObject();
			$obj->setPosition($v);
			$obj->update();
		}
		ilUtil::sendSuccess(self::plugin()->translate('sorting_saved'));
		self::dic()->ctrl()->redirect($this);
	}


	public function selectEntryType() {
		$select = new ilPropertyFormGUI();
		$select->setFormAction(self::dic()->ctrl()->getFormAction($this));
		$select->setTitle(self::plugin()->translate('select_type'));
		$se = new ilSelectInputGUI(self::plugin()->translate('common_type'), 'type');
		$se->setOptions(ctrlmmMenu::getAllTypesAsArray(true, $_GET['parent_id']));
		$select->addItem($se);
		$select->addCommandButton(self::CMD_ADD_ENTRY, self::plugin()->translate('common_select'));
		$select->addCommandButton(self::CMD_CONFIGURE, self::plugin()->translate('common_cancel'));
		self::dic()->mainTemplate()->setContent($select->getHTML());
	}


	public function addEntry() {
		/**
		 * @var ctrlmmEntryCtrlGUI $entry_gui
		 */
		$type_id = $_POST['type'] ? $_POST['type'] : $_GET['type'];
		self::dic()->ctrl()->setParameter($this, 'type', $type_id);
		$entry_gui = ctrlmmEntryInstaceFactory::getInstanceByTypeId($type_id)->getGUIObject($this);
		$entry_gui->initForm();
		$entry_gui->setFormValuesByArray();
		self::dic()->mainTemplate()->setContent($entry_gui->form->getHTML());
	}


	public function createObjectAndStay() {
		$this->createObject(false);
		$this->editEntry();
	}


	public function createObject($redirect = true) {
		$type_id = $_POST['type'] ? $_POST['type'] : $_GET['type'];
		$entry_gui = ctrlmmEntryInstaceFactory::getInstanceByTypeId($type_id)->getGUIObject($this);
		$entry_gui->initForm();
		if ($entry_gui->form->checkInput()) {
			$entry_gui->createEntry();
			ilUtil::sendSuccess(self::plugin()->translate('entry_added'), $redirect);
			if ($redirect) {
				self::dic()->ctrl()->redirect($this);
			}
		}
		$entry_gui->form->setValuesByPost();
		self::dic()->mainTemplate()->setContent($entry_gui->form->getHTML());
	}


	public function editEntry() {
		/**
		 * @var ctrlmmEntryCtrlGUI     $entry_gui
		 * @var ctrlmmEntryCtrlFormGUI $entry_formgui
		 */
		self::dic()->ctrl()->saveParameter($this, 'entry_id');
		$entry_gui = ctrlmmEntryInstaceFactory::getInstanceByEntryId($_GET['entry_id'])->getGUIObject($this);
		$entry_gui->initForm('update');
		$entry_gui->setFormValuesByArray();
		self::dic()->mainTemplate()->setContent($entry_gui->form->getHTML());
	}


	public function updateObjectAndStay() {
		$this->updateObject(false);
		$this->editEntry();
	}


	/**
	 * @param bool $redirect
	 */
	public function updateObject($redirect = true) {
		/**
		 * @var ctrlmmEntryCtrlGUI $entry_gui
		 */
		$entry_gui = ctrlmmEntryInstaceFactory::getInstanceByEntryId($_GET['entry_id'])->getGUIObject($this);
		$entry_gui->initForm('update');
		if ($entry_gui->form->checkInput()) {
			$entry_gui->createEntry();
			ilUtil::sendSuccess(self::plugin()->translate('entry_updated'), $redirect);
			if ($redirect) {
				self::dic()->ctrl()->redirect($this);
			}
		}
		$entry_gui->form->setValuesByPost();
		self::dic()->mainTemplate()->setContent($entry_gui->form->getHTML());
	}


	public function deleteEntry() {
		$entry = ctrlmmEntryInstaceFactory::getInstanceByEntryId($_GET['entry_id'])->getObject();
		$conf = new ilConfirmationGUI();
		ilUtil::sendQuestion(self::plugin()->translate('qst_delete_entry'));
		$conf->setFormAction(self::dic()->ctrl()->getFormAction($this));
		$conf->setConfirm(self::plugin()->translate('common_delete'), self::CMD_DELETE_OBJECT);
		$conf->setCancel(self::plugin()->translate('common_cancel'), self::CMD_CONFIGURE);
		$conf->addItem('entry_id', $_GET['entry_id'], $entry->getTitle());
		self::dic()->mainTemplate()->setContent($conf->getHTML());
	}


	public function deleteObject() {
		/**
		 * @var ctrlmmEntry $entry
		 */
		$entry = ctrlmmEntryInstaceFactory::getInstanceByEntryId($_POST['entry_id'])->getObject();
		$entry->delete();

		ilUtil::sendSuccess(self::plugin()->translate('entry_deleted'));
		self::dic()->ctrl()->redirect($this, self::CMD_CONFIGURE);
	}


	//
	// Default Configuration
	//
	public function getValues() {
		foreach ($this->getFields() as $key => $item) {
			$values[$key] = ilCtrlMainMenuConfig::getConfigValue($key);
			if (is_array($item['subelements'])) {
				foreach ($item['subelements'] as $subkey => $subitem) {
					$values[$key . '_' . $subkey] = ilCtrlMainMenuConfig::getConfigValue($key . '_' . $subkey);
				}
			}
		}
		$this->form->setValuesByArray($values);
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	public function initConfigurationForm() {
		$this->form = new ilPropertyFormGUI();
		foreach ($this->getFields() as $key => $item) {
			$field = new $item['type'](self::plugin()->translate($key), $key);
			if ($item['info']) {
				$field->setInfo(self::plugin()->translate($key . '_info'));
			}
			if (is_array($item['subelements'])) {
				foreach ($item['subelements'] as $subkey => $subitem) {
					$subfield = new $subitem['type'](self::plugin()->translate($key . '_' . $subkey), $key . '_' . $subkey);
					if ($subitem['info']) {
						$subfield->setInfo(self::plugin()->translate($key . '_info'));
					}
					$field->addSubItem($subfield);
				}
			}
			$this->form->addItem($field);
		}
		$this->form->addCommandButton(self::CMD_SAVE, self::dic()->language()->txt('save'));
		$this->form->setTitle(self::plugin()->translate('common_configuration'));
		$this->form->setFormAction(self::dic()->ctrl()->getFormAction($this));

		return $this->form;
	}


	public function save() {
		$this->initConfigurationForm();
		if ($this->form->checkInput()) {
			foreach ($this->getFields() as $key => $item) {
				ilCtrlMainMenuConfig::set($key, $this->form->getInput($key));
				if (is_array($item['subelements'])) {
					foreach ($item['subelements'] as $subkey => $subitem) {
						ilCtrlMainMenuConfig::set($key . '_' . $subkey, $this->form->getInput($key . '_' . $subkey));
					}
				}
			}
			ilUtil::sendSuccess(self::plugin()->translate('conf_saved'), true);
			self::dic()->ctrl()->redirect($this, self::CMD_CSS_SETTINGS);
		} else {
			$this->form->setValuesByPost();
			self::dic()->mainTemplate()->setContent($this->form->getHtml());
		}
	}
}
