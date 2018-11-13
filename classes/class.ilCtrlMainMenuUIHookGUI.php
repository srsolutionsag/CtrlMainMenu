<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenuGUI;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * User interface hook class
 *
 * @author            Alex Killing <alex.killing@gmx.de>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           2.0.02
 * @ingroup           ServicesUIComponent
 * @ilCtrl_IsCalledBy ilCtrlMainMenuUIHookGUI: ilAdministrationGUI, ilPersonalDesktopGUI, ilRepositoryGUI, ilObjPluginDispatchGUI, ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilCtrlMainMenuUIHookGUI: ilAdministrationGUI, ilPersonalDesktopGUI, ilRepositoryGUI, ilObjPluginDispatchGUI, ilCommonActionDispatcherGUI
 */
class ilCtrlMainMenuUIHookGUI extends ilUIHookPluginGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;
	/**
	 * @var bool
	 */
	protected static $replaced = false;


	public function __construct() {

	}


	/**
	 * @param string $a_comp
	 * @param string $a_part
	 * @param array  $a_par
	 *
	 * @return array
	 */
	public function getHTML($a_comp, $a_part, $a_par = array()) {

		$full_header = ($a_part == 'template_get' AND $a_par['tpl_id'] == 'Services/MainMenu/tpl.main_menu.html');
		$replace = (bool)ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_REPLACE_FULL_HEADER);
		if ($full_header && !self::$replaced) {
			if ($full_header && $replace) {
				self::$replaced = true;

				return array(
					'mode' => ilUIHookPluginGUI::REPLACE,
					'html' => $this->getMainMenuHTML()
				);
			}
		}

		$menu_only = ($a_comp == 'Services/MainMenu' AND $a_part == 'main_menu_list_entries');
		if ($menu_only && !self::$replaced AND !$replace) {
			$mm = new ctrlmmMenuGUI(0);
			self::$replaced = true;

			return array(
				'mode' => ilUIHookPluginGUI::REPLACE,
				'html' => $mm->getHTML()
			);
		}

		return array( 'mode' => ilUIHookPluginGUI::KEEP, 'html' => '' );
	}


	/**
	 * @return string
	 */
	protected function getMainMenuHTML() {
		$mainMenu = ilCtrlMainMenuPlugin::getInstance()->getTemplate('tpl.mainmenu.html', true, true);

		$mainMenu->setVariable("CSS_PREFIX", ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_PREFIX));

		$mainMenu->setVariable("HEADER_URL", $this->getHeaderURL());
		$header_icon = ilUtil::getImagePath("HeaderIcon.svg");

		$mainMenu->setVariable("HEADER_ICON", $header_icon);
		$mm = new ctrlmmMenuGUI(0);
		$mainMenu->setVariable("MAIN_MENU_LEFT", $mm->getHTML());
		$mm = new ctrlmmMenuGUI(0);
		$mm->setSide(ctrlmmMenuGUI::SIDE_RIGHT);
		$mm->setCssId('ilTopBarNav');
		$mainMenu->setVariable("MAIN_MENU_RIGHT", $mm->getHTML());

		$notificationSettings = new ilSetting('notifications');
		$chatSettings = new ilSetting('chatroom');

		//iljQueryUtil::initjQuery();

		if ($chatSettings->get('chat_enabled') && $notificationSettings->get('enable_osd')) {
			$mainMenu->touchBlock('osd_enabled');
			$mainMenu->touchBlock('osd_container');

			iljQueryUtil::initjQuery();

			ilPlayerUtil::initMediaElementJs();

			$mainMenu->addJavaScript('Services/Notifications/templates/default/notifications.js');
			$mainMenu->addCSS('Services/Notifications/templates/default/osd.css');

			$notifications = ilNotificationOSDHandler::getNotificationsForUser(self::dic()->user()->getId());
			$mainMenu->setVariable('NOTIFICATION_CLOSE_HTML', json_encode(ilGlyphGUI::get(ilGlyphGUI::CLOSE, self::dic()->language()->txt('close'))));
			$mainMenu->setVariable('INITIAL_NOTIFICATIONS', json_encode($notifications));
			$mainMenu->setVariable('OSD_POLLING_INTERVALL', $notificationSettings->get('osd_polling_intervall') ? $notificationSettings->get('osd_polling_intervall') : '5');
			$mainMenu->setVariable('OSD_PLAY_SOUND', $chatSettings->get('play_invitation_sound')
			&& self::dic()->user()->getPref('chat_play_invitation_sound') ? 'true' : 'false');
			foreach ($notifications as $notification) {
				if ($notification['type'] == 'osd_maint') {
					continue;
				}
				$mainMenu->setCurrentBlock('osd_notification_item');

				$mainMenu->setVariable('NOTIFICATION_ICON_PATH', $notification['data']->iconPath);
				$mainMenu->setVariable('NOTIFICATION_TITLE', $notification['data']->title);
				$mainMenu->setVariable('NOTIFICATION_LINK', $notification['data']->link);
				$mainMenu->setVariable('NOTIFICATION_LINKTARGET', $notification['data']->linktarget);
				$mainMenu->setVariable('NOTIFICATION_ID', $notification['notification_osd_id']);
				$mainMenu->setVariable('NOTIFICATION_SHORT_DESCRIPTION', $notification['data']->shortDescription);
				$mainMenu->parseCurrentBlock();
			}
		}

		$ilObjSystemFolder = new ilObjSystemFolder(SYSTEM_FOLDER_ID);
		$header_top_title = $ilObjSystemFolder->_getHeaderTitle();
		$mainMenu->setVariable("TXT_HEADER_TITLE", $header_top_title);

		$mainMenu->setVariable("LOCATION_STYLESHEET", ilUtil::getStyleSheetLocation());

		$mainMenu->setVariable("TXT_LOGOUT", self::dic()->language()->txt("logout"));
		//		$mainMenu->setVariable("HEADER_URL", $this->getHeaderURL());
		//		$mainMenu->setVariable("HEADER_ICON", ilUtil::getImagePath("HeaderIcon.png"));

		return $mainMenu->get();
	}


	protected function getHeaderURL() {
		$url = ilUserUtil::getStartingPointAsUrl();

		if (!$url) {
			$url = "./goto.php?target=root_1";
		}

		return $url;
	}
}
