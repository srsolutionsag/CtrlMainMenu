<?php

namespace srag\Plugins\CtrlMainMenu\Config;

use ActiveRecord;
use ilCtrlMainMenuPlugin;
use srag\DIC\DICTrait;

/**
 * Class Configuration
 *
 * @package    srag\Plugins\CtrlMainMenu\Config
 *
 * @author     Michael Heren <mh@studer-raimann.ch>
 * @author     Fabian Schmid <fs@studer-raimann.ch>
 *
 * @deprecated TODO: Use srag\ActiveRecordConfig
 */
class ilCtrlMainMenuConfig extends ActiveRecord {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;
	/**
	 * @deprecated
	 */
	const TABLE_NAME = 'uihkctrlmainmenu_c';
	/**
	 * @deprecated
	 */
	const F_CSS_PREFIX = 'css_prefix';
	/**
	 * @deprecated
	 */
	const F_CSS_ACTIVE = 'css_active';
	/**
	 * @deprecated
	 */
	const F_CSS_INACTIVE = 'css_inactive';
	/**
	 * @deprecated
	 */
	const F_DOUBLECLICK_PREVENTION = 'doubleclick_prevention';
	/**
	 * @deprecated
	 */
	const F_SIMPLE_FORM_VALIDATION = 'simple_form_validation';
	/**
	 * @deprecated
	 */
	const F_REPLACE_FULL_HEADER = "replace_full_header";


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @var array
	 *
	 * @deprecated
	 */
	protected static $cache = array();
	/**
	 * @var array
	 *
	 * @deprecated
	 */
	protected static $cache_loaded = array();
	/**
	 * @var bool
	 *
	 * @deprecated
	 */
	protected $ar_safe_read = false;


	/**
	 * @param string $name
	 *
	 * @return string
	 *
	 * @deprecated
	 */
	public static function getConfigValue($name) {
		if (!isset(self::$cache_loaded[$name])) {
			/**
			 * @var self $obj
			 */
			$obj = self::find($name);
			if ($obj === NULL) {
				self::$cache[$name] = NULL;
			} else {
				self::$cache[$name] = $obj->getFieldValue();
			}
			self::$cache_loaded[$name] = true;
		}

		return self::$cache[$name];
	}


	/**
	 * @param string $name
	 * @param string $value
	 *
	 * @deprecated
	 */
	public static function set($name, $value) {
		/**
		 * @var self $obj
		 */
		$obj = self::findOrGetInstance($name);
		$obj->setFieldValue($value);
		if (self::where(array( 'name_key' => $name ))->hasSets()) {
			$obj->update();
		} else {
			$obj->create();
		}
	}


	/**
	 * @param string $name
	 *
	 * @deprecated
	 */
	public static function remove($name) {
		/**
		 * @var self $obj
		 */
		$obj = self::find($name);
		if ($obj !== NULL) {
			$obj->delete();
		}
	}


	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_is_unique        true
	 * @db_is_primary       true
	 * @db_is_notnull       true
	 * @db_fieldtype        text
	 * @db_length           250
	 *
	 * @deprecated
	 */
	protected $name_key;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           1000
	 *
	 * @deprecated
	 */
	protected $field_value;


	/**
	 * @param string $field_value
	 *
	 * @deprecated
	 */
	public function setFieldValue($field_value) {
		$this->field_value = $field_value;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getFieldValue() {
		return $this->field_value;
	}


	/**
	 * @param string $name_key
	 *
	 * @deprecated
	 */
	public function setNameKey($name_key) {
		$this->name_key = $name_key;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getNameKey() {
		return $this->name_key;
	}
}
