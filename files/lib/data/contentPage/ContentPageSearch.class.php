<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/search/AbstractSearchableMessageType.class.php');
require_once(WCF_DIR.'lib/data/contentPage/ContentPageSearchResult.class.php');
 
/**
 * An implementation of SearchableMessageType for searching in the content page.
 * 
 * @svn			$Id: ContentPageSearch.class.php 1461 2010-05-23 18:07:22Z TobiasH87 $
 *
 * @author		Community4WCF
 * @copyright	2010 Community4WF
 * @license		Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		de.community4wcf.contentPage
 * @subpackage	data.contentPage
 * @category	WoltLab Community Framework 
 */
class ContentPageSearch extends AbstractSearchableMessageType {
	protected $messageCache = array();
	
	/**
	 * Caches the data of the messages with the given ids.
	 */
	public function cacheMessageData($messageIDs, $additionalData = null) {
		// get items
		$sql = "SELECT		content_page.*
			FROM		wcf".WCF_N."_content_page content_page
			WHERE		content_page.pageID IN (".$messageIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($this->isSeeable($row['canSeeGroupIDs'])) {
				$item = new ContentPageSearchResult(null, $row);
				$this->messageCache[$row['pageID']] = array('type' => 'contentPage', 'message' => $item);
			}
		}
	}
	
	/**
	 * Check if page(s) is/are seeable.
	 */
	public function isSeeable($groupIDs) {
		$canSeeGroupIDs = explode(',', $groupIDs);
		
		// check view permission
		foreach ($canSeeGroupIDs as $group) {
			if (Group::isMember($group)) {
				return true;
			}
		}
		
	}
	
	/**
	 * @see SearchableMessageType::getMessageData()
	 */
	public function getMessageData($messageID, $additionalData = null) {
		if (isset($this->messageCache[$messageID])) return $this->messageCache[$messageID];
		return null;
	}
	
	/**
	 * Returns the database table name for this search type.
	 */
	public function getTableName() {
		return 'wcf'.WCF_N.'_content_page';
	}
	
	/**
	 * Returns the message id field name for this search type.
	 */
	public function getIDFieldName() {
		return 'pageID';
	}
	
	/**
	 * @see SearchableMessageType::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'searchResultContentPage';
	}
}
?>