<?php
/********************************************************/
/* Copyright (C) 2010 Tom Hartung, All Rights Reserved. */
/********************************************************/

/**
 * @version     $Id: helloWorld.php,v 1.3 2009/05/04 14:04:01 tomh Exp tomh $
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoobase
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php .
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// jimport( 'joomla.application.component.model' );

/**
 * Joomoo interface to joomla mail functionality
 * minimal class just in case we want to make a system-wide change to this functionality
 */

class JoomoobaseEmailer extends JObject
{
	/**
	 * email address of the sender of the email
	 * @access public
	 * @var string
	 */
	public $sender = null;
	/**
	 * name of the sender of the email
	 * ignored by sendEmailJMail, defaults to sender in sendEmailJUtility
	 * @access public
	 * @var string
	 */
	public $fromname = null;
	/**
	 * recipient of the email
	 * @access public
	 * @var string
	 */
	public $recipient = null;
	/**
	 * subject of the email
	 * @access public
	 * @var string
	 */
	public $subject;
	/**
	 * body of the email
	 * @access public
	 * @var string
	 */
	public $body;
	/**
	 * joomla mailer object
	 * @access private
	 * @var JMail object
	 */
	private $_mailer = null;

	/**
	 * Constructor
	 * @access public
	 */
	public function __construct( )
	{
		parent::__construct();
	}

	/**
	 * create and use a joomla JMail object to send an email
	 * @access public
	 * @return true if successful else false - use getError() to get error message
	 * @see http://api.joomla.org/Joomla-Framework/Mail/JMail.html
	 */
	public function sendEmailJMail( )
	{
		if ( $this->recipient == null )
		{
			$this->setError( 'No recipient specified!' );
			return FALSE;
		}

		if ( $this->_mailer == null )
		{
			$this->_mailer =& JFactory::getMailer();
		}

		$this->_checkSender();
		$this->_mailer->setSender( $this->sender );
		$this->_mailer->addRecipient( $this->recipient );
		$this->_mailer->setSubject( $this->subject );
		$this->_mailer->setBody( $this->body );

		$returnValue = $this->_mailer->Send();
		//	print 'JoomoobaseEmailer::sendEmailJMail return value:<br />';
		//	print_r( $returnValue );
		//	print '<br />';

		return $returnValue;
	}
	/**
	 * Check whether the sender has been set and if not set it
	 * @access private
	 * @return void
	 */
	private function _checkSender( )
	{
		if ( $this->sender == null )
		{
			JFactory::getConfig( "../../../configuration.php" );
			$config = new JConfig();
			$this->sender = $config->mailfrom;
		}
	}
	/**
	 * use JUtility sendmail method to send an email
	 * @access public
	 * @return true if successful
	 * @see http://api.joomla.org/Joomla-Framework/Utilities/JUtility.html
	 * @note this function won't work in an ajax call (outside of the framework)
	 * but it does check for at least one error case (no recipient)
	 */
	public function sendEmailJUtility( )
	{
		if ( $this->recipient == null )
		{
			$this->setError( 'Unable to send mail: no recipient specified.' );
			return FALSE;
		}

		$this->_checkSender();

		if ( $this->fromname == null )
		{
			$this->fromname = $this->sender;
		}

		$returnValue = JUtility::sendMail(
				$this->sender,
				$this->fromname,
				$this->recipient,
				$this->subject,
				$this->body );
		//	print 'JoomoobaseEmailer::sendEmailJUtility return value:<br />';
		//	print_r( $returnValue );
		//	print '<br />';

		return $returnValue;
	}
}
?>
