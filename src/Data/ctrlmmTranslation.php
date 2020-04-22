<?php

namespace srag\Plugins\CtrlMainMenu\Data;

use ActiveRecord;
use ilCtrlMainMenuPlugin;
use ilLanguage;
use srag\DIC\CtrlMainMenu\DICTrait;

/**
 * ctrlmmTranslation
 *
 * @package srag\Plugins\CtrlMainMenu\Data
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmTranslation extends ActiveRecord {

	use DICTrait;
	const TABLE_NAME = 'ui_uihk_ctrlmm_t';
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @var int
	 *
	 * @con_is_primary true
	 * @con_sequence   true
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_is_notnull true
	 * @con_length     8
	 */
	protected $id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_is_notnull true
	 * @con_length     8
	 */
	protected $entry_id = 0;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_is_notnull true
	 * @con_length     255
	 */
	protected $language_key = '';
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_is_notnull true
	 * @con_length     500
	 */
	protected $title = '';
	/**
	 * @var array
	 */
	protected static $instance_cache = array();


	/**
	 * @param int    $entry_id
	 * @param string $language_key
	 *
	 * @return ctrlmmTranslation
	 */
	public static function _getInstanceForLanguageKey($entry_id, $language_key) {
		if (!empty($instance_cache[$entry_id][$language_key])) {
			return $instance_cache[$entry_id][$language_key];
		}
		$result = self::where(array( 'entry_id' => $entry_id, 'language_key' => $language_key ));

		if ($result->hasSets()) {
			$instance_cache[$entry_id][$language_key] = $result->first();
		} else {
			$instace = new self();
			$instace->setLanguageKey($language_key);
			$instace->setEntryId($entry_id);

			$instance_cache[$entry_id][$language_key] = $instace;
		}

		return $instance_cache[$entry_id][$language_key];
	}


	/**
	 * @var array
	 */
	protected static $translations_array_cache = array();


	/**
	 * @param int $entry_id
	 *
	 * @return mixed
	 */
	public static function _getAllTranslationsAsArray($entry_id) {
		if (empty($translations_array_cache [$entry_id])) {

			$query = self::where(array( 'entry_id' => $entry_id ));

			$entries = $query->getArray();
			$return = array();
			foreach ($entries as $set) {
				$return[$set['language_key']] = $set['title'];
			}

			$translations_array_cache [$entry_id] = $return;
		}

		return $translations_array_cache [$entry_id];
	}


	/**
	 * @param int $entry_id
	 *
	 * @return bool|string
	 */
	public static function _getTitleForEntryId($entry_id) {
		$obj = self::_getInstanceForLanguageKey($entry_id, self::dic()->user()->getLanguage());

		if ($obj->getId() == 0) {
			$lngs = new ilLanguage('en');
			$obj = self::_getInstanceForLanguageKey($entry_id, $lngs->getDefaultLanguage());
			if ($obj->getId() == 0) {
				return false;
			}
		}

		return $obj->getTitle();
	}


	/**
	 * @param int $entry_id
	 *
	 * @return ctrlmmTranslation[]
	 */
	public static function _getAllInstancesForEntryId($entry_id) {
		$result = self::where(array( 'entry_id' => $entry_id ));

		return $result->get();
	}


	public static function _deleteAllInstancesForEntryId($entry_id) {
		foreach (self::_getAllInstancesForEntryId($entry_id) as $tr) {
			$tr->delete();
		}
	}


	//
	// Setter & Getter
	//
	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $entry_id
	 */
	public function setEntryId($entry_id) {
		$this->entry_id = $entry_id;
	}


	/**
	 * @return int
	 */
	public function getEntryId() {
		return $this->entry_id;
	}


	/**
	 * @param string $language_key
	 */
	public function setLanguageKey($language_key) {
		$this->language_key = $language_key;
	}


	/**
	 * @return string
	 */
	public function getLanguageKey() {
		return $this->language_key;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}
}
