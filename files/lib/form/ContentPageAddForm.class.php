<?php
// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/data/contentPage/ContentPageEditor.class.php');
require_once(WCF_DIR.'lib/data/message/poll/PollEditor.class.php');
require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows the form for adding a new content page.
 * 
 * @svn			$Id: C4WpagePage.class.php 1207 2010-04-09 23:32:46Z TobiasH87 $
 *
 * @author		Community4WCF
 * @copyright	2010 Community4WCF
 * @license		Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package		de.community4wcf.contentPage
 * @subpackage	form
 * @category	WoltLab Community Framework 
 */
class ContentPageAddForm extends MessageForm {
	// system
	public $templateName = 'contentPageAdd';
	public $useCaptcha = 1;
	public $showSignatureSetting = false;
	public $preview, $send;
	public $pageID = 0;
	
	/**
	 * attachment list editor
	 * 
	 * @var	AttachmentListEditor
	 */
	public $attachmentListEditor = null;
	
	/**
	 * poll editor object
	 * 
	 * @var	PollEditor
	 */
	public $pollEditor = null;
	
	// form parameters
	public $icon = '';
	public $shortDescription = '';
	public $languageID = 0;
	public $canSeeGroupIDs = array(1);
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		$this->canSeeGroupIDs = array();
		
		if (isset($_POST['icon'])) $this->icon = StringUtil::trim($_POST['icon']);
		if (isset($_POST['shortDescription'])) $this->shortDescription = StringUtil::trim($_POST['shortDescription']);
		if (isset($_POST['languageID'])) $this->languageID = intval($_POST['languageID']);
		if (isset($_POST['canSeeGroupIDs'])) $this->canSeeGroupIDs = ArrayUtil::toIntegerArray($_POST['canSeeGroupIDs']);
		if (isset($_POST['preview'])) $this->preview = (boolean) $_POST['preview'];
		if (isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
	}
	
	/**
	 * @see Form::submit()
	 */
	public function submit() {
		// call submit event
		EventHandler::fireAction($this, 'submit');
		
		$this->readFormParameters();
		
		try {
			// attachment handling
			if ($this->showAttachments) {
				$this->attachmentListEditor->handleRequest();
			}
			
			// poll handling
			if ($this->showPoll) {
				$this->pollEditor->readParams();
			}
				
			// preview
			if ($this->preview) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachmentListEditor->getSortedAttachments());
				WCF::getTPL()->assign('preview', ContentPageEditor::createPreview($this->subject, $this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes));
			}
			// save message
			if ($this->send) {
				$this->validate();
				// no errors
				$this->save();
			}
		}
		catch (UserInputException $e) {
			$this->errorField = $e->getField();
			$this->errorType = $e->getType();
		}
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// validate url
		if (empty($this->icon)) {
			throw new UserInputException('icon', 'empty');
		}
		
		// poll
		if ($this->showPoll) $this->pollEditor->checkParams();
		
		// count group ids who can see the new page
		if (count($this->canSeeGroupIDs) < 1) {
			throw new UserInputException('canSeeGroupIDs', 'empty');
		}

	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save poll
		if ($this->showPoll) {
			$this->pollEditor->save();
		}
		
		// save page
		$this->page = ContentPageEditor::create($this->subject, $this->text, $this->shortDescription, $this->icon,  WCF::getLanguage()->getLanguageID(), $this->canSeeGroupIDs, $this->getOptions(), $this->attachmentListEditor, $this->pollEditor);
		$this->saved();
		
		// redirect
		WCF::getTPL()->assign(array(
			'url' => ('index.php?page=ContentPage&pageID='.$this->page->pageID.SID_ARG_2ND_NOT_ENCODED),
			'message' => WCF::getLanguage()->get('wcf.content.page.pageAdd.success'),
			'wait' => 5
		));
		WCF::getTPL()->display('redirect');
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'pageID' => $this->pageID,
			'shortDescription' => $this->shortDescription,
			'icon' => $this->icon,
			'languageID' => $this->languageID,
			'canSeeGroupIDs' => $this->canSeeGroupIDs,
			'availableGroups'	=> $this->getGroups()
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {		
		// check permission
		WCF::getUser()->checkPermission('user.contentPage.canAddContentPage');
		
		// check module options
		if (MODULE_CONTENT_PAGE != 1) {
			throw new IllegalLinkException();
		}
		
		// check upload permission
		if (MODULE_ATTACHMENT != 1  || !WCF::getUser()->getPermission('user.contentPage.canUploadAttachment')) {
			$this->showAttachments = false;
		}
		
		// get attachments editor
		if ($this->attachmentListEditor == null) {
			$this->attachmentListEditor = new MessageAttachmentListEditor(array(), 'contentPage', WCF::getPackageID('de.community4wcf.contentPage'), WCF::getUser()->getPermission('user.contentPage.maxAttachmentSize'), WCF::getUser()->getPermission('user.contentPage.allowedAttachmentExtensions'), WCF::getUser()->getPermission('user.contentPage.maxAttachmentCount'));
		}
		
		// check poll permission
		if (MODULE_POLL != 1 || !WCF::getUser()->getPermission('user.contentPage.canStartPoll')) {
			$this->showPoll = false;
		}
		
		// get poll editor
		if ($this->pollEditor == null) $this->pollEditor = new PollEditor(0, 0, 'contentPage', WCF::getUser()->getPermission('user.contentPage.canStartPublicPoll'));
		
		parent::show();
	}
	
	/**
	 * Returns a list of all available user groups.
	 * 
	 * @return	array
	 */
	private function getGroups() {
		require_once(WCF_DIR.'lib/data/user/group/Group.class.php');
		return Group::getAllGroups();
	}	
}
?>