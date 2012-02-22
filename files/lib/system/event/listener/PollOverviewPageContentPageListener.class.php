<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Checks the permissions for viewing the poll overview page and shows the poll.
 * 
 * @svn			$Id: C4WpagePage.class.php 1207 2010-04-09 23:32:46Z TobiasH87 $
 *
 * @author		Community4WCF
 * @copyright	2010 Community4WCF
 * @license		Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		de.community4wcf.contentPage
 * @subpackage	system.event.listener
 * @category 	WoltLab Community Framework
 */
class PollOverviewPageContentPageListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($eventObj->poll->messageType == 'contentPage') {
			// check permissions
			require_once(WCF_DIR.'lib/data/contentPage/ContentPage.class.php');
			$page = new ContentPage($eventObj->poll->messageID);
			$page->enter();
			$eventObj->canVotePoll = WCF::getUser()->getPermission('user.contentPage.canVotePoll');
			
			// plug in breadcrumbs
			WCF::getTPL()->assign(array(
				'page' => $page
			));
			WCF::getTPL()->append('specialBreadCrumbs', '<ul class="breadCrumbs"><li><a href="index.php?page=Index'.SID_ARG_1ST.'"><img src="'.StyleManager::getStyle()->getIconPath('indexS.png').'" alt="" /> <span>'.WCF::getLanguage()->get(PAGE_TITLE).'</span></a> &raquo;</li> <li><a href="index.php?page=ContentPage&amp;pageID='.$page->pageID.SID_ARG_1ST.'"><img src="'.StyleManager::getStyle()->getIconPath($page->icon.'S.png').'" alt="" /> <span>'.WCF::getLanguage()->get('wcf.content.page.page-'.$page->pageID).'</span></a> &raquo;</li></ul>');
			
		// set active menu items
		PageMenu::setActiveMenuItem('wcf.content.page.page-'.$page->pageID);
		}
	}
}
?>