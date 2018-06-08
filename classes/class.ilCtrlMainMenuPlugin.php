<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilCtrlMainMenuPlugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Michael Herren <mh@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ilCtrlMainMenuPlugin extends ilUserInterfaceHookPlugin {

	const PLUGIN_ID = 'ctrlmm';
	const PLUGIN_NAME = 'CtrlMainMenu';
	/**
	 * @var ilCtrlMainMenuConfig
	 */
	protected static $config_cache;
	/**
	 * @var ilCtrlMainMenuPlugin
	 */
	protected static $plugin_cache;
	/**
	 * @var ilDB
	 */
	protected $db;


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	public function __construct() {
		parent::__construct();

		global $DIC;

		$this->db = $DIC->database();
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
	 * @return ilCtrlMainMenuPlugin
	 */
	public static function getInstance() {
		if (!isset(self::$plugin_cache)) {
			self::$plugin_cache = new ilCtrlMainMenuPlugin();
		}

		return self::$plugin_cache;
	}


	/**
	 * @return bool
	 */
	protected function beforeUninstall() {
		$this->db->dropTable(ctrlmmEntry::TABLE_NAME, false);
		$this->db->dropTable(ctrlmmData::TABLE_NAME, false);
		$this->db->dropTable(ctrlmmTranslation::TABLE_NAME, false);
		$this->db->dropTable(ilCtrlMainMenuConfig::TABLE_NAME, false);

		return true;
	}


	/**
	 * @return bool
	 */
	public static function isGlobalCacheActive() {
		static $has_global_cache;
		if (!isset($has_global_cache)) {
			$has_global_cache = ilCtrlMainMenuConfig::getConfigValue('activate_cache');
		}

		return $has_global_cache;
	}
}
