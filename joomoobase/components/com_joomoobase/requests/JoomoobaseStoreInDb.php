<?php
/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoobase
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php .
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//
// Note: must link ../joomla/configuration.php ../joomla/libraries/ and ../joomla/includes/
//       to customizations
//

require_once "../../../configuration.php";
require_once "../../../libraries/loader.php";
require_once "../../../libraries/joomla/base/object.php";
require_once "../../../libraries/joomla/factory.php";
require_once "../../../libraries/joomla/database/database.php";
require_once "../../../libraries/joomla/database/database/mysql.php";
require_once "../../../libraries/joomla/database/table.php";
require_once "../../../libraries/joomla/error/error.php";
require_once "../../../libraries/joomla/environment/request.php";
require_once "../../../libraries/joomla/filter/filterinput.php";
require_once "../../../libraries/joomla/methods.php";
require_once "../../../libraries/joomla/user/user.php";

// - //	---------------------------------------------------------------
// - //	Lines commented out like this were moved to the subclass
// - //	  or replaced with subclass-specific code
// - //	Just sayin' - for when we refactor this back into jommoogallery
// - //	---------------------------------------------------------------
// - //	require_once ( '../classes/TpColors.php' );
// - //	require_once ( '../classes/TpLimits.php' );
// - //	require_once ( '../tables/templateparametersparams.php' );
// - //	$saveParms = new JoomoobaseStoreInDb();
// - //	$saveParms->storeInDatabase();

/**
 * Base class for ajax clients to use to save data in the database
 * This class runs outside of the joomla! framework
 */
class JoomoobaseStoreInDb
{
	/**
	 * id of row in database
	 * @access protected
	 * @var int
	 */
	protected $_id = 0;
	/**
	 * row just saved in database by storeInDatabase()
	 * @access protected
	 * @var int
	 */
	protected $_row = null;
	/**
	 * array of configuration options used to set up DB connection
	 * @access protected
	 */
	protected $_options;
	/**
	 * database table name
	 * @access protected
	 */
	protected $_tableName;
	/**
	 * JDatabase object used to connect with DB
	 * @access protected
	 */
	protected $_db;
	// - //	/**
	// - //	 * stdClass object with data to store
	// - //	 * @access protected
	// - //	 */
	// - //	protected $_params;
	/**
	 * array object with data to store
	 * @access protected
	 */
	protected $_data;
	/**
	 * success or error message returned to requestor
	 * @access protected
	 */
	protected $_message;

	/**
	 * constructor
	 * @access public
	 */
	public function __construct()
	{
		// - //	print "Hello from JoomoobaseStoreInDb::__construct()<br />\n";

		// - //	$id = htmlspecialchars($_POST['id']);
		// - //	if ( is_numeric($id) )
		// - //	{
		// - //		$this->_id = $id;
		// - //	}

		/*
		 * Read configuration information and establish connection to DB
		 */
		JFactory::getConfig( "../../../configuration.php" );
		$config = new JConfig();
		$host = $config->host;
		$user = $config->user;
		$password = $config->password;
		$db = $config->db;
		$dbprefix = $config->dbprefix;

		// - //	print "host = " . $host . "<br />\n";
		// - //	print "user = " . $user . "<br />\n";
		// - //	print "password = " . $password . "<br />\n";
		// - //	print "db = " . $db . "<br />\n";
		// - //	print "dbprefix = " . $dbprefix . "<br />\n";

		$this->_options = array (
			'host'     => $host,
			'user'     => $user,
			'password' => $password,
			'database' => $db,
			'prefix'   => $dbprefix,
		);
//		$this->_db =& new JDatabaseMySQL( $this->_options );
		$this->_db = JFactory::getDbo();
	}
	/**
	 * returns database object instantiated in constructor
	 * @access protected
	 * @return db JDatabaseMySQL object
	 */
	protected function &_getDb()
	{
		// - //	print "Hello from JoomoobaseStoreInDb::_getDb();<br />\n";

		return $this->_db;
	}

	/**
	 * driver function to save parameters
	 * @access public
	 * @return void
	 */
	public function storeInDatabase( )
	{
		//	print "Hello from JoomoobaseStoreInDb::storeInDatabase()<br />\n";
		//	$this->_printAllPostVariables();

		$this->_setData();
		$storedOk = $this->_storeData();

		// - //	if ( $storedOk )
		// - //	{
		// - //		print $this->_message . ' saved OK.<br />';
		// - //	}
		// - //	else
		// - //	{
		// - //		$this->_message = $this->_db->getError();
		// - //		print "Error storing data: " . $this->_message . "<br />\n";
		// - //	}

		return $storedOk;
	}
	// - //	/**
	// - //	 * set data (stdClass object) to values set in POST variables
	// - //	 * @access private
	// - //	 * @return void
	// - //	 */
	// - //	private function _setData( )
	// - //	{
	// - //		// print "Hello from JoomoobaseStoreInDb::_setData()<br />\n";
	// - //		$this->_params = new stdClass();
	// - //		$this->_params->id = $this->_id;
	// - //
	// - //		$this->_message = 'Value for ';
	// - //
	// - //		$background = htmlspecialchars($_POST['background']);
	// - //		if ( 0 < strlen($background) )
	// - //		{
	// - //			$this->_params->background = $background;
	// - //			$this->_message .= 'background (' . $background . ') ';
	// - //		}
	// - //
	// - //		$border_color_name = htmlspecialchars($_POST['border_color_name']);
	// - //		if ( 0 < strlen($border_color_name) )
	// - //		{
	// - //			$this->_params->border_color_name = $border_color_name;
	// - //			$this->_message .= 'border color (' . $border_color_name . ') ';
	// - //		}
	// - //
	// - //		$border_style = htmlspecialchars($_POST['border_style']);
	// - //		if ( 0 < strlen($border_style) )
	// - //		{
	// - //			$this->_params->border_style = $border_style;
	// - //			$this->_message .= 'border style (' . $border_style . ') ';
	// - //		}
	// - //
	// - //		if ( 0 < strlen($_POST['border_width']) )
	// - //		{
	// - //			$border_width = (int) htmlspecialchars($_POST['border_width']);
	// - //			if ( TpLimits::$minimum_border_width <= $border_width &&
	// - //			     $border_width <= TpLimits::$maximum_border_width   )
	// - //			{
	// - //				$this->_params->border_width = $border_width;
	// - //				$this->_message .= 'border width (' . $border_width . ') ';
	// - //			}
	// - //		}
	// - //
	// - //		if ( 0 < strlen($_POST['font_size']) )
	// - //		{
	// - //			$font_size = (int) htmlspecialchars($_POST['font_size']);
	// - //			if ( TpLimits::$minimum_font_size <= $font_size &&
	// - //			     $font_size <= TpLimits::$maximum_font_size   )
	// - //			{
	// - //				$this->_params->font_size = $font_size;
	// - //				$this->_message .= 'font size (' . $font_size . ') ';
	// - //			}
	// - //		}
	// - //
	// - //		// print "JoomoobaseStoreInDb::_setData(): storing data for this->_id   = \"" . $this->_id . "\"<br />";
	// - //		// print "this->_params->id = '" . $this->_params->id . "'<br />\n";
	// - //		// print "this->_params->background = '" . $this->_params->background . "'<br />\n";
	// - //		// print "this->_params->border_color_name = '" . $this->_params->border_color_name . "'<br />\n";
	// - //		// print "this->_params->border_style = '" . $this->_params->border_style . "'<br />\n";
	// - //		// print "this->_params->border_width = '" . $this->_params->border_width . "'<br />\n";
	// - //		// print "this->_params->font_size = '" . $this->_params->font_size . "'<br />\n";
	// - //	}
	// - //	/**
	// - //	 * store new value(s) for template parameter(s)
	// - //	 * @access private
	// - //	 * @return True if successful, else False
	// - //	 */
	// - //	private function _storeData( )
	// - //	{
	// - //		// - //	print "Hello from JoomoobaseStoreInDb::_storeData()<br />\n";
	// - //
	// - //		$db =& $this->_getDb();
	// - //		$table = new TableTemplateparametersParams( $db );
	// - //
	// - //		// $tableClass = get_class( $table );
	// - //		// print "In JoomoobaseStoreInDb::_storeData(): tableClass = \"$tableClass\"<br />\n";
	// - //
	// - //		if ( ! $table->bind($this->_params) )
	// - //		{
	// - //			print "JoomoobaseStoreInDb::_storeData - bind error: " . $table->getError() . "<br />\n";
	// - //			$db->setError( $table->getError() );
	// - //			return FALSE;
	// - //		}
	// - //		// else
	// - //		// {
	// - //		// 	print "JoomoobaseStoreInDb::_storeData(): table->bind() ran OK.<br />\n";
	// - //		// }
	// - //
	// - //		if ( ! $table->check() )
	// - //		{
	// - //			print "JoomoobaseStoreInDb::_storeData - check error: " . $table->getError() . "<br />\n";
	// - //			$db->setError( $table->getError() );
	// - //			return FALSE;
	// - //		}
	// - //		// else
	// - //		// {
	// - //		// 	print "JoomoobaseStoreInDb::_storeData(): table->check() ran OK.<br />\n";
	// - //		// }
	// - //
	// - //		if ( ! $table->store() )
	// - //		{
	// - //			print "JoomoobaseStoreInDb::_storeData - store error: " . $table->getError() . "<br />\n";
	// - //			$db->setError( $table->getError() );
	// - //			return FALSE;
	// - //		}
	// - //		// else
	// - //		// {
	// - //		// 	print "JoomoobaseStoreInDb::_storeData(): table->store() ran OK.<br />\n";
	// - //		// }
	// - //
	// - //		// $this->_row =& $table;
	// - //
	// - //		return TRUE;
	// - //	}

	/**
	 * print all post variables (useful when debugging)
	 * @access protected
	 * @return void
	 */
	protected function _printAllPostVariables( )
	{
		print "<br />\n";
		print "<h3>Hello from JoomoobaseStoreInDb::_printAllPostVariables()</h3>\n";
		print "<p style=\"margin-left: 16px;\">\n";

		foreach ( $_POST as $name => $value )
		{
		   print "post variables: _POST[$name] = $value<br />\n";
		}

		print " <br />\n";
		print " (End of list of post variables.)\n";
		print "</p>\n";
	}
}
?>
