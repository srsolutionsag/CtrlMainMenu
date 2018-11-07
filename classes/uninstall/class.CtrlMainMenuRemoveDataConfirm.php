<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class CtrlMainMenuRemoveDataConfirm
 *
 * @ilCtrl_isCalledBy CtrlMainMenuRemoveDataConfirm: ilUIPluginRouterGUI
 */
class CtrlMainMenuRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;
}
