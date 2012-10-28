<?php
/**
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2010 - Tom Hartung.  All rights reserved.
 * @license		TBD.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

// // This is a ContentViewFrontpage object
// $this_class = get_class( $this );
// print "Loading plgContentJoomoocomments.php: this class = " . $this_class . "<br />\n";

/**
 * Plugin to display parameters passed to content plugins
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.5
 */
class plgContentJoomoodebug extends JPlugin
{
	/**
	 * User currently logged in - or not
	 * @var JUser Object
	 */
	private $_user;
	/**
	 * text we append to post
	 * @var string
	 */
	private $_appendText;

	/**
	 * Constructor
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	public function plgContentJoomoodebug( &$subject, $params )
	{
		parent::__construct( $subject, $params );

		$this->_appendText = '';

		$document =& JFactory::getDocument();  // JDocumentHTML object
		$document->addStyleSheet( DS.'components'.DS.'com_joomoobase'.DS.'assets'.DS.'joomoobase.css' );
		$document->addScript( DS.'components'.DS.'com_joomoobase'.DS.'javascript'.DS.'myTypeOf.js' );

		$baseConstantsFilePath = JPATH_SITE.DS.'components'.DS.'com_joomoobase'.DS.'assets'.DS.'constants.php';
		require_once( $baseConstantsFilePath );
		$this->_user = & JFactory::getUser();

		//	JHTML::_('behavior.modal');     // adds link tag for modal.css and script tag for modal.js to page header
	}

	//
	// ---------------------------------------------
	// these methods are useful for debugging - only
	// ---------------------------------------------
	// uncomment lines and what-not as appropriate to see what's what
	//
	/**
	 * Example prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param	string	The type of content being passed, eg. 'com_content.article'
	 * @param 	object	The article object.  Note $article->text is also available
	 * @param 	object	The article params
	 * @param 	int		The 'page' number
	 * @access	public
	 * @return	string
	 */
	public function onContentPrepare( $context, &$article, &$params, $page=0 )
	{
		$prependText  = '';
		$appendText  = '';

		//
		// article->text - what the system is going to print now
		//     it equals either the introtext, fulltext, or introtext + fulltext
		// article->introtext - text the user sees before clicking on the read more link
		// article->fulltext - text the user sees only after clicking on the read more link
		//
		$prependText .= '<p>plgContentJoomoodebug::onContentPrepare: PREpending this to article->text</p>';
		$appendText .= '<p>plgContentJoomoodebug::onContentPrepare: APPending this to article->text</p>';

		//	$appendText  = '<br />Returning this "_seeWhatWeHaveHere" text from plgContentJoomoodebug::onContentPrepare().<br />';
		//	$appendText .= $this->_seeWhatWeHaveHere( $context, $article, $params );
		//	$appendText .= '<br />End of "_seeWhatWeHaveHere" text from plgContentJoomoodebug::onContentPrepare().<br />';

		$article->text = $prependText . $article->text . $appendText;
	}

	/**
	 * Example before display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param	string	The type of content being passed, eg. 'com_content.article'
	 * @param 	object	The article object.  Note $article->text is also available
	 * @param 	object	The article params
	 * @param 	int		The 'page' number
	 * @access	public
	 * @return	string
	 */
	public function onContentBeforeDisplay( $context, &$article, &$params=null, $limitstart=null )
	{
		$this->_appendText = '';
		$returnText = '';

		$prependText = '';
		$appendText  = '';
		$prependText .= '<p>plgContentJoomoodebug:onContentBeforeDisplay: PREpending this to article->text</p>';
		$appendText .= '<p>plgContentJoomoodebug::onContentBeforeDisplay: APPending this to article->text</p>';
		$article->text = $prependText . $article->text . $appendText;

		$returnText .= '<div>' . "\n";
		$returnText .= ' <p>Returning this paragraph from plgContentJoomoodebug::onContentBeforeDisplay()</p>' . "\n";
		$returnText .= '</div>' . "\n";

		return $returnText;
	}

	/**
	 * On after display content is when we can display debug information
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 * Uncomment appropriate lines in this method to see what we have in $article and learn about other things
	 * @param	string	The type of content being passed, eg. 'com_content.article'
	 * @param 	object	The article object.  Note $article->text is also available
	 * @param 	object	The article params
	 * @param 	int		The 'page' number
	 * @access	public
	 * @return	string
	 */
	public function onContentAfterDisplay( $context, &$article, &$params, $limitstart )
	{
		$prependText = '';
		$appendText  = '';
		$prependText .= '<p>plgContentJoomoodebug:onContentAfterDisplay: PREpending this to article->text</p>';
		$appendText .= '<p>plgContentJoomoodebug::onContentAfterDisplay: APPending this to article->text</p>';
		$article->text = $prependText . $article->text . $appendText;

		$this->_appendText = '';
		$this->_appendText = '<br />Returning this text from plgContentJoomoodebug::onContentAfterDisplay().<br />';
		$this->_appendText = $this->_seeWhatWeHaveHere( $context, $article, $params );
		$article->text .= $this->_appendText;

		return $this->_appendText;
	}
	/**
	 * run print_r on article and params arguments, and other objects, so we can see what we have to work with
	 * to see this output make sure uncomment the appropriate lines in onContentAfterDisplay()
	 * @param	string	The type of content being passed, eg. 'com_content.article'
	 * @param	object	The article object
	 * @param 	object	The article params
	 * @access private
	 * @return	string	print_r output for the two objects
	 */
	private function _seeWhatWeHaveHere( $context, $article, $params )
	{
		$debugHtml  = '';
		$debugHtml .= '<br />----------------------------------';
		$debugHtml .= '<br />Running print_r on context object:';
		$debugHtml .= '<br />----------------------------------<br />';
		$debugHtml .= print_r( $context, TRUE );
		$debugHtml .= '<br />----------------------------------------';
		$debugHtml .= '<br />End of print_r output for context object.';
		$debugHtml .= '<br />----------------------------------------<br />';

		$debugHtml .= '<br />----------------------------------';
		$debugHtml .= '<br />Running print_r on article object:';
		$debugHtml .= '<br />----------------------------------<br />';
		$debugHtml .= print_r( $article, TRUE );
		$debugHtml .= '<br />----------------------------------------';
		$debugHtml .= '<br />End of print_r output for article object.';
		$debugHtml .= '<br />----------------------------------------<br />';
		$debugHtml .= isset($article->s_ordering)  ? "<br />article->s_ordering = "  . $article->s_ordering : '';
		$debugHtml .= isset($article->cc_ordering) ? "<br />article->cc_ordering = " . $article->cc_ordering : '';
		$debugHtml .= isset($article->a_ordering)  ? "<br />article->a_ordering = "  . $article->a_ordering : '';
		$debugHtml .= isset($article->f_ordering)  ? "<br />article->f_ordering = "  . $article->f_ordering : '';
		$debugHtml .= isset($article->ordering)    ? "<br />article->ordering = " . $article->ordering : '';
		$debugHtml .= isset($article->version)     ? "<br />article->version = "  . $article->version : '';
		$debugHtml .= isset($article->readmore_link) ? "<br />article->readmore_link = " . $article->readmore_link : '';
		$debugHtml .= isset($article->popup) ? "<br />article->popup = " . $article->popup : '';

		$paramsClass = get_class( $article->params );
		$debugHtml .= "<br />class of article->params is " . $paramsClass;

		if ( isset($article->parameters) && is_object($article->parameters) )
		{
			$parametersClass = get_class( $article->parameters );
			$debugHtml .= "<br />class of article->parameters is " . $parametersClass;
		}

		$debugHtml .= '<br />---------------------------------';
		$debugHtml .= '<br />Running print_r on params object:';
		$debugHtml .= '<br />---------------------------------<br />';
		$debugHtml .= print_r( $params, TRUE );
		$debugHtml .= '<br />---------------------------------------';
		$debugHtml .= '<br />End of print_r output for params object.';
		$debugHtml .= '<br />----------------------------------------<br />';
		$debugHtml .= "<br />params->get('show_item_navigation') = " . $params->get('show_item_navigation');
		$debugHtml .= '<br />';

		//	$debugHtml .= '<br />---------------------------------';
		//	$debugHtml .= '<br />Running print_r on this->params object:';
		//	$debugHtml .= '<br />---------------------------------<br />';
		//	$debugHtml .= print_r( $this->params, TRUE );
		//	$debugHtml .= '<br />---------------------------------------';
		//	$debugHtml .= '<br />End of print_r output for this->params object.';
		//	$debugHtml .= '<br />----------------------------------------<br />';
		//	$plugin =& JPluginHelper::getPlugin( 'content', 'joomoocomments' );
		//	$debugHtml .= '<br />---------------------------------';
		//	$debugHtml .= '<br />Running print_r on plugin object:';
		//	$debugHtml .= '<br />---------------------------------<br />';
		//	$debugHtml .= print_r( $plugin, TRUE );
		//	$debugHtml .= '<br />---------------------------------------';
		//	$debugHtml .= '<br />End of print_r output for plugin object.';
		//	$debugHtml .= '<br />----------------------------------------<br />';

		return $debugHtml;
	}
	//
	// ------------------------------------------------------
	// Unused example methods - for possible future reference
	// ------------------------------------------------------
	// For more info. see joomla/plugins/content/example.php
	//
	function onContentAfterTitle( $context, &$article, &$params, $limitstart )
	{
		return '';
	}
	function onContentBeforeSave( $context, &$article, $isNew )
	{
		return true;
	}
	function onContentAfterSave( $context, &$article, $isNew )
	{
		return true;
	}
}
