<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/Message.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');

/**
 * Represents a content page.
 *
 * @svn			$Id: C4WpagePage.class.php 1207 2010-04-09 23:32:46Z TobiasH87 $
 *
 * @author		Community4WCF
 * @copyright	2010 Community4WF
 * @license		Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		de.community4wcf.contentPage
 * @subpackage	data.contentPage
 * @category	WoltLab Community Framework 
 */
class ContentPage extends Message {

	/**
	 * Creates a new page object.
	 *
	 * @param	integer		$itemID		id of a page
	 * @param array 		$row		resultset with item data form database
	 */
	public function __construct($pageID, $row = null) {		
		if ($pageID !== null) {			
			$sql = "SELECT		*
					FROM		wcf".WCF_N."_content_page content_page
					WHERE	content_page.pageID = ".$pageID;
			$row = WCF::getDB()->getFirstRow($sql);			
		}
		parent::__construct($row);	
		$this->messageID = $row['pageID'];
	}
	
	/**
	 * Enters item and checks permission.
	 */
	public function enter() {		
		// check if item exists
		if (!$this->pageID) {
			throw new IllegalLinkException();
		}
		
		if (!$this->isSeeable() && !WCF::getUser()->getPermission('user.contentPage.canViewContentPage')) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Checks if item is seeable.
	 */
	public function isSeeable() {
		$canSeeGroupIDs = explode(',', $this->canSeeGroupIDs);
		
		// check view permission
		foreach ($canSeeGroupIDs as $group) {
			if (Group::isMember($group)) {
				return true;
			}
		}
		
	}
	
	/**
	 * Returns true, if the active user can edit this item.
	 * 
	 * @return	boolean
	 */
	public function isEditable() {
		if (($this->userID == WCF::getUser()->userID && WCF::getUser()->userID) || WCF::getUser()->getPermission('mod.contentPage.canEditContentPage')) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns true, if the active user can delete this item.
	 * 
	 * @return	boolean
	 */
	public function isDeletable() {
		if (($this->userID == WCF::getUser()->userID && WCF::getUser()->userID) || WCF::getUser()->getPermission('mod.contentPage.canDeleteContentPage')) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns formatted message.
	 *
	 * @return	string
	 */	
	public function getFormattedMessage() {
		require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
		MessageParser::getInstance()->setOutputType('text/html');
		require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
		AttachmentBBCode::setMessageID($this->pageID);
		return MessageParser::getInstance()->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, !$this->messagePreview);
	}
	
	/**
	 * Returns the user object.
	 * 
	 * @return	UserProfile
	 */
	public function getAuthor() {
		return new UserProfile($this->userID);
	}
	
}
?>