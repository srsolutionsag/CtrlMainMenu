<#1>
<?php
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ilCtrlMainMenuPlugin.php');
ilCtrlMainMenuPlugin::loadActiveRecord();


require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');
ctrlmmEntry::installDB();


require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ilCtrlMainMenuConfig.php');
ilCtrlMainMenuConfig::installDB();

ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_CSS_PREFIX, 'il');
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_CSS_ACTIVE, 'MMActive');
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_CSS_INACTIVE, 'MMInactive');
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_REPLACE_FULL_HEADER, false);
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_DOUBLECLICK_PREVENTION, false);
ilCtrlMainMenuConfig::set(ilCtrlMainMenuConfig::F_SIMPLE_FORM_VALIDATION, false);

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmData.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmTranslation.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/EntryInstaceFactory/class.ctrlmmEntryInstaceFactory.php');

ctrlmmData::installDB();
ctrlmmTranslation::installDB();

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
global $ilDB;
/**
 * @var $ilDB ilDB
 */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');
$ilDB->modifyTableColumn(ctrlmmEntry::returnDbTableName(), 'parent', array(
    'length' => '8',
));
?>

<#7>
<?php
global $ilDB;

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmData.php');

if ($ilDB->tableColumnExists(ctrlmmData::returnDbTableName(), 'data_type')) {
    $ilDB->modifyTableColumn(ctrlmmData::returnDbTableName(), 'data_type', array(
        'notnull' => true,
        'default' => ctrlmmData::DATA_TYPE_STRING,
    ));
} else {
    $ilDB->addTableColumn(ctrlmmData::returnDbTableName(), 'data_type', array(
        'type' => 'text',
        'notnull' => true,
        'length' => 10,
        'default' => ctrlmmData::DATA_TYPE_STRING,
    ));
}
?>
<#8>
<?php
global $ilDB;
$ilDB->manipulate('DELETE FROM ctrl_classfile WHERE comp_prefix IN ("ui_uihk_ctrlmm", "ui_uihk_ctrlmainmenu");');
?>
<#9>
<?php
global $ilDB;
$ilDB->modifyTableColumn('ui_uihk_ctrlmm_t', 'language_key', array( "length" => 64 ));
$ilDB->addIndex('ui_uihk_ctrlmm_t', array('entry_id', 'language_key'), 'i2');
$ilDB->addIndex('ui_uihk_ctrlmm_d', array('parent_id'), 'i2');
$ilDB->addIndex('ui_uihk_ctrlmm_e', array('parent'), 'i2');
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
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmm.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ilCtrlMainMenuConfig.php');
global $ilDB;
$table_column = ctrlmmEntry::returnDbTableName();
if ($ilDB->tableColumnExists($table_column, 'type')) {
	$ilDB->renameTableColumn($table_column, 'type', 'type_id');
}
$table_column = ilCtrlMainMenuConfig::returnDbTableName();
if ($ilDB->tableColumnExists($table_column, 'name')) {
	$ilDB->renameTableColumn($table_column, 'name', 'name_key');
}
if ($ilDB->tableColumnExists($table_column, 'value')) {
	$ilDB->renameTableColumn($table_column, 'value', 'field_value');
}
?>

