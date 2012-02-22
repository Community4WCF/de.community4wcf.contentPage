<?php
// wcf imports
require_once(WCF_DIR.'lib/form/ContentPageAddForm.class.php');
 
/**
 * Shows the form for edit a content page.
 * 
 * @svn			$Id: C4WpagePage.class.php 1207 2010-04-09 23:32:46Z TobiasH87 $
 *
 * @author		Community4WCF
 * @copyright	2010 Community4WCF
 * @license		Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package		de.community4wcf.contentPage
 * @subpackage	form
 * @category 	WoltLab Community Framework 
 */
class ContentPageEditForm extends ContentPageAddForm {
	/**
	 * page id
	 *
	 * @var integer
	 */
	public $pageID = 0;
	
	/**
	 * page editor object
	 *
	 * @var ContentPageEditor
	 */
	public $page = null;
	
	/**
	 * languages
	 *
	 * @var array<string>
	 */
	public $languages = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		MessageForm::readParameters();
		
		// get page id
		if (isset($_REQUEST['pageID'])) $this->pageID = intval($_REQUEST['pageID']);
		// new page editor object
		$this->page = new ContentPageEditor($this->pageID);
		// check if page exists and check permission
		if (!$this->page->pageID || !$this->page->isEditable()) {
			throw new IllegalLinkException();
		}
		
		// language
		if (isset($_REQUEST['languageID'])) $this->languageID = intval($_REQUEST['languageID']);
		else $this->languageID = WCF::getLanguage()->getLanguageID();
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		MessageForm::save();

		// save poll
		if ($this->showPoll) {
			$this->pollEditor->save();
		}
		
		// update page
		$this->page->update($this->subject, $this->shortDescription, $this->text, $this->icon, $this->languageID, implode(",",$this->canSeeGroupIDs), $this->getOptions(), $this->attachmentListEditor, $this->pollEditor);
		$this->saved();
		
		// redirect to url
		WCF::getTPL()->assign(array(
			'url' => ('index.php?page=ContentPage&pageID='.$this->pageID.SID_ARG_2ND_NOT_ENCODED),
			'message' => WCF::getLanguage()->get('wcf.content.page.edit.successful'),
			'wait' => 5
		));
		WCF::getTPL()->display('redirect');
		exit;

	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// get all available languages
		$this->languages = WCF::getLanguage()->getLanguageCodes();
		
		// default values
		if (!count($_POST)) {
			$this->text = $this->page->message;
			$this->icon = $this->page->icon;
			$this->canSeeGroupIDs = explode(",",$this->page->canSeeGroupIDs);
			$this->attachments = $this->page->attachments;
			$this->enableSmilies =  $this->page->enableSmilies;
			$this->enableHtml = $this->page->enableHtml;
			$this->enableBBCodes = $this->page->enableBBCodes;
			
			if (WCF::getLanguage()->getLanguageID() != $this->languageID) {
				$language = new Language($this->languageID);
			}
			else {
				$language = WCF::getLanguage();
			}

			$this->subject = $language->get('wcf.content.page.page-'.$this->pageID);
			if ($this->subject == 'wcf.content.page.page-'.$this->pageID) $this->subject = "";
			$this->shortDescription = $language->get('wcf.content.page.page-'.$this->pageID.'.shortDescription');
			if ($this->shortDescription == 'wcf.content.page.page-'.$this->pageID.'.shortDescription') $this->shortDescription = "";
			
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'page' => $this->page,
			'pageID' => $this->pageID,
			'languages' => $this->languages
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// get attachments
		$this->attachmentListEditor = new MessageAttachmentListEditor(array($this->pageID), 'contentPage', WCF::getPackageID('de.community4wcf.contentPage'), WCF::getUser()->getPermission('user.contentPage.maxAttachmentSize'), WCF::getUser()->getPermission('user.contentPage.allowedAttachmentExtensions'), WCF::getUser()->getPermission('user.contentPage.maxAttachmentCount'));

		// get poll
		$this->pollEditor = new PollEditor($this->page->pollID, 0, 'contentPage', WCF::getUser()->getPermission('user.contentPage.canStartPublicPoll'));

		// set active menu items
		PageMenu::setActiveMenuItem('wcf.content.page.page-'.$this->pageID);
		
		parent::show();
	}
}
?>