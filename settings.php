<?php

require_once __DIR__ . "/vendor/autoload.php";

use srag\Plugins\CtrlMainMenu\EntryTypes\Settings\ctrlmmSettings;

ctrlmmSettings::save($_POST);
