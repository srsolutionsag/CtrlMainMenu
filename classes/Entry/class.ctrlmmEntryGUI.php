<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\Plugins\CtrlMainMenu\EntryInstaceFactory\ctrlmmEntryInstaceFactory;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * User interface hook class
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           2.0.02
 * @ingroup           ServicesUIComponent
 *
 * @ilCtrl_IsCalledBy ctrlmmEntryGUI: ilAdministrationGUI, ilPersonalDesktopGUI, ilRepositoryGUI, ilCtrlMainMenuConfigGUI
 */
class ctrlmmEntryGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;
	/**
	 * @var ilTemplate
	 */
	protected $html;
	/**
	 * @var ctrlmmEntry
	 */
	protected $entry;
	/**
	 * @var ilPropertyFormGUI
	 */
	public $form;
	/**
	 * @var
	 */
	protected $parent_gui;


	/**
	 * @param ctrlmmEntry $entry
	 * @param null        $parent_gui
	 */
	public function __construct(ctrlmmEntry $entry, $parent_gui = NULL) {
		$this->entry = $entry;
		$this->parent_gui = $parent_gui;
	}


	public function executeCommand() {
		$cmd = self::dic()->ctrl()->getCmd('index');
		$this->$cmd();
	}


	public function edit() {
		$form = ctrlmmEntryInstaceFactory::getInstanceByEntryId($this->entry->getId())->getFormObject($this);
		$form->fillForm();
		self::dic()->template()->setContent($form->getHTML());
	}


	public function update() {
	}


	public function add() {
	}


	public function create() {
	}


	public function confirmDelete() {
	}


	public function delete() {
	}


	/**
	 * @param string $entry_div_id
	 *
	 * @return string
	 */
	public function renderEntry($entry_div_id = '') {
		$this->html = self::plugin()->template('tpl.ctrl_menu_entry.html', true, true);
		$this->html->setVariable('TITLE', $this->entry->getTitle());
		$this->html->setVariable('CSS_ID', ($entry_div_id) ? $entry_div_id : 'ctrl_mm_e_' . $this->entry->getId());
		$this->html->setVariable('LINK', $this->entry->getLink());
		$this->html->setVariable('CSS_PREFIX', ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_PREFIX));
		$this->html->setVariable('TARGET', $this->entry->getTarget());
		$cssActive = ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_ACTIVE);
		$cssInactive = ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_INACTIVE);
		$this->html->setVariable('STATE', ($this->entry->isActive() ? $cssActive : $cssInactive));

		return $this->html->get();
	}


	/**
	 * @param string $entry_div_id If set, the value is used to construct the unique ID of the entry (HTML)
	 *
	 * @return string
	 */
	public function prepareAndRenderEntry($entry_div_id = '') {
		$this->entry->replacePlaceholders();

		return $this->renderEntry($entry_div_id);
	}


	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		self::dic()->language()->loadLanguageModule('meta');
		$this->form = new ilPropertyFormGUI();
		$this->initPermissionSelectionForm();
		$te = new ilFormSectionHeaderGUI();
		$te->setTitle(self::plugin()->translate('common_title'));
		$this->form->addItem($te);
		$this->form->setTitle(self::plugin()->translate('form_title'));
		$this->form->setFormAction(self::dic()->ctrl()->getFormAction($this->parent_gui));
		foreach (ctrlmmEntry::getAllLanguageIds() as $language) {
			$te = new ilTextInputGUI(self::dic()->language()->txt('meta_l_' . $language), 'title_' . $language);
			$te->setRequired(ctrlmmEntry::isDefaultLanguage($language));
			$this->form->addItem($te);
		}
		$type = new ilHiddenInputGUI('type');
		$type->setValue($this->entry->getTypeId());
		$this->form->addItem($type);
		$link = new ilHiddenInputGUI('link');
		$this->form->addItem($link);

		if (count(ctrlmmEntry::getAdditionalFieldsAsArray($this->entry)) > 0) {
			$te = new ilFormSectionHeaderGUI();
			$te->setTitle(self::plugin()->translate('common_settings'));
			$this->form->addItem($te);
		}
		$this->form->addCommandButton($mode . 'Object', self::plugin()->translate('common_create'));
		if ($mode != 'create') {
			$this->form->addCommandButton(ilCtrlMainMenuConfigGUI::CMD_UPDATE_OBJECT_AND_STAY, self::plugin()->translate('create_and_stay'));
		}
		$this->form->addCommandButton(ilCtrlMainMenuConfigGUI::CMD_CONFIGURE, self::plugin()->translate('common_cancel'));
	}


	/**
	 * @return array
	 */
	public function setFormValuesByArray() {
		$values = array();
		foreach ($this->entry->getTranslations() as $k => $v) {
			$values['title_' . $k] = $v;
		}
		$perm_type = $this->entry->getPermissionType();
		$values['permission_type'] = $perm_type;

		if ($perm_type == ctrlmmMenu::PERM_SCRIPT) {
			$perm_settings = json_decode($this->entry->getPermission());
			$values['perm_input_script_path'] = $perm_settings[0];
			$values['perm_input_script_class'] = $perm_settings[1];
			$values['perm_input_script_method'] = $perm_settings[2];
		} else {
			$role_ids = json_decode($this->entry->getPermission());
			$roles_global = @array_intersect($role_ids, self::getRoles(ilRbacReview::FILTER_ALL_GLOBAL, false));
			$roles_local = @array_intersect($role_ids, self::getRoles(ilRbacReview::FILTER_ALL_LOCAL, false));
			//$roles_local = @array_diff($role_ids, $roles_global); // Bessere Variante, da auch falsche vorhanden
			$values['permission_' . $perm_type] = $roles_global;
			$values['permission_locale_' . $perm_type] = @implode(',', $roles_local); // Variante Textfeld
			// $values['permission_locale_' . $perm_type] = $roles_local; // Variante MultiSelect
			$role_ids_as_string = '';
			if (is_array($role_ids) AND count($role_ids) > 0) {
				$role_ids_as_string = implode(',', $role_ids);
			}
		}

		$values['permission_user_' . $perm_type] = $role_ids_as_string;
		$values['type'] = $this->entry->getTypeId();
		$this->form->setValuesByArray($values);

		return $values;
	}


	/**
	 * @param int  $filter
	 * @param bool $with_text
	 *
	 * @deprecated
	 * @return array
	 */
	public static function getRoles($filter, $with_text = true) {
		$opt = array();
		$role_ids = array();
		foreach (self::dic()->rbacreview()->getRolesByFilter($filter) as $role) {
			$opt[$role['obj_id']] = $role['title'] . ' (' . $role['obj_id'] . ')';
			$role_ids[] = $role['obj_id'];
		}
		if ($with_text) {
			return $opt;
		} else {
			return $role_ids;
		}
	}


	private function initPermissionSelectionForm() {
		$global_roles = self::getRoles(ilRbacReview::FILTER_ALL_GLOBAL);
		$locale_roles = self::getRoles(ilRbacReview::FILTER_ALL_LOCAL);
		$ro = new ilRadioGroupInputGUI(self::plugin()->translate('permission_type'), 'permission_type');
		$ro->setRequired(true);
		foreach (ctrlmmMenu::getAllPermissionsAsArray() as $k => $v) {
			$option = new ilRadioOption($v, $k);
			switch ($k) {
				case ctrlmmMenu::PERM_NONE :
					break;
				case ctrlmmMenu::PERM_ROLE :
				case ctrlmmMenu::PERM_ROLE_EXEPTION :
					$se = new ilMultiSelectInputGUI(self::plugin()->translate('perm_input'), 'permission_' . $k);
					$se->setWidth(400);
					$se->setOptions($global_roles);
					$option->addSubItem($se);
					// Variante mit MultiSelection
					$se = new ilMultiSelectInputGUI(self::plugin()->translate('perm_input_locale'), 'permission_locale_' . $k);
					$se->setWidth(400);
					$se->setOptions($locale_roles);
					// $option->addSubItem($se);
					// Variante mit TextInputGUI
					$te = new ilTextInputGUI(self::plugin()->translate('perm_input_locale'), 'permission_locale_' . $k);
					$te->setInfo(self::plugin()->translate('perm_input_locale_info'));
					$option->addSubItem($te);
					break;
				case ctrlmmMenu::PERM_REF_WRITE :
				case ctrlmmMenu::PERM_REF_READ :
					$te = new ilTextInputGUI(self::plugin()->translate('perm_input'), 'permission_' . $k);
					$option->addSubItem($te);
					break;
				case ctrlmmMenu::PERM_USERID :
					$te = new ilTextInputGUI(self::plugin()->translate('perm_input_user'), 'permission_user_' . $k);
					$te->setInfo(self::plugin()->translate('perm_input_user_info'));
					$option->addSubItem($te);
					break;
				case ctrlmmMenu::PERM_SCRIPT :
					$te = new ilTextInputGUI(self::plugin()->translate('perm_input_script_path'), 'perm_input_script_path');
					$option->addSubItem($te);
					$te = new ilTextInputGUI(self::plugin()->translate('perm_input_script_class'), 'perm_input_script_class');
					$option->addSubItem($te);
					$te = new ilTextInputGUI(self::plugin()->translate('perm_input_script_method'), 'perm_input_script_method');
					$option->addSubItem($te);
					break;
			}
			$ro->addOption($option);
		}
		$this->form->addItem($ro);
	}


	public function createEntry() {
		$lngs = array();
		foreach (ctrlmmEntry::getAllLanguageIds() as $lng) {
			if ($this->form->getInput('title_' . $lng)) {
				$lngs[$lng] = $this->form->getInput('title_' . $lng);
			}
		}
		$perm_type = $this->form->getInput('permission_type');
		$this->entry->setParent($_GET['parent_id']);
		$this->entry->setTranslations($lngs);
		$this->entry->setTypeId($this->form->getInput('type'));
		$this->entry->setPermissionType($perm_type);
		if ($this->form->getInput('permission_locale_' . $perm_type)) {
			$permission = array_merge(explode(',', $this->form->getInput('permission_locale_'
				. $perm_type)), (array)$this->form->getInput('permission_' . $perm_type)); // Variante Textfeld
			/*$permission = array_merge((array)$this->form->getInput('permission_locale_'
				. $perm_type), (array)$this->form->getInput('permission_' . $perm_type));*/
		} elseif ($this->form->getInput('permission_user_' . $perm_type)) {
			$permission = explode(',', $this->form->getInput('permission_user_' . $perm_type));
		} elseif ($this->form->getInput('permission_type') == ctrlmmMenu::PERM_SCRIPT) {
			$permission = array(
				0 => $this->form->getInput('perm_input_script_path'),
				1 => $this->form->getInput('perm_input_script_class'),
				2 => $this->form->getInput('perm_input_script_method'),
			);
		} else {
			$permission = (array)$this->form->getInput('permission_' . $perm_type);
		}
		$this->entry->setPermission(json_encode($permission));
		$this->entry->create();
	}


	/**
	 * @return bool
	 * @deprecated
	 */
	public function isActive() {
		return $this->entry->isActive();
	}


	/**
	 * @deprecated
	 */
	public static function includeAllEntryTypeGUIs() {
		foreach (scandir(dirname(__FILE__) . '/EntryTypes/') as $file) {
			if (strpos($file, 'GUI.php')) {
				require_once(dirname(__FILE__) . '/EntryTypes/' . $file);
			}
		}
	}
}
