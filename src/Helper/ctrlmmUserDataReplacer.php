<?php

namespace srag\Plugins\CtrlMainMenu\Helper;

use ilCtrlMainMenuPlugin;
use ilUserDefinedFields;
use srag\DIC\CtrlMainMenu\DICTrait;

/**
 * Class ctrlmmUserDataReplacer
 *
 * @package srag\Plugins\CtrlMainMenu\Helper
 *
 * @author  Michael Herren <mh@studer-raimann.ch>
 */
class ctrlmmUserDataReplacer {
use DICTrait;
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;

	const OPEN_TAG = "[";
	const CLOSE_TAG = "]";
	/**
	 * @var array
	 */
	protected static $values = array();


	/**
	 * @param string $input_string
	 *
	 * @return string
	 */
	public static function parse($input_string) {
		self::loadData();

		foreach (self::$values as $key => $value) {
			$input_string = str_replace(self::OPEN_TAG . $key . self::CLOSE_TAG, $value, $input_string);
		}

		return $input_string;
	}


	protected static function loadData() {
		if (!self::$values) {
			self::$values['user_id'] = self::dic()->user()->getId();
			self::$values['user_name'] = self::dic()->user()->getLogin();
			//self::$values['user_session_id'] = session_id();
			self::$values['user_matriculation'] = self::dic()->user()->getMatriculation();

			self::$values['user_email'] = self::dic()->user()->getEmail();
			self::$values['user_language'] = self::dic()->user()->getCurrentLanguage();
			self::$values['user_country'] = self::dic()->user()->getCountry();
			self::$values['user_department'] = self::dic()->user()->getDepartment();
			self::$values['user_firstname'] = self::dic()->user()->getFirstname();
			self::$values['user_lastname'] = self::dic()->user()->getLastname();

			foreach (self::$values as $key => $value) {
				self::$values[$key] = urlencode($value);
			}

			$user_field_definitions = ilUserDefinedFields::_getInstance();
			$fds = $user_field_definitions->getDefinitions();

			$user_fields = self::dic()->user()->getUserDefinedData();

			foreach ($fds as $k => $f) {
				// prefixes needed for ilias!
				self::$values["f_" . self::escapeGetParameterKeys($f['field_name'])] = urlencode($user_fields['f_' . $f['field_id']]);
			}
		}
	}


	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public static function escapeGetParameterKeys($value) {
		return str_replace(array( '=', '&', '@' ), array( '', '', '' ), $value);
	}


	/**
	 * @return array
	 */
	public static function getDropdownData() {
		self::loadData();

		$out = array();
		foreach (self::$values as $key => $value) {
			$out[self::OPEN_TAG . $key . self::CLOSE_TAG] = $key;
		}

		return $out;
	}
}
