<?php

require_once __DIR__ . "/vendor/autoload.php";

use srag\Plugins\CtrlMainMenu\Menu\jsonMenu;

$jsonMenu = new jsonMenu();
$jsonMenu->draw();
