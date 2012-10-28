<?php
/**
 * @version     $Id: joomoositestyleparams.php,v 1.1 2009/05/19 22:08:52 tomh Exp tomh $
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  JoomooSitestyle
 * @copyright   Copyright (C) 2008 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Joomoo controller base class
 * maybe not much right now but allows for expansion later...
 */
class JoomooBaseController extends JController
{
	/**
	 * name of the model class this controller uses
	 * @access protected
	 * @var string
	 */
	protected $_modelName = '';

	/**
	 * Constructor: call c'tor in parent class
	 */
	public function __construct( $default = array() )
	{
		parent::__construct( $default );

		// print( "in JoomooRatingController::__construct() calling print_r on default:<br />\n" );
		// print_r( $default );
		// print( "<br />\n" );
	}

	/**
	 * saftey net: called by framework when task is not handled by another method in this class or one of its children
	 * @access public
	 * @return void
	 */
	public function display()
	{
		// print "Hello from JoomooBaseController::display()<br />\n";

		$model =& $this->getModel( $this->getModelName() );           // instantiates model class

		$view->display();
	}

	/**
	 * Returns name of this controller's model
	 * @return string name of model for this component
	 */
	public function getModelName()
	{
		// print "Hello from JoomooBaseController::getModelName()<br />\n";
		// print "returning this->_modelName = \"$this->_modelName\" <br />\n";

		return $this->_modelName;
	}
}
?>
