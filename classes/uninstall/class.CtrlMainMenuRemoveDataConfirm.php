<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig;
use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class CtrlMainMenuRemoveDataConfirm
 *
 * @ilCtrl_isCalledBy CtrlMainMenuRemoveDataConfirm: ilUIPluginRouterGUI
 */
class CtrlMainMenuRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function getUninstallRemovesData() {
		return ilCtrlMainMenuConfig::getConfigValue(self::KEY_UNINSTALL_REMOVES_DATA);
	}


	/**
	 * @inheritdoc
	 */
	public function setUninstallRemovesData($uninstall_removes_data) {
		ilCtrlMainMenuConfig::set(self::KEY_UNINSTALL_REMOVES_DATA, $uninstall_removes_data);
	}


	/**
	 * @inheritdoc
	 */
	public function removeUninstallRemovesData() {
		ilCtrlMainMenuConfig::remove(self::KEY_UNINSTALL_REMOVES_DATA);
	}
}
