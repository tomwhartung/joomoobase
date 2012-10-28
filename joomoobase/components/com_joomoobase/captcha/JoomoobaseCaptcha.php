<?php
/********************************************************/
/* Copyright (C) 2010 Tom Hartung, All Rights Reserved. */
/********************************************************/

/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoobase
 * @copyright   Copyright (C) 2009 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php .
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * class to serve as a generic interface to multiple types of captcha
 */
class JoomoobaseCaptcha extends JObject
{
	/**
	 * captcha type - set in back end to one of the CAPTCHA_TYPE_* constants in constants.php
	 * @access private
	 */
	private $_captcha_type;

	/**
	 * Constructor
	 * @access public
	 */
	public function __construct( $captcha_type, $captchaPathPrefix=null )
	{
		//	print 'Hi from JoomoobaseCaptcha::_construct<br />';

		$this->_captcha_type = $captcha_type;

		if ( $captchaPathPrefix == null )
		{
			$recaptchaFilePath   = RECAPTCHA_FILE_PATH;
			$opencaptchaFilePath = OPENCAPTCHA_FILE_PATH;
		}
		else
		{
			$recaptchaFilePath   = $captchaPathPrefix .DS. 'captcha' .DS. RECAPTCHA_VERSION .DS. RECAPTCHA_FILENAME;
			$opencaptchaFilePath = $captchaPathPrefix .DS. 'captcha' .DS.  OPENCAPTCHA_VERSION .DS. OPENCAPTCHA_FILENAME;
		}

		switch ( $this->_captcha_type )
		{
			case CAPTCHA_TYPE_RECAPTCHA:
				require_once $recaptchaFilePath;
				break;
			default:
			case CAPTCHA_TYPE_OPENCAPTCHA:
				require_once $opencaptchaFilePath;
				break;
		}
	}

	/**
	 * returns string containing xhml to display captcha image and input tag for specified captcha type
	 * @access public
	 */
	public function getCaptchaString()
	{
		$captchaString = '';

		switch ( $this->_captcha_type )
		{
			case CAPTCHA_TYPE_RECAPTCHA:
				if ( RECAPTCHA_DARK_BACKGROUND )
				{
					$captchaString .= '<noscript>' . "\n";
					$captchaString .= ' <div class="recaptcha_dark_background">' . "\n";
					$captchaString .= '  <p>You have disabled javascript in your browser.' . "\n";
					$captchaString .=   ' To post your comment you must follow these steps:</p>' . "\n";
					$captchaString .= '  <ol>' . "\n";
					$captchaString .= '   <li>Type the two words in the box as indicated</li>' . "\n";
					$captchaString .= '   <li>Click on the "I\'m a Human" button</li>' . "\n";
					$captchaString .= '   <li>Select (Ctrl-A), copy (Ctrl-C) and paste (Ctrl-V) the code that appears into';
					$captchaString .=    ' the text box at the bottom of the form</li>' . "\n";
					$captchaString .= '   <li>Click on the "Post Your Comment" button</li>' . "\n";
					$captchaString .= '  </ol>' . "\n";
					$captchaString .= '  <p>To facilitate posting comments in the future, please consider changing your browser\'s';
					$captchaString .=   ' preferences so that javascript is enabled for this site.</p>' . "\n";
					$captchaString .= ' </div>' . "\n";
					$captchaString .= '</noscript>' . "\n";
				}
				$captchaString .= recaptcha_get_html(RECAPTCHA_PUBLIC_KEY) . "\n";
				break;
			default:
			case CAPTCHA_TYPE_OPENCAPTCHA:
				$captchaString .= getOpenCaptchaHtml() . "\n";
				break;
		}

		return $captchaString;
	}
	/**
	 * checks response for specified captcha type
	 * @access public
	 * @return boolean True if response is correct else False
	 */
	public function checkCaptchaResponse()
	{
		switch ( $this->_captcha_type )
		{
			case CAPTCHA_TYPE_RECAPTCHA:
				$response = null;
				$response = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY,
					$_SERVER["REMOTE_ADDR"],
					$_POST["recaptcha_challenge_field"],
					$_POST["recaptcha_response_field"]);
				$answerOk = $response->is_valid;
				if ( ! $answerOk )
				{   
					$message  = 'The reCAPTCHA wasn\'t entered correctly. Go back and try it again. ';
					$message .= 'Error message: ' . $response->error;
					$this->setError( $message );
				}
				break;
			default:
			case CAPTCHA_TYPE_OPENCAPTCHA:
				$answerOk = checkOpenCaptchaResponse();
				if ( ! $answerOk )
				{   
					$message = 'Unable to verify captcha string entered. You may want to try it again. ';
					$this->setError( $message );
				}
				break;
		}

		return $answerOk;
	}
}

?>
