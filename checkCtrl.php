<?php
/**
 * AJAX ilCtrlMainMenuChecker
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */

require_once __DIR__ . "/vendor/autoload.php";

use srag\Plugins\CtrlMainMenu\Checker\ctrlmmChecker;

ctrlmmChecker::check($_REQUEST['classes']);
