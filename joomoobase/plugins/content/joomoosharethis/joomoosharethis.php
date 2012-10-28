<?php
/**
 * @version		$Id: example.php 10714 2008-08-21 10:10:14Z eddieajau $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2009 - 2010 Tom Hartung. All rights reserved.
 * @license		TBD
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Plugin to add share this link to Content
 *
 * @package		Joomoo
 * @subpackage	joomoobase
 * @since 		1.5
 */
class plgContentJoomooShareThis extends JPlugin
{
	/**
	 * placeholder regular expression indicating where to put javascript for share this button
	 * value is JOOMOO_SHARETHIS_PLACEHOLDER ensconced in delimiters; JOOMOO_SHARETHIS_PLACEHOLDER defined in assets/constants.php
	 * @access private
	 * @var string
	 */
	private $_placeholderRegEx;

	/**
	 * javascript to display a share this button - from sharethis.com
	 * @access private
	 * @var string
	 */
	//	private $_javascript = '<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=2d1ac5f7-86de-4973-ba08-200e152c65a7&amp;type=website"></script>';
	private $_javascript = '<script type="text/javascript" src="http://sharethis.com"></script>';

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	public function __construct( &$subject, $params )
	{
		parent::__construct( $subject, $params );

		$document =& JFactory::getDocument();  // JDocumentHTML object
		$document->addStyleSheet( DS.'components'.DS.'com_joomoobase'.DS.'assets'.DS.'joomoobase.css' );
		$constantsFilePath = JPATH_SITE.DS.'components'.DS.'com_joomoobase'.DS.'assets'.DS.'constants.php';
		require_once $constantsFilePath;
		$this->_placeholderRegEx = '&' . JOOMOO_SHARETHIS_PLACEHOLDER . '&';
	}

	/**
	 * if article contains the place holder string, substitute javascript for share this button
	 * @param	string	The type of content being passed, eg. 'com_content.article'
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	public function onContentPrepare( $context, &$article, &$params, $page=0 )
	{
		$sharethis_url = $this->params->get('sharethis_url','');
		$this->_javascript = '<script type="text/javascript" src="' . $sharethis_url . '"></script>';

		$html = '';
		//	$html .= '<p class="joomoosharethis">this->_javascript = "' . $this->_javascript . '"</p>';
		$html .= '<center><p class="joomoosharethis">' . $this->_javascript . '</p></center>';
		$result = preg_replace( $this->_placeholderRegEx, $html, $article->text );

		if ( $result )       // if an error occurred don't use result
		{
			$article->text = $result;
		}

		//	$article->text .= 'sharethis_url = ' . $sharethis_url . '<br />';

		return '';
	}
}
