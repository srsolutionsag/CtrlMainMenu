<?php

namespace srag\Plugins\CtrlMainMenu\Data;

use ActiveRecord;
use ilCtrlMainMenuPlugin;
use srag\DIC\DICTrait;

/**
 * ctrlmmData
 *
 * @package srag\Plugins\CtrlMainMenu\Data
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmData extends ActiveRecord {

	use DICTrait;
	const TABLE_NAME = 'ui_uihk_ctrlmm_d';
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;
	const DATA_TYPE_STRING = 'str';
	const DATA_TYPE_ARRAY = 'arr';
	const DATA_TYPE_INT = 'int';
	const DATA_TYPE_BOOL = 'bool';


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
	 * @con_is_unique  true
	 * @con_sequence   true
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	public $id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	public $parent_id = 0;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     1024
	 */
	public $data_key = '';
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     1024
	 */
	public $data_value = '';
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     10
	 */
	public $data_type = self::DATA_TYPE_STRING;
	/**
	 * @var array
	 */
	protected static $instance_cache = array();


	/**
	 * @param int    $parent_id
	 * @param string $data_key
	 *
	 * @return ctrlmmData
	 */
	public static function _getInstanceForDataKey($parent_id, $data_key) {
		if (!empty($instance_cache[$parent_id][$data_key])) {
			return $instance_cache[$parent_id][$data_key];
		}
		$result = self::where(array( 'parent_id' => $parent_id, 'data_key' => $data_key ));

		if ($result->hasSets()) {
			$instance_cache[$parent_id][$data_key] = $result->first();
		} else {
			$instance = new self();
			$instance->setParentId($parent_id);
			$instance->setDataKey($data_key);

			$instance_cache[$parent_id][$data_key] = $instance;
		}

		return $instance_cache[$parent_id][$data_key];
	}


	/**
	 * @param int  $parent_id
	 * @param bool $as_array
	 *
	 * @return ctrlmmData[]|array
	 */
	public static function _getAllInstancesForParentId($parent_id, $as_array = false) {
		$result = self::where(array( 'parent_id' => $parent_id ));

		if ($as_array) {
			return $result->getArray();
		} else {
			return $result->get();
		}
	}


	public static function _deleteAllInstancesForParentId($parent_id) {
		$deleteChilds = self::_getAllInstancesForParentId($parent_id);
		foreach ($deleteChilds as $nr => $child) {
			$child->delete();
		}
	}


	/**
	 * @param mixed $value
	 *
	 * @return string
	 */
	public static function _getDataTypeForValue($value) {
		switch (true) {
			case (is_array($value));
				return self::DATA_TYPE_ARRAY;
			case (is_bool($value));
				return self::DATA_TYPE_BOOL;
			case (is_int($value));
				return self::DATA_TYPE_INT;
			default:
				return self::DATA_TYPE_STRING;
		}
	}


	/**
	 * @param int $parent_id
	 *
	 * @return array
	 */
	public static function getDataForEntry($parent_id) {
		$sets = self::_getAllInstancesForParentId($parent_id);

		$data = array();
		foreach ($sets as $set) {
			switch ($set->getDataType()) {
				case self::DATA_TYPE_ARRAY:
					$data[$set->getDataKey()] = json_decode($set->getDataValue(), true);
					break;
				case self::DATA_TYPE_INT:
					$data[$set->getDataKey()] = $set->getDataValue();
					break;
				case self::DATA_TYPE_BOOL:
					$data[$set->getDataKey()] = $set->getDataValue() ? '1' : '0';
					break;
				default:
					$data[$set->getDataKey()] = $set->getDataValue();
					break;
			}
		}

		return $data;
	}


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
	 * @param string $data_key
	 */
	public function setDataKey($data_key) {
		$this->data_key = $data_key;
	}


	/**
	 * @return string
	 */
	public function getDataKey() {
		return $this->data_key;
	}


	/**
	 * @param string $data_value
	 */
	public function setDataValue($data_value) {
		$this->data_value = $data_value;
	}


	/**
	 * @return string
	 */
	public function getDataValue() {
		return $this->data_value;
	}


	/**
	 * @param int $parent_id
	 */
	public function setParentId($parent_id) {
		$this->parent_id = $parent_id;
	}


	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->parent_id;
	}


	/**
	 * @return string
	 */
	public function getDataType() {
		return $this->data_type;
	}


	/**
	 * @param string $data_type
	 */
	public function setDataType($data_type) {
		$this->data_type = $data_type;
	}
}
