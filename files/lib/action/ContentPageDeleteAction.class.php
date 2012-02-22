<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');
require_once(WCF_DIR.'lib/data/contentPage/ContentPageEditor.class.php');
 
/**
 * Deletes a content page.
 * 
 * @svn			$Id: C4WpagePage.class.php 1207 2010-04-09 23:32:46Z TobiasH87 $
 *
 * @author		Community4WCF
 * @copyright	2010 Community4WCF
 * @license		Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		de.community4wcf.contentPage
 * @subpackage	action
 * @category	WoltLab Community Framework
 */
class ContentPageDeleteAction extends AbstractSecureAction {
	/**
	 *  page id
	 *
	 * @var integer
	 */
	public $pageID = 0;
	
	/**
	 * content page editor object
	 *
	 * @var ContentPageEditor
	 */
	public $page = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get page
		if (isset($_REQUEST['pageID'])) $this->pageID = intval($_REQUEST['pageID']);
		$this->page = new ContentPageEditor($this->pageID);
		$this->page->enter();

		// check, if page is deletable
		if (!$this->page->isDeletable()) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// delete page
		$this->page->delete();
		
		// call event
		$this->executed();
		
		// forward
		HeaderUtil::redirect('index.php?page=Index'.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>