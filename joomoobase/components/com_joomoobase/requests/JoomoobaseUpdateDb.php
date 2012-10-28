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

/**
 * Base class for ajax clients to use to delete data from the database
 * This class runs outside of the joomla! framework
 */
class JoomoobaseUpdateDb
{
	/**
	 * id of row in database to delete
	 * @access protected
	 * @var int
	 */
	protected $_id = 0;
	/**
	 * JConfig object containing our configuration options
	 * @access protected
	 */
	protected $_config;
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
	/**
	 * Current row in table (or null)
	 * @access protected
	 */
	protected $_row = null;
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
		//	print "Hello from JoomoobaseUpdateDb::__construct()<br />\n";

		/*
		 * Read configuration information and establish connection to DB
		 */
		JFactory::getConfig( "../../../configuration.php" );
		$this->_config = new JConfig();
		$host = $this->_config->host;
		$user = $this->_config->user;
		$password = $this->_config->password;
		$db = $this->_config->db;
		$dbprefix = $this->_config->dbprefix;

		//	print "host = " . $host . "<br />\n";
		//	print "user = " . $user . "<br />\n";
		//	print "password = " . $password . "<br />\n";
		//	print "db = " . $db . "<br />\n";
		//	print "dbprefix = " . $dbprefix . "<br />\n";

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
		//	print "Hello from JoomoobaseUpdateDb::_getDb();<br />\n";

		return $this->_db;
	}
}
?>
