<?php

namespace srag\Plugins\CtrlMainMenu\Menu;

/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/

use ilCtrlMainMenuPlugin;
use ReflectionClass;
use srag\DIC\CtrlMainMenu\DICTrait;
use srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig;
use srag\Plugins\CtrlMainMenu\Data\ctrlmmData;
use srag\Plugins\CtrlMainMenu\Data\ctrlmmTranslation;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\Plugins\CtrlMainMenu\EntryInstaceFactory\ctrlmmEntryInstaceFactory;

/**
 * Application class for ctrlmmMenu Object.
 *
 * @package        srag\Plugins\CtrlMainMenu\Menu
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmMenu {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;
	const TYPE_CTRL = 1;
	const TYPE_LINK = 2;
	const TYPE_DROPDOWN = 3;
	const TYPE_REFID = 4;
	const TYPE_ADMIN = 5;
	const TYPE_LASTVISITED = 6;
	const TYPE_DESKTOP = 7;
	const TYPE_REPOSITORY = 8;
	const TYPE_SETTINGS = 9;
	const TYPE_SEPARATOR = 10;
	const TYPE_SEARCH = 11;
	const TYPE_STATUSBOX = 12;
	const TYPE_AUTH = 13;
	const TYPE_SUBTITLE = 14;
	const PERM_NONE = 100;
	const PERM_ROLE = 101;
	const PERM_ROLE_EXEPTION = 104;
	const PERM_REF_READ = 102;
	const PERM_REF_WRITE = 103;
	const PERM_USERID = 105;
	const PERM_SCRIPT = 106;
	/**
	 * @var array
	 */
	protected $entries;
	/**
	 * @var bool
	 */
	protected static $types_included = false;
	/**
	 * @var bool
	 */
	protected $after_separator = false;
	/**
	 * @var
	 */
	protected static $cache_active;


	/**
	 * @return bool
	 */
	public static function checkGlobalCache() {
		/*if (isset(self::$cache_active)) {
			return self::$cache_active;
		}
		$is_file = file_exists('./Services/GlobalCache/classes/class.ilGlobalCache.php');
		if ($is_file) {
			require_once('./Services/GlobalCache/classes/class.ilGlobalCache.php');

			self::$cache_active = ilGlobalCache::getInstance('ctrl_mm')->isActive();
		} else {
			self::$cache_active = false;
		}*/
		return false;
		//return self::$cache_active;
	}


	/**
	 * @return string
	 * @deprecated use ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_PREFIX)
	 */
	public static function getCssPrefix() {
		return ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_PREFIX);
	}


	/**
	 * @param boolean $after_separator
	 */
	public function setAfterSeparator($after_separator) {
		$this->after_separator = $after_separator;
	}


	/**
	 * @return boolean
	 */
	public function getAfterSeparator() {
		return $this->after_separator;
	}


	/**
	 * @param int $id
	 */
	public function __construct($id = 0) {
		ctrlmmEntry::get();
		ctrlmmTranslation::get();
		ctrlmmData::get();

		self::includeAllTypes();

		$this->setEntries(ctrlmmEntryInstaceFactory::getAllChildsForId($id));
	}


	/**
	 * @param mixed $entry
	 */
	public function addEntry($entry) {
	}


	/**
	 * @param array $entries
	 */
	public function setEntries($entries) {
		$this->entries = $entries;
	}


	/**
	 * @return array
	 */
	public function getEntries() {
		return $this->entries;
	}


	//
	// Static
	//
	/**
	 * @return string
	 */
	/*public function getCssPrefix() {
		return ilCtrlMainMenuConfig::getConfigValue(ilCtrlMainMenuConfig::F_CSS_PREFIX);
	}*/

	/**
	 * @param bool $filter
	 *
	 * @return array
	 */
	public static function getAllTypesAsArray($filter = false, $parent_id = NULL) {
		$names = array();
		foreach (self::getAllTypeConstants() as $name => $value) {
			$names[$value] = ilCtrlMainMenuPlugin::getInstance()->txt(strtolower($name));
		}
		if ($filter) {
			if ($parent_id) {
				$entry = ctrlmmEntryInstaceFactory::getInstanceByEntryId($parent_id)->getObject();
			}
			foreach ($names as $type_id => $name) {
				if (!ctrlmmEntry::isSecondInstanceAllowed($type_id)) {
					unset($names[$type_id]);
				}
				if ($parent_id) {
					if (!$entry->isChildAllowed($type_id)) {
						unset($names[$type_id]);
					}
				}
			}
		}

		return $names;
	}


	/**
	 * @return array
	 */
	public static function getAllTypeConstants() {
		$fooClass = new ReflectionClass(self::class);
		$fooClass->getConstants();
		$return = array();
		foreach ($fooClass->getConstants() as $name => $value) {
			if (strpos($name, 'TYPE_') === 0) {
				$return[$name] = $value;
			}
		}

		return $return;
	}


	/**
	 * @deprecated
	 */
	public static function includeAllTypes() {
		if (!self::$types_included) {
			self::$types_included = true;
		}
	}


	/**
	 * @return array
	 */
	public static function getAllPermissionsAsArray() {
		$fooClass = new ReflectionClass(self::class);
		$names = array();
		foreach ($fooClass->getConstants() as $name => $value) {
			$b = strpos($name, 'PERM_REF_') === false;
			if (strpos($name, 'PERM_') === 0) {
				$names[$value] = ilCtrlMainMenuPlugin::getInstance()->txt(strtolower($name));
			}
		}

		return $names;
	}
}
