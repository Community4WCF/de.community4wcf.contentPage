<?php
// wcf imports
require_once(WCF_DIR.'lib/data/contentPage/ContentPage.class.php');
require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');
 
/**
 * Provides functions to manage this content page.
 * 
 * @svn			$Id: C4WpagePage.class.php 1207 2010-04-09 23:32:46Z TobiasH87 $
 *
 * @author		Community4WCF
 * @copyright	2010 Community4WCF
 * @license		Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		de.community4wcf.contentPage
 * @subpackage	data.contentPage
 * @category	WoltLab Community Framework
 */
class ContentPageEditor extends ContentPage {

	/**
	 * Creates a new faq item.
	 * 
	 * @param	string				$subject
	 * @param	string				$message
	 * @param	string				$shortDescription
	 * @param	integer				$languageID
	 * @param	string				$icon
	 * @param array				$options
	 * @param	MessageAttachmentListEditor	$attachmentList
	 * @param	PollEditor			$pollEditor
	 * @return	ContentPageEditor
	 */
	public function create($subject, $message, $shortDescription, $icon, $languageID = 0, $canSeeGroupIDs, $options = array(), $attachmentList = null, $poll = null) {
		// get number of attachments
		$attachmentsAmount = ($attachmentList !== null ? count($attachmentList->getAttachments()) : 0);
		// get group ids, who can see the page
		if (is_array($canSeeGroupIDs)) $canSeeGroupIDs = implode(',', $canSeeGroupIDs);
		
		// save page
		$sql = "INSERT INTO	wcf".WCF_N."_content_page
					(subject, message, shortDescription, icon, userID, canSeeGroupIDs, time, attachments, pollID, enableSmilies, enableHtml, enableBBCodes)
			VALUES		('".escapeString($subject)."', '".escapeString($message)."', '".escapeString($shortDescription)."', '".escapeString($icon)."', ".WCF::getUser()->userID.", '".escapeString($canSeeGroupIDs)."', ".TIME_NOW.", ".$attachmentsAmount.", ".$poll->pollID.",
					".(isset($options['enableSmilies']) ? $options['enableSmilies'] : 1).",
					".(isset($options['enableHtml']) ? $options['enableHtml'] : 0).",
					".(isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1).")";
		WCF::getDB()->sendQuery($sql);
		
		// get id
		$pageID = WCF::getDB()->getInsertID("wcf".WCF_N."_content_page", 'page');
		
		// get new object
		$page = new ContentPageEditor($pageID);
		
		// update attachments
		if ($attachmentList !== null) {
			$attachmentList->updateContainerID($pageID);
			$attachmentList->findEmbeddedAttachments($message);
		}
		
		// update poll
		if ($poll != null) {
			$poll->updateMessageID($pageID);
		}
		
		if ($languageID != 0) {
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array(
				'wcf.content.page.page-'.$pageID => $subject,
				'wcf.content.page.page-'.$pageID.'.shortDescription' => $shortDescription
			), 0, WCF::getPackageID('de.community4wcf.contentPage'));
			LanguageEditor::deleteLanguageFiles($languageID, 'wcf.content.page');
		}
		
		// save page menu item
		if (CONTENTPAGE_ENABLE_PAGEMENU) {
			if ($languageID == 0) {
				$menuItem = $subject;
			}
			else {
				$menuItem = 'wcf.content.page.page-'.$pageID;
			}
			$menuItemLink = 'index.php?page=ContentPage&pageID='.$pageID;
			$menuItemIconS = $icon.'S.png';
			$menuItemIconM = $icon.'M.png';
			$menuShowOrder= 0;
			$menuPosition = 'header';
			require_once(WCF_DIR.'lib/data/page/menu/PageMenuItemEditor.class.php');
			PageMenuItemEditor::create($menuItem, $menuItemLink, $menuItemIconS, $menuItemIconM, $menuShowOrder, $menuPosition, 0, WCF::getPackageID('de.community4wcf.contentPage'));

			// reset cache
			PageMenuItemEditor::clearCache();
		}
		
		// creates database entries
		if ($languageID == 0) {
			$locationName = $subject;
		}
		else {
			$locationName = 'wcf.content.page.page-'.$pageID;
		}

		$locationPattern = 'index\.php\?page=ContentPage&.*pageID='.$pageID;
		$packageID = WCF::getPackageID('de.community4wcf.contentPage');
		$sql = "INSERT INTO wcf".WCF_N."_page_location (locationPattern, locationName, packageID)
				VALUES ('".escapeString($locationPattern)."', '".escapeString($locationName)."', ".$packageID.")";
		WCF::getDB()->sendQuery($sql);
		
		WCF::getCache()->clear(WCF_DIR.'cache', '*.php', true);
		
		// return object
		return $page;
	}
	
	/**
	 * Creates a preview of a content page.
	 *
	 * @param 	string		$title
	 * @param 	string		$message
	 * @param 	boolean		$enableSmilies
	 * @param 	boolean		$enableHtml
	 * @param 	boolean		$enableBBCodes
	 * @return	string
	 */
	public static function createPreview($title, $message, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
		$row = array(
			'pageID' => 0,
			'title' => $title,
			'message' => $message,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes,
			'messagePreview' => true
		);

		require_once(WCF_DIR.'lib/data/contentPage/ContentPage.class.php');
		$page = new ContentPage(null, $row);
		return $page->getFormattedMessage();
	}
	
	/**
	 * Deletes a page.
	 */
	public function delete() {
		// delete page
		$sql = "DELETE FROM	wcf".WCF_N."_content_page
			WHERE		pageID = ".$this->pageID;
		WCF::getDB()->sendQuery($sql);
		
		// delete attachments
		if ($this->attachments > 0) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
			$attachmentList = new MessageAttachmentListEditor($this->pageID, 'fAQItem');
			$attachmentList->deleteAll();
		}
		
		// delete polls
		if ($this->pollID != 0) {
			require_once(WCF_DIR.'lib/data/message/poll/PollEditor.class.php');
			PollEditor::deleteAll($pageID);
		}
		
		// delete page menu item
		if (CONTENTPAGE_ENABLE_PAGEMENU) {
			$this->deletePageMenuItem($this->pageID);
		}
		// delete page location
		$this->deletePageLocation($this->pageID);
		
		// delete language variables
		LanguageEditor::deleteVariable('wcf.content.page.page-'.$this->pageID);
		LanguageEditor::deleteVariable('wcf.content.page.page-'.$this->pageID.'.shortDescription');
	}
	
	/**
	 * Deletes a page menu item.
	 */
		public function deletePageMenuItem() {	
			if (CONTENTPAGE_ENABLE_PAGEMENU) {
			// delete page menu item
			$sql = "DELETE FROM	wcf".WCF_N."_page_menu_item
				WHERE		menuItem LIKE 'wcf.content.page.page-".$this->pageID."'";
			WCF::getDB()->sendQuery($sql);
		
			// reset cache
			require_once(WCF_DIR.'lib/data/page/menu/PageMenuItemEditor.class.php');
			PageMenuItemEditor::clearCache();
			}
		}
	
	/**
	 * Deletes the matching page location
	 */
	public function deletePageLocation() {
		// location
		$sql = "DELETE FROM   wcf".WCF_N."_page_location
		WHERE   locationName LIKE 'wcf.content.page.page-".$this->pageID."'";
		WCF::getDB()->sendQuery($sql);
		
		// reset cache
		WCF::getCache()->clear(WCF_DIR.'cache', '*.php', true);
	}
	
	/**
	 * Updates this content page.
	 *
	 * @param 	string		$subject
 	 * @param 	string		$shortDescription
	 * @param 	string		$text
	 * @param 	string		$icon
	 * @param	integer		$languageID
	 * @param	AttachmentsEditor		$attachments		
	 * @param	PollEditor			$poll
	 */
	public function update($subject, $shortDescription, $message, $icon, $languageID = 0, $canSeeGroupIDs, $options, $attachmentList = null, $poll = null) {
		// get number of attachments
		$attachmentsAmount = ($attachmentList !== null ? count($attachmentList->getAttachments($this->pageID)) : 0);

		// update page
		$sql = "UPDATE	wcf".WCF_N."_content_page
			SET	".($languageID == 0 ? "subject = '".escapeString($subject)."'," : '')."
				".($languageID == 0 ? "shortDescription = '".escapeString($shortDescription)."'," : '')."
				message = '".escapeString($message)."',
				icon = '".escapeString($icon)."',
				canSeeGroupIDs = '".escapeString($canSeeGroupIDs)."',
				attachments = ".$attachmentsAmount.",
				pollID = ".$poll->pollID.",
				enableSmilies = ".(isset($options['enableSmilies']) ? $options['enableSmilies'] : 1).",
				enableHtml = ".(isset($options['enableHtml']) ? $options['enableHtml'] : 0).",
				enableBBCodes = ".(isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1)."
			WHERE 	pageID = ".$this->pageID;
		WCF::getDB()->sendQuery($sql);
		
		// update attachments
		if ($attachmentList != null) {
			$attachmentList->findEmbeddedAttachments($message);
		}
		
		// update poll
		if ($poll != null) {
			$poll->updateMessageID($this->pageID);
		}
		
		$sql = "SELECT	showOrder
			FROM	wcf".WCF_N."_page_menu_item
			WHERE	menuItem LIKE 'wcf.content.page.page-".$this->pageID."'";
		$row = WCF::getDB()->getFirstRow($sql);
		$oldShowOrder = $row['showOrder'];
		
		// delete page menu item
		if (CONTENTPAGE_ENABLE_PAGEMENU) {
			$this->deletePageMenuItem($this->pageID);
		}
		
		// delete page location
		$this->deletePageLocation($this->pageID);
		
		// update page menu item
		if (CONTENTPAGE_ENABLE_PAGEMENU) {
			if ($languageID == 0) {
				$menuItem = $subject;
			}
			else {
				$menuItem = 'wcf.content.page.page-'.$this->pageID;
			}
			$menuItemLink = 'index.php?page=ContentPage&pageID='.$this->pageID;
			$menuItemIconS = $icon.'S.png';
			$menuItemIconM = $icon.'M.png';
			$menuShowOrder = $oldShowOrder;
			$menuPosition = 'header';
			require_once(WCF_DIR.'lib/data/page/menu/PageMenuItemEditor.class.php');
			PageMenuItemEditor::create($menuItem, $menuItemLink, $menuItemIconS, $menuItemIconM, $menuShowOrder, $menuPosition, 0, WCF::getPackageID('de.community4wcf.contentPage'));
	
			// reset cache
			PageMenuItemEditor::clearCache();
		}
		
		// creates database entries
		if ($languageID == 0) {
			$locationName = $subject;
		}
		else {
			$locationName = 'wcf.content.page.page-'.$this->pageID;
		}

		$locationPattern = 'index\.php\?page=ContentPage&.*pageID='.$this->pageID;
		$packageID = WCF::getPackageID('de.community4wcf.contentPage');
		$sql = "INSERT INTO wcf".WCF_N."_page_location (locationPattern, locationName, packageID)
				VALUES ('".escapeString($locationPattern)."', '".escapeString($locationName)."', ".$packageID.")";
		WCF::getDB()->sendQuery($sql);
		
		WCF::getCache()->clear(WCF_DIR.'cache', '*.php', true);

		if ($languageID != 0) {
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array(
				'wcf.content.page.page-'.$this->pageID => $subject,
				'wcf.content.page.page-'.$this->pageID.'.shortDescription' => $shortDescription), 0, WCF::getPackageID('de.community4wcf.contentPage'),
			array('wcf.content.page.page-'.$this->pageID => 1, 'wcf.content.page.page-'.$this->pageID.'.shortDescription' => 1));
			LanguageEditor::deleteLanguageFiles($languageID, 'wcf.content.page');		
		}
	}
}
?>