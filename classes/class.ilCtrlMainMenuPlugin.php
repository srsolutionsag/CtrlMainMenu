<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig;
use srag\Plugins\CtrlMainMenu\Data\ctrlmmData;
use srag\Plugins\CtrlMainMenu\Data\ctrlmmTranslation;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\RemovePluginDataConfirm\PluginUninstallTrait;

/**
 * Class ilCtrlMainMenuPlugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Michael Herren <mh@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ilCtrlMainMenuPlugin extends ilUserInterfaceHookPlugin {

	use PluginUninstallTrait;
	const PLUGIN_ID = 'ctrlmm';
	const PLUGIN_NAME = 'CtrlMainMenu';
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = CtrlMainMenuRemoveDataConfirm::class;
	/**
	 * @var ilCtrlMainMenuConfig
	 */
	protected static $config_cache;
	/**
	 * @var self
	 */
	protected static $plugin_cache;


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	public function __construct() {
		parent::__construct();
	}


	//
	//	public function txt($a_var) {
	//		require_once('./Customizing/global/plugins/Libraries/PluginTranslator/class.sragPluginTranslator.php');
	////		return parent::txt($a_var);
	//
	////		return sragPluginTranslator::getInstance($this)->rebuild(true)->txt($a_var);
	//		return sragPluginTranslator::getInstance($this)->active(true)->write(true)->txt($a_var);
	//	}

	/**
	 * @return self
	 */
	public static function getInstance() {
		if (!isset(self::$plugin_cache)) {
			self::$plugin_cache = new self();
		}

		return self::$plugin_cache;
	}


	/**
	 * @inheritdoc
	 */
	protected function deleteData()/*: void*/ {
		self::dic()->database()->dropTable(ctrlmmEntry::TABLE_NAME, false);
		self::dic()->database()->dropTable(ctrlmmData::TABLE_NAME, false);
		self::dic()->database()->dropTable(ctrlmmTranslation::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilCtrlMainMenuConfig::TABLE_NAME, false);

		ilUtil::delDir(CLIENT_WEB_DIR . "/" . self::PLUGIN_ID);
	}


	/**
	 * @return bool
	 */
	public static function isGlobalCacheActive() {
		static $has_global_cache;
		if (!isset($has_global_cache)) {
			$has_global_cache = boolval(ilCtrlMainMenuConfig::getConfigValue('activate_cache'));
		}

		return $has_global_cache;
	}
}
