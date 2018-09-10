<?php

require_once __DIR__ . "/../../../vendor/autoload.php";

use srag\Plugins\CtrlMainMenu\EntryTypes\Settings\ctrlmmEntrySettings;
use srag\Plugins\CtrlMainMenu\EntryTypes\Settings\ctrlmmSettings;
use srag\Plugins\CtrlMainMenu\GroupedListDropdown\ctrlmmEntryGroupedListDropdownGUI;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ctrlmmEntryCtrlGUI
 *
 * @author                   Fabian Schmid <fs@studer-raimann.ch>
 * @version                  2.0.02
 *
 * @ilCtrl_IsCalledBy        ctrlmmEntrySettingsGUI: ilCommonActionDispatcherGUI
 */
class ctrlmmEntrySettingsGUI extends ctrlmmEntryGroupedListDropdownGUI {

	const F_SHOW_TITLE = 'show_title';
	/**
	 * @var bool
	 */
	protected $show_arrow = false;
	/**
	 * @var ctrlmmEntrySettings
	 */
	public $entry;


	/**
	 * Render main menu entry
	 *
	 * @param
	 *
	 * @return string html
	 */
	protected function setGroupedListContent() {
		return "";
	}


	/**
	 * @return string
	 */
	protected function getContent() {
		self::dic()->language()->loadLanguageModule('mail');

		self::dic()->template()->addJavaScript(self::plugin()->directory() . '/templates/js/settings.js');
		self::dic()->template()->addCss(self::plugin()->directory() . '/templates/css/settings.css');

		$form = new ilPropertyFormGUI();

		$form->setId('ctrl_mm_settings_entry_form');

		//
		// Sprachen
		//
		$options = array();
		foreach (self::dic()->language()->getInstalledLanguages() as $lang_key) {
			$options[$lang_key] = ilLanguage::_lookupEntry($lang_key, 'meta', 'meta_l_' . $lang_key);
		}
		$language = new ilSelectInputGUI(self::dic()->language()->txt('language'), ctrlmmSettings::LANGUAGE);
		$language->setValue(self::dic()->user()->getLanguage());
		$language->setDisabled(self::dic()->settings()->get('usr_settings_disable_language'));
		$language->setOptions($options);
		$form->addItem($language);

		//
		// Template/Skin
		//
		if (!self::dic()->settings()->get('usr_settings_disable_skin_style')) {
			$templates = self::dic()->systemStyle()->getAllTemplates();
			if (is_array($templates)) {
				$options = array();
				foreach ($templates as $template) {
					// get styles information of template
					$styleDef = new ilStyleDefinition($template['id']);
					$styleDef->startParsing();
					$styles = $styleDef->getStyles();

					foreach ($styles as $style) {
						if ($style['id'] == 'mobile') {
							continue;
						}
						if (!ilObjStyleSettings::_lookupActivatedStyle($template['id'], $style['id'])) {
							continue;
						}

						$options[$template['id'] . ':' . $style['id']] = $styleDef->getTemplateName() . ' / ' . $style['name'];
					}
				}

				$skin = new ilSelectInputGUI(self::dic()->language()->txt('skin_style'), ctrlmmSettings::SKIN);
				$skin->setValue(self::dic()->user()->skin . ':' . self::dic()->user()->prefs['style']);
				$skin->setDisabled(self::dic()->settings()->get('usr_settings_disable_skin_style'));
				$skin->setOptions($options);
				$form->addItem($skin);
			}
		}

		//
		// Table-Results
		//
		$results = new ilSelectInputGUI(self::dic()->language()->txt('hits_per_page'), ctrlmmSettings::RESULTS);
		$hits_options = array( 10, 15, 20, 30, 40, 50, 100, 9999 );
		$options = array();
		foreach ($hits_options as $hits_option) {
			$hstr = ($hits_option == 9999) ? self::dic()->language()->txt('no_limit') : $hits_option;
			$options[$hits_option] = $hstr;
		}
		$results->setOptions($options);
		$results->setValue(self::dic()->user()->prefs['hits_per_page']);
		$results->setDisabled(self::dic()->settings()->get('usr_settings_disable_hits_per_page'));
		$results->setOptions($options);
		$form->addItem($results);

		//
		// Mail
		//
		if (self::dic()->settings()->get('usr_settings_disable_mail_incoming_mail') != '1') {
			$options = array(
				IL_MAIL_LOCAL => self::dic()->language()->txt('mail_incoming_local'),
				IL_MAIL_EMAIL => self::dic()->language()->txt('mail_incoming_smtp'),
				IL_MAIL_BOTH => self::dic()->language()->txt('mail_incoming_both'),
			);
			$si = new ilSelectInputGUI(self::dic()->language()->txt('mail_incoming'), ctrlmmSettings::INCOMING_TYPE);
			$si->setOptions($options);
			if (!strlen(ilObjUser::_lookupEmail(self::dic()->user()->getId())) OR self::dic()->settings()->get('usr_settings_disable_mail_incoming_mail') == '1') {
				$si->setDisabled(true);
			}
			$mailOptions = new ilMailOptions(self::dic()->user()->getId());
			$si->setValue($mailOptions->getIncomingType());

			$form->addItem($si);
		}

		//
		// User
		//
		$te = new ilHiddenInputGUI(ctrlmmSettings::USR_TOKEN);

		$te->setValue(ctrlmmSettings::enc(self::dic()->user()->getId()));
		$form->addItem($te);

		$form->addCommandButton('#', self::plugin()->translate('settentr_button_save'));

		$setting_tpl = self::plugin()->template('tpl.settings_entry.html', false);
		$setting_tpl->setVariable('CTRLMM_CONTENT', $form->getHTML());

		return $setting_tpl->get();
	}


	//	/**
	//	 * @param string $mode
	//	 */
	//	public function initForm($mode = 'create') {
	//		parent::initForm($mode);
	//		$te = new ilCheckboxInputGUI(self::plugin()->translate(self::F_SHOW_TITLE), self::F_SHOW_TITLE);
	//		$this->form->addItem($te);
	//	}
	//
	//
	//	public function setFormValuesByArray() {
	//		$values = parent::setFormValuesByArray();
	//		$values[self::F_SHOW_TITLE] = $this->entry->getShowTitle();
	//		$this->form->setValuesByArray($values);
	//	}
	//
	//
	//	public function createEntry() {
	//		parent::createEntry();
	//		$this->entry->setShowTitle($this->form->getInput(self::F_SHOW_TITLE));
	//		$this->entry->update();
	//	}
}
