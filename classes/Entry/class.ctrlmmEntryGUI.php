<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ilCtrlMainMenuPlugin.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Menu/class.ctrlmmMenu.php');
require_once('class.ctrlmmEntry.php');
require_once('./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php');
require_once('./Services/Form/classes/class.ilMultiSelectInputGUI.php');

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
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilCtrlMainMenuPlugin
	 */
	protected $pl;
	/**
	 * @var
	 */
	protected $parent_gui;
	/**
	 * @var ilLanguage
	 */
	protected $lng;
	/**
	 * @var ilObjUser
	 */
	protected $usr;
	/**
	 * @var ilSetting
	 */
	protected $settings;
	/**
	 * @var ilRbacSystem
	 */
	protected $rbacsystem;
	/**
	 * @var
	 */
	protected $ilias;
	/**
	 * @var ilNavigationHistory
	 */
	protected $ilNavigationHistory;


	/**
	 * @param ctrlmmEntry $entry
	 * @param null        $parent_gui
	 */
	public function __construct(ctrlmmEntry $entry, $parent_gui = NULL) {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->tpl = $DIC->ui()->mainTemplate();
		$this->pl = ilCtrlMainMenuPlugin::getInstance();
		$this->lng = $DIC->language();
		$this->usr = $DIC->user();
		$this->settings = $DIC["ilSetting"];
		$this->rbacsystem = $DIC->rbac()->system();
		$this->ilias = $DIC["ilias"];
		$this->ilNavigationHistory = $DIC["ilNavigationHistory"];
		$this->entry = $entry;
		$this->parent_gui = $parent_gui;
	}


	public function executeCommand() {
		$cmd = $this->ctrl->getCmd('index');
		$this->$cmd();
	}


	public function edit() {
		$form = ctrlmmEntryInstaceFactory::getInstanceByEntryId($this->entry->getId())->getFormObject($this);
		$form->fillForm();
		$this->tpl->setContent($form->getHTML());
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
		$this->html = $this->pl->getTemplate('tpl.ctrl_menu_entry.html', true, true);
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
		$this->lng->loadLanguageModule('meta');
		$this->form = new ilPropertyFormGUI();
		$this->initPermissionSelectionForm();
		$te = new ilFormSectionHeaderGUI();
		$te->setTitle($this->pl->txt('common_title'));
		$this->form->addItem($te);
		$this->form->setTitle($this->pl->txt('form_title'));
		$this->form->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		foreach (ctrlmmEntry::getAllLanguageIds() as $language) {
			$te = new ilTextInputGUI($this->lng->txt('meta_l_' . $language), 'title_' . $language);
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
			$te->setTitle($this->pl->txt('common_settings'));
			$this->form->addItem($te);
		}
		$this->form->addCommandButton($mode . 'Object', $this->pl->txt('common_create'));
		if ($mode != 'create') {
			$this->form->addCommandButton(ilCtrlMainMenuConfigGUI::CMD_UPDATE_OBJECT_AND_STAY, $this->pl->txt('create_and_stay'));
		}
		$this->form->addCommandButton(ilCtrlMainMenuConfigGUI::CMD_CONFIGURE, $this->pl->txt('common_cancel'));
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
		global $DIC;
		$opt = array();
		$role_ids = array();
		foreach ($DIC->rbac()->review()->getRolesByFilter($filter) as $role) {
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
		$ro = new ilRadioGroupInputGUI($this->pl->txt('permission_type'), 'permission_type');
		$ro->setRequired(true);
		foreach (ctrlmmMenu::getAllPermissionsAsArray() as $k => $v) {
			$option = new ilRadioOption($v, $k);
			switch ($k) {
				case ctrlmmMenu::PERM_NONE :
					break;
				case ctrlmmMenu::PERM_ROLE :
				case ctrlmmMenu::PERM_ROLE_EXEPTION :
					$se = new ilMultiSelectInputGUI($this->pl->txt('perm_input'), 'permission_' . $k);
					$se->setWidth(400);
					$se->setOptions($global_roles);
					$option->addSubItem($se);
					// Variante mit MultiSelection
					$se = new ilMultiSelectInputGUI($this->pl->txt('perm_input_locale'), 'permission_locale_' . $k);
					$se->setWidth(400);
					$se->setOptions($locale_roles);
					// $option->addSubItem($se);
					// Variante mit TextInputGUI
					$te = new ilTextInputGUI($this->pl->txt('perm_input_locale'), 'permission_locale_' . $k);
					$te->setInfo($this->pl->txt('perm_input_locale_info'));
					$option->addSubItem($te);
					break;
				case ctrlmmMenu::PERM_REF_WRITE :
				case ctrlmmMenu::PERM_REF_READ :
					$te = new ilTextInputGUI($this->pl->txt('perm_input'), 'permission_' . $k);
					$option->addSubItem($te);
					break;
				case ctrlmmMenu::PERM_USERID :
					$te = new ilTextInputGUI($this->pl->txt('perm_input_user'), 'permission_user_' . $k);
					$te->setInfo($this->pl->txt('perm_input_user_info'));
					$option->addSubItem($te);
					break;
				case ctrlmmMenu::PERM_SCRIPT :
					$te = new ilTextInputGUI($this->pl->txt('perm_input_script_path'), 'perm_input_script_path');
					$option->addSubItem($te);
					$te = new ilTextInputGUI($this->pl->txt('perm_input_script_class'), 'perm_input_script_class');
					$option->addSubItem($te);
					$te = new ilTextInputGUI($this->pl->txt('perm_input_script_method'), 'perm_input_script_method');
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

