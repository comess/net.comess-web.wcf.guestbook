<?php
namespace wcf\form;
use wcf\data\guestbook\Guestbook;
use wcf\data\guestbook\GuestbookAction;
use wcf\data\guestbook\GuestbookEditor;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\Regex;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * @author       Philipp Bornemann
 * @copyright	2013 Comess Web.Net
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class AddGuestbookEntryForm extends AbstractForm {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.guestbook';
	
	/**
	 * @var	string
	 */
	public $username = '';
	
	/**
	 * @var	string
	 */
	public $email = '';
	
	/**
	 * @var	string
	 */
	public $website = '';
		
	/**
	 * @var	string
	 */
	public $message = '';

	/**
	 * @see	wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('user.guestbook.canAddEntries');
	
	/**
	 * @see	wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_GUESTBOOK');
	
	/**
	 * @see	wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'addGuestbookEntry';
	
	/**
	 * @see	wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
		if (isset($_POST['email'])) $this->email = StringUtil::trim($_POST['email']);
		if (isset($_POST['website'])) $this->website = StringUtil::trim($_POST['website']);
		if (isset($_POST['message'])) $this->message = StringUtil::trim($_POST['message']);
	}

	/**
	 * @see	wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->username)) {
			throw new UserInputException('username');
		}
		
		if (!Regex::compile('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', Regex::CASE_INSENSITIVE)->match($this->email)) {
			throw new UserInputException('email', 'notValid');
		}
		
		if (!Regex::compile('/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', Regex::CASE_INSENSITIVE)->match($this->website)) {
			throw new UserInputException('website', 'notValid');
		}
		
		if (empty($this->message)) {
			throw new UserInputException('message');
		}
	}
	
	/**
	 * @see	wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$this->objectAction = new GuestbookAction(array(), 'create', array('data' => array(
			'username' => $this->username,
			'email' => $this->email,
			'website' => $this->website,
			'message' => $this->message
		)));
		$this->objectAction->executeAction();

		$this->saved();

		WCF::getTPL()->assign(array(
			'success' => true
		));
	}
	
	/**
	 * @see	wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
				
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'username' => $this->username,
			'email' => $this->email,
			'website' => $this->website,
			'message' => $this->message
		));
	}
}
?>
