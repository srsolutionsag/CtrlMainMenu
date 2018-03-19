<#1>
<?php
require_once('./Services/ActiveRecord/class.ActiveRecord.php');

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');
ctrlmmEntry::updateDB();

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ilCtrlMainMenuConfig.php');
ilCtrlMainMenuConfig::updateDB();

ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_CSS_PREFIX, 'il');
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_CSS_ACTIVE, 'MMActive');
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_CSS_INACTIVE, 'MMInactive');
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_REPLACE_FULL_HEADER, false);
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_DOUBLECLICK_PREVENTION, false);
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_SIMPLE_FORM_VALIDATION, false);

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmData.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmTranslation.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/EntryInstaceFactory/class.ctrlmmEntryInstaceFactory.php');

ctrlmmData::updateDB();
ctrlmmTranslation::updateDB();

ctrlmmMenu::includeAllTypes();

$desktop = new ctrlmmEntryDesktop();
$desktop->setPosition(1);
$desktop->create();

$repo = new ctrlmmEntryRepository();
$repo->setPosition(2);
$repo->create();

ctrlmmEntryInstaceFactory::createAdminEntry();

?>
<#2>

<#3>

<#4>

<#5>
<?php
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ilCtrlMainMenuConfig.php');
ilCtrlMainMenuConfig::renameDBField('config_key', 'name');
ilCtrlMainMenuConfig::renameDBField('config_value', 'value');
?>
<#6>
<?php
global $DIC;
$ilDB = $DIC->database();
/**
 * @var $ilDB ilDB
 */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');
$ilDB->modifyTableColumn(ctrlmmEntry::TABLE_NAME, 'parent', array(
	'length' => '8',
));
?>
<#7>
<?php
global $DIC;
$ilDB = $DIC->database();

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmData.php');

if ($ilDB->tableColumnExists(ctrlmmData::TABLE_NAME, 'data_type')) {
	$ilDB->modifyTableColumn(ctrlmmData::TABLE_NAME, 'data_type', array(
		'notnull' => true,
		'default' => ctrlmmData::DATA_TYPE_STRING,
	));
} else {
	$ilDB->addTableColumn(ctrlmmData::TABLE_NAME, 'data_type', array(
		'type' => 'text',
		'notnull' => true,
		'length' => 10,
		'default' => ctrlmmData::DATA_TYPE_STRING,
	));
}
?>
<#8>
<?php
global $DIC;
$ilDB = $DIC->database();
$ilDB->manipulate('DELETE FROM ctrl_classfile WHERE comp_prefix IN ("ui_uihk_ctrlmm", "ui_uihk_ctrlmainmenu");');
?>
<#9>
<?php
global $DIC;
$ilDB = $DIC->database();
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmTranslation.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmData.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');
$ilDB->modifyTableColumn(ctrlmmTranslation::TABLE_NAME, 'language_key', array( "length" => 64 ));
$ilDB->addIndex(ctrlmmTranslation::TABLE_NAME, array( 'entry_id', 'language_key' ), 'i2');
$ilDB->addIndex(ctrlmmData::TABLE_NAME, array( 'parent_id' ), 'i2');
$ilDB->addIndex(ctrlmmEntry::TABLE_NAME, array( 'parent' ), 'i2');
?>
<#10>
<?php
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/EntryInstaceFactory/class.ctrlmmEntryInstaceFactory.php');
foreach (ctrlmmEntry::get() as $ctrlmmEntry) {
	/**
	 * @var $ctrlmmEntry ctrlmmEntryAdmin
	 */
	if ($ctrlmmEntry->getTypeId() == ctrlmmMenu::TYPE_ADMIN && $ctrlmmEntry->getPermissionType() == ctrlmmMenu::PERM_NONE) {
		$ctrlmmEntry->setPermissionType(ctrlmmMenu::PERM_ROLE);
		$ctrlmmEntry->setPermission("[2]");
		$ctrlmmEntry->update();
	}
}
?>
<#11>
<?php
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/EntryInstaceFactory/class.ctrlmmEntryInstaceFactory.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ilCtrlMainMenuConfig.php');
global $DIC;
$ilDB = $DIC->database();
$table_column = ctrlmmEntry::TABLE_NAME;
if ($ilDB->tableColumnExists($table_column, 'type')) {
	$ilDB->renameTableColumn($table_column, 'type', 'type_id');
}
$table_column = ilCtrlMainMenuConfig::TABLE_NAME;
if ($ilDB->tableColumnExists($table_column, 'name')) {
	$ilDB->renameTableColumn($table_column, 'name', 'name_key');
}
if ($ilDB->tableColumnExists($table_column, 'value')) {
	$ilDB->renameTableColumn($table_column, 'value', 'field_value');
}
?>

