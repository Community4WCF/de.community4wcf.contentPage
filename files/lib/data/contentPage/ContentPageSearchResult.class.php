<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/util/SearchResultTextParser.class.php');
require_once(WCF_DIR.'lib/data/contentPage/ContentPage.class.php');

/**
 * This class extends the content page by functions for a search result output.
 * 
 * @svn			$Id: ContentPageSearchResult.class.php 1460 2010-05-23 14:58:35Z TobiasH87 $
 *
 * @author		Community4WCF
 * @copyright	2010 Community4WF
 * @license		Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		de.community4wcf.contentPage
 * @subpackage	data.contentPage
 * @category	WoltLab Community Framework 
 */
class ContentPageSearchResult extends ContentPage {
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		$this->data['messagePreview'] = true;
	}

	/**
	 * @see ContentPage::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		return SearchResultTextParser::parse(parent::getFormattedMessage());
	}
}
?>