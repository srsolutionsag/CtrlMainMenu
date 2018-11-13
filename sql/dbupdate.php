<#1>
<?php
\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::updateDB();
\srag\Plugins\CtrlMainMenu\Data\ctrlmmData::updateDB();
\srag\Plugins\CtrlMainMenu\Data\ctrlmmTranslation::updateDB();
\srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::updateDB();
\srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu::includeAllTypes();

\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::set(\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::F_CSS_PREFIX, 'il');
\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::set(\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::F_CSS_ACTIVE, 'MMActive');
\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::set(\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::F_CSS_INACTIVE, 'MMInactive');
\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::set(\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::F_REPLACE_FULL_HEADER, false);
\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::set(\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::F_DOUBLECLICK_PREVENTION, false);
\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::set(\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::F_SIMPLE_FORM_VALIDATION, false);

if (!\srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::entriesExistForType(\srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu::TYPE_DESKTOP)) {
	$desktop = new \srag\Plugins\CtrlMainMenu\EntryTypes\Desktop\ctrlmmEntryDesktop();
	$desktop->setPosition(1);
	$desktop->create();
}

if (!\srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::entriesExistForType(\srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu::TYPE_REPOSITORY)) {
	$repo = new \srag\Plugins\CtrlMainMenu\EntryTypes\Repository\ctrlmmEntryRepository();
	$repo->setPosition(2);
	$repo->create();
}

if (!\srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::entriesExistForType(\srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu::TYPE_ADMIN)) {
	\srag\Plugins\CtrlMainMenu\EntryInstaceFactory\ctrlmmEntryInstaceFactory::createAdminEntry();
}
?>
<#2>
<?php
/* */
?>
<#3>
<?php
/* */
?>
<#4>
<?php
/* */
?>
<#5>
<?php
\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::renameDBField('config_key', 'name');
\srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::renameDBField('config_value', 'value');
?>
<#6>
<?php
\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->modifyTableColumn(\srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::TABLE_NAME, 'parent', array(
	'length' => '8',
));
?>
<#7>
<?php
if (\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->tableColumnExists(\srag\Plugins\CtrlMainMenu\Data\ctrlmmData::TABLE_NAME, 'data_type')) {
	\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->modifyTableColumn(\srag\Plugins\CtrlMainMenu\Data\ctrlmmData::TABLE_NAME, 'data_type', array(
		'notnull' => true,
		'default' => \srag\Plugins\CtrlMainMenu\Data\ctrlmmData::DATA_TYPE_STRING,
	));
} else {
	\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->addTableColumn(\srag\Plugins\CtrlMainMenu\Data\ctrlmmData::TABLE_NAME, 'data_type', array(
		'type' => 'text',
		'notnull' => true,
		'length' => 10,
		'default' => \srag\Plugins\CtrlMainMenu\Data\ctrlmmData::DATA_TYPE_STRING,
	));
}
?>
<#8>
<?php
\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->manipulate('DELETE FROM ctrl_classfile WHERE comp_prefix IN ("ui_uihk_ctrlmm", "ui_uihk_ctrlmainmenu");');
?>
<#9>
<?php
\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()
	->modifyTableColumn(\srag\Plugins\CtrlMainMenu\Data\ctrlmmTranslation::TABLE_NAME, 'language_key', array( "length" => 64 ));
try {
	\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->addIndex(\srag\Plugins\CtrlMainMenu\Data\ctrlmmTranslation::TABLE_NAME, array(
		'entry_id',
		'language_key'
	), 'i2');
	\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->addIndex(\srag\Plugins\CtrlMainMenu\Data\ctrlmmData::TABLE_NAME, array( 'parent_id' ), 'i2');
	\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->addIndex(\srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::TABLE_NAME, array( 'parent' ), 'i2');
} catch (Exception $ex) {
}
?>
<#10>
<?php
foreach (\srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::get() as $ctrlmmEntry) {
	/**
	 * @var \srag\Plugins\CtrlMainMenu\EntryTypes\Admin\ctrlmmEntryAdmin $ctrlmmEntry
	 */
	if ($ctrlmmEntry->getTypeId() == \srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu::TYPE_ADMIN
		&& $ctrlmmEntry->getPermissionType() == \srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu::PERM_NONE) {
		$ctrlmmEntry->setPermissionType(\srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu::PERM_ROLE);
		$ctrlmmEntry->setPermission("[2]");
		$ctrlmmEntry->update();
	}
}
?>
<#11>
<?php
$table_column = \srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::TABLE_NAME;
if (\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->tableColumnExists($table_column, 'type')) {
	\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->renameTableColumn($table_column, 'type', 'type_id');
}
$table_column = \srag\Plugins\CtrlMainMenu\Config\ilCtrlMainMenuConfig::TABLE_NAME;
if (\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->tableColumnExists($table_column, 'name')) {
	\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->renameTableColumn($table_column, 'name', 'name_key');
}
if (\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->tableColumnExists($table_column, 'value')) {
	\srag\DIC\CtrlMainMenu\DICStatic::dic()->database()->renameTableColumn($table_column, 'value', 'field_value');
}
?>
<#12>
<?php
\srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::updateDB();

foreach (\srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::get() as $ctrlmmEntry) {
	/**
	 * @var \srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry $ctrlmmEntry
	 */

	if (empty($ctrlmmEntry->getTitleType())) {
		$ctrlmmEntry->setTitleType(\srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry::TITLE_TYPE_TEXT);
		$ctrlmmEntry->update();
	}
}
?>
