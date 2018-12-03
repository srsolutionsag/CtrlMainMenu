<?php
/**
 * AJAX ilCtrlMainMenuChecker
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */

require_once __DIR__ . "/vendor/autoload.php";
ctrlmmChecker::check($_REQUEST['classes']);
