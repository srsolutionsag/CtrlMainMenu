<?php

namespace srag\Plugins\CtrlMainMenu\Checker;

use ilAuthFactory;
use ilContext;
use ilCtrlMainMenuPlugin;
use srag\DIC\CtrlMainMenu\DICTrait;

/**
 * Class ctrlmmChecker
 *
 * @package srag\Plugins\CtrlMainMenu\Checker
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmChecker {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;
	/**
	 * @var array
	 */
	protected $classes = array();


	/**
	 * @param string $gui_classes
	 */
	public static function check($gui_classes) {
		new self($gui_classes);
	}


	/**
	 * @param string $gui_classes
	 */
	private function __construct($gui_classes) {
		$this->initILIAS();
		$this->setClasses(explode(',', $gui_classes));
		$this->printJson();
	}


	protected function printJson() {
		header('Content-Type: application/json');
		echo json_encode(array( 'status' => self::dic()->ctrl()->checkTargetClass($this->getClasses()) ));
	}


	//
	// Setter & Getter
	//
	/**
	 * @param array $classes
	 */
	public function setClasses($classes) {
		$this->classes = $classes;
	}


	/**
	 * @return array
	 */
	public function getClasses() {
		return $this->classes;
	}


	//
	// Helpers
	//
	//

	public function initILIAS() {
		chdir(substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], "/Customizing")));
		require_once "include/inc.ilias_version.php";
		require_once "include/inc.header.php";
		ilContext::init(ilContext::CONTEXT_CRON);
		ilAuthFactory::setContext(ilAuthFactory::CONTEXT_WEB);
	}


	private static function includes() {
	}
}
