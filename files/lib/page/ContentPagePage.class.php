<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/contentPage/ContentPage.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows the content page.
 * 
 * @svn			$Id: C4WpagePage.class.php 1207 2010-04-09 23:32:46Z TobiasH87 $
 *
 * @author		Community4WCF
 * @copyright	2010 Community4WCF
 * @license		Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		de.community4wcf.contentPage
 * @subpackage	page
 * @category 	WoltLab Community Framework
 */
class ContentPagePage extends AbstractPage {
	// system
	public $templateName = 'contentPage';

	/**
	 * page id
	 * 
	 * @var integer
	 */
	public $pageID = 0;
	
	/**
	 * content page instance
	 * 
	 * @var ContentPage
	 */
	public $page = null;
	
	/**
	 * attachment list object
	 * 
	 * @var	MessageAttachmentList
	 */
	public $attachmentList = null;
	
	/**
	 * list of attachments
	 * 
	 * @var	array<Attachment>
	 */
	public $attachments = array();
	
	/**
	 * list of polls
	 * 
	 * @var	array<Poll>
	 */
	public $polls;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// page id
		if (isset($_REQUEST['pageID'])) $this->pageID = intval($_REQUEST['pageID']);
		// create new content page instance
		$this->page = new ContentPage($this->pageID);
		// enter page
		$this->page->enter();
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read attachments
		if (MODULE_ATTACHMENT == 1 && $this->page->attachments > 0) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentList.class.php');
			$this->attachmentList = new MessageAttachmentList($this->pageID, 'contentPage', '', WCF::getPackageID('de.community4wcf.contentPage'));
			$this->attachmentList->readObjects();
			$this->attachments = $this->attachmentList->getSortedAttachments(WCF::getUser()->getPermission('user.contentPage.canViewAttachmentPreview'));
			
			// set embedded attachments
			if (WCF::getUser()->getPermission('user.contentPage.canViewAttachmentPreview')) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachments);
			}
			
			// remove embedded attachments from list
			if (count($this->attachments) > 0) {
				MessageAttachmentList::removeEmbeddedAttachments($this->attachments);
			}
		}
		
		// read polls
		if (MODULE_POLL == 1 && $this->page->pollID) {
			require_once(WCF_DIR.'lib/data/message/poll/Polls.class.php');
			$this->polls = new Polls($this->page->pollID, WCF::getUser()->getPermission('user.contentPage.canVotePoll'), 'index.php?page=ContentPage&pageID='.$this->pageID);
		}
		
	}
	
	/**
	* @see Page::assignVariables();
	*/
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'page' => $this->page,
			'pageID' => $this->pageID,
			'allowSpidersToIndexThisPage' => true,
			'attachments' => $this->attachments,
			'polls' => $this->polls,
		));
	}
	
	
	/**
	 * @see Page::show()
	 */
	public function show() {		
		// set active menu item
		PageMenu::setActiveMenuItem('wcf.content.page.page-'.$this->pageID);
		
		// check module options
		if (MODULE_CONTENT_PAGE != 1) {
			throw new IllegalLinkException();
		}
		
		// show page
		parent::show();
	}
	
}
?>