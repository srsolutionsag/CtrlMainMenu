<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Desktop;

use ilBadgeHandler;
use ilBuddySystem;
use ilCalendarSettings;
use ilCheckboxInputGUI;
use ilHelp;
use ilMailGlobalServices;
use ilMailGUI;
use ilMyStaffAccess;
use ilObjUserTracking;
use ilPersonalDesktopGUI;
use ilSetting;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\Plugins\CtrlMainMenu\GroupedListDropdown\ctrlmmEntryGroupedListDropdownGUI;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ctrlmmEntryDesktopGUI
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Desktop
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryDesktopGUI extends ctrlmmEntryGroupedListDropdownGUI {

	const F_SHOW_LOGOUT = 'show_logout';
	const F_DISABLE_ACTIVE = 'disable_active';
	const CLASS_DISABLED = 'disabled';
	/**
	 * @var ctrlmmEntryDesktop
	 */
	public $entry;
	/**
	 * @var bool
	 */
	protected $mail = false;
	/**
	 * @var bool
	 */
	protected $contacts = false;


	/**
	 * @param ctrlmmEntry $entry
	 * @param null        $parent_gui
	 */
	public function __construct(ctrlmmEntry $entry, $parent_gui = NULL) {
		parent::__construct($entry, $parent_gui);
		$this->mail = (self::dic()->rbacsystem()->checkAccess('internal_mail', ilMailGlobalServices::getMailObjectRefId()) AND self::dic()->user()->getId()
			!= ANONYMOUS_USER_ID);
		$this->contacts = ilBuddySystem::getInstance()->isEnabled();
	}


	/**
	 * @return string
	 */
	protected function getAdditionalClasses($class = '') {
		$classes = array( $class );
		if ($this->entry->isDisableActive()) {
			$classes[] = self::CLASS_DISABLED;
		}

		return implode(' ', $classes);
	}


	/**
	 * @param ctrlmmGLEntry $ctrlmmGLEntry
	 */
	protected function addGLEntry(ctrlmmGLEntry $ctrlmmGLEntry) {
		$this->gl->addEntry($ctrlmmGLEntry->getTitle(), $ctrlmmGLEntry->getLink(), '_top', '', implode(' ', $ctrlmmGLEntry->getClasses($this->entry->isDisableActive())), $ctrlmmGLEntry->getId(), ilHelp::getMainMenuTooltip($ctrlmmGLEntry->getId()), 'left center', 'right center', false);
	}


	/**
	 * Render main menu entry
	 *
	 * @return string html
	 */
	public function setGroupedListContent() {
	
		/** @var $lng \ilLanguage */
		/** @var $ilCtrl \ilCtrl */
		global $lng, $ilCtrl;
	
		// Overview
		$ctrlmmGLEntry = new ctrlmmGLEntry();
		$ctrlmmGLEntry->setId('mm_pd_sel_items');
		$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('overview'));
		$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToSelectedItems');
		$this->addGLEntry($ctrlmmGLEntry);

		// my groups and courses, if both is available
		if (self::dic()->settings()->get('disable_my_offers') == 0 AND self::dic()->settings()->get('disable_my_memberships') == 0) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_crs_grp');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('my_courses_groups'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToMemberships');
			$this->addGLEntry($ctrlmmGLEntry);
		}

		// bookmarks
		if (!self::dic()->ilias()->getSetting('disable_bookmarks')) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_bookm');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('bookmarks'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToBookmarks');
			$this->addGLEntry($ctrlmmGLEntry);
		}
		
		// calendar
		$settings = ilCalendarSettings::_getInstance();
		if ($settings->isEnabled()) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_cal');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('calendar'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToCalendar');
			$this->addGLEntry($ctrlmmGLEntry);
		}

		if (self::dic()->settings()->get("enable_my_staff") and ilMyStaffAccess::getInstance()->hasCurrentUserAccessToMyStaff() == true) {
			// my staff
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_mst');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('my_staff'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToMyStaff');
			$this->addGLEntry($ctrlmmGLEntry);
		}

		if (!self::dic()->settings()->get('disable_personal_workspace')) {
			// workspace
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_wsp');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('personal_workspace'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToWorkspace');
			$this->addGLEntry($ctrlmmGLEntry);
		}

		// portfolio
		if (self::dic()->settings()->get('user_portfolios')) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_port');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('portfolio'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToPortfolio');
			$this->addGLEntry($ctrlmmGLEntry);
		}

		// skills
		$skmg_set = new ilSetting('skmg');
		if ($skmg_set->get('enable_skmg')) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_skill');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('skills'));
			$ctrlmmGLEntry->setLink( $ilCtrl->getLinkTargetByClass(['ilpersonaldesktopgui', 'ilAchievementsGUI', 'ilPersonalSkillsGUI']) );
			$this->addGLEntry($ctrlmmGLEntry);
		}

		// Learning Progress
		if (ilObjUserTracking::_enabledLearningProgress() AND (ilObjUserTracking::_hasLearningProgressOtherUsers()
				OR ilObjUserTracking::_hasLearningProgressLearner())) {

			$lng->loadLanguageModule( 'pd' );

			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_lp');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('pd_achievements'));
			$ctrlmmGLEntry->setLink( $ilCtrl->getLinkTargetByClass(['ilpersonaldesktopgui', 'ilAchievementsGUI', 'ilLearningHistoryGUI']) );
			$this->addGLEntry($ctrlmmGLEntry);
		}
		
		// mail
		if ($this->mail) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_mail');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('mail'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilMailGUI::class);
			$this->addGLEntry($ctrlmmGLEntry);
		}

		// contacts
		if ($this->contacts) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_contacts');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('mail_addressbook'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToContacts');
			$this->addGLEntry($ctrlmmGLEntry);
		}
		
		// private notes
		if (!self::dic()->ilias()->getSetting('disable_notes')) {
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_pd_notes');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('notes_and_comments'));
			$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToNotes');
			$this->addGLEntry($ctrlmmGLEntry);
		}

		// profile
		$ctrlmmGLEntry = new ctrlmmGLEntry();
		$ctrlmmGLEntry->setId('mm_pd_profile');
		$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('personal_profile'));
		$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToProfile');
		$this->addGLEntry($ctrlmmGLEntry);

		// settings
		$ctrlmmGLEntry = new ctrlmmGLEntry();
		$ctrlmmGLEntry->setId('mm_pd_sett');
		$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('personal_settings'));
		$ctrlmmGLEntry->setLink('ilias.php?baseClass=' . ilPersonalDesktopGUI::class . '&amp;cmd=jumpToSettings');
		$this->addGLEntry($ctrlmmGLEntry);

		if ($this->entry->getShowLogout()) {
			$this->gl->addSeparator();
			$ctrlmmGLEntry = new ctrlmmGLEntry();
			$ctrlmmGLEntry->setId('mm_logout');
			$ctrlmmGLEntry->setTitle(self::dic()->language()->txt('logout'));
			$ctrlmmGLEntry->setLink('logout.php');
			$this->addGLEntry($ctrlmmGLEntry);
		}
	}



	//
	// FORM
	//

	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);
		$te = new ilCheckboxInputGUI(self::plugin()->translate(self::F_SHOW_LOGOUT), self::F_SHOW_LOGOUT);
		$this->form->addItem($te);

		$te = new ilCheckboxInputGUI(self::plugin()->translate(self::F_DISABLE_ACTIVE), self::F_DISABLE_ACTIVE);
		$this->form->addItem($te);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values[self::F_SHOW_LOGOUT] = $this->entry->getShowLogout();
		$values[self::F_DISABLE_ACTIVE] = $this->entry->isDisableActive();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		$show_logout = (bool)$this->form->getInput(self::F_SHOW_LOGOUT);
		$disable_active = (bool)$this->form->getInput(self::F_DISABLE_ACTIVE);
		$this->entry->setShowLogout($show_logout);
		$this->entry->setDisableActive($disable_active);
		parent::createEntry();
	}
}
