<?php

namespace srag\Plugins\CtrlMainMenu\EntryTypes\Desktop;

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

use ilCtrlMainMenuPlugin;
use srag\DIC\CtrlMainMenu\DICTrait;

/**
 * Class ctrlmmGLEntry
 *
 * @package srag\Plugins\CtrlMainMenu\EntryTypes\Desktop
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class ctrlmmGLEntry {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilCtrlMainMenuPlugin::class;
	const CLASS_DISABLED = 'disabled ctrlmm_disabled';
	/**
	 * @var string
	 */
	protected $id = '';
	/**
	 * @var string
	 */
	protected $title = '';
	/**
	 * @var string
	 */
	protected $link = '';
	/**
	 * @var string
	 */
	protected $target = '_top';
	/**
	 * @var string
	 */
	protected $onclick = '';
	/**
	 * @var array
	 */
	protected $classes = array();


	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getLink() {
		return $this->link;
	}


	/**
	 * @param string $link
	 */
	public function setLink($link) {
		$this->link = $link;
	}


	/**
	 * @return string
	 */
	public function getTarget() {
		return $this->target;
	}


	/**
	 * @param string $target
	 */
	public function setTarget($target) {
		$this->target = $target;
	}


	/**
	 * @return string
	 */
	public function getOnclick() {
		return $this->onclick;
	}


	/**
	 * @param string $onclick
	 */
	public function setOnclick($onclick) {
		$this->onclick = $onclick;
	}


	/**
	 * @param bool $disable_active
	 *
	 * @return array
	 */
	public function getClasses($disable_active) {
		$this->checkClasses($disable_active);

		return $this->classes;
	}


	/**
	 * @param array $classes
	 */
	public function setClasses($classes) {
		$this->classes = $classes;
	}


	/**
	 * @param bool $disable_active
	 */
	protected function checkClasses($disable_active) {
		if ($disable_active) {
			$real_link = str_replace('&amp;', '&', $this->getLink());
			$same_link = ($real_link == ltrim($_SERVER['REQUEST_URI'], "/"));
			if ($same_link) {
				$this->classes[] = self::CLASS_DISABLED;
			}

			$active = '';
			switch ($_GET['cmdClass']) {
				case 'ilbookmarkadministrationgui':
					$active = 'bookm';
					break;
				case 'ilpdnotesgui':
					$active = 'notes';
					break;
				case 'ilpdnewsgui':
					$active = 'news';
					break;
				case 'ilobjectownershipmanagementgui':
				case 'ilobjworkspacerootfoldergui':
				case 'ilpersonalworkspacegui':
					$active = 'wsp';
					break;
				case 'ilobjportfoliogui':
					$active = 'port';
					break;
				case 'illearningprogressgui':
				case 'illplistofobjectsgui':
				case 'illplistofprogressgui':
					$active = 'lp';
					break;
				case 'ilcalendarpresentationgui':
				case 'ilcalendarinboxgui':
				case 'ilcalendardaygui':
				case 'ilcalendarweekgui':
				case 'ilcalendarmonthgui':
				case 'ilcalendarcategorygui':
				case 'ilcalendarusersettingsgui':
					$active = 'cal';
					break;
				case 'ilmailoptionsgui':
				case 'ilmailformgui':
				case 'ilmailfoldergui':
					$active = 'mail';
					break;
				case 'ilmailaddressbookgui':
					$active = 'contacts';
					break;
				case 'ilpersonalprofilegui':
					$active = 'profile';
					break;
				case 'ilpersonalsettingsgui':
					$active = 'sett';
					break;
				case 'ilusabilitypersonaldesktopgui':
					$active = 'crs_grp';
					break;
			}

			if ("mm_pd_{$active}" == $this->getId()) {
				$this->classes[] = self::CLASS_DISABLED;
			}
		}
	}
}
