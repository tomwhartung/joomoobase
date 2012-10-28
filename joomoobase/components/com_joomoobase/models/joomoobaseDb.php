<?php
/**
 * @version     $Id: joomoobase.php,v 1.11 2008/10/31 06:15:48 tomh Exp tomh $
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  Joomoobase
 * @copyright   Copyright (C) 2008 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.modellist' );

/**
 *  Base class for models in the Joomoo system
 */
class JoomoobaseModelJoomoobaseDb extends JModelList
{
	/**
	 * name of table that this component uses
	 * @access protected
	 * @var string
	 */
	protected $_tableName = '';
	/**
	 * pagination object
	 * @access protected
	 * @var integer
	 */
	protected $_pagination;

	/**
	 * ID of current row in table (or 0)
	 * @access protected
	 */
	protected $_id = 0;
	/**
	 * Current row in table (or null)
	 * @access protected
	 */
	protected $_row = null;

	/**
	 * sql query to get list of rows for default page
	 * @access protected
	 * @var string
	 */
	protected $_listQuery = '';
	/**
	 * data array
	 * @access protected
	 * @var array
	 */
	protected $_rows;
	/**
	 * number of rows of data in DB
	 * @access protected
	 * @var integer
	 */
	protected $_rowCount = null;

	/**
	 * lists used when outputing HTML for lists - of groups or images - in back end
	 * @access protected
	 * @var array
	 */
	protected $_lists = array();

	/**
	 * Overridden constructor
	 * @access public
	 */
	public function __construct( $config = array() )
	{
		//	print "Hello from JoomoobaseModelJoomoobase::__construct()<br />\n";
		//	global $mainframe;                  // replaced by $app in 1.7.3
		$app = JFactory::getApplication();      // formerly global $mainframe object
		$option = JRequest::getCmd('option');
		parent::__construct( $config );

		//
		// get the cid (might be an array) from the default request hash and set it in this object
		//
		$cid = JRequest::getVar('cid', false, 'DEFAULT', 'array' );

		if ( $cid )
		{
			$id = $cid[0];
		}

		if ( $id == 0 )      // $cid could be an array of 0s or even nothing at all
		{
			$id = JRequest::getInt( 'id', 0 );
		}

		$this->setId( $id );
		//
		// Get the pagination request variables
		// Use those values to set the state pagination variables
		// 2012/01/14: Updated to work with 1.7.3; used components/com_contact/models/category.php as an example
		//
		$limit = $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit') );
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState( 'list.limit', $limit );
		$this->setState( 'list.start', $limitstart );
	}

	/**
	 * Retrieves name of this component's table in DB
	 * @return string containing name of DB for this component
	 */
	public function getTableName()
	{
		// print "Hello from JoomoobaseModelJoomoobase::getTableName()<br />\n";
		// print "returning this->_tableName = \"$this->_tableName\" <br />\n";
		return $this->_tableName;
	}

	/**
	 * Sets ID
	 * @access public
	 * @return void
	 */
	public function setId( $id=0 )
	{
		$this->_id = $id;
	}
	/**
	 * Gets ID
	 * @access public
	 * @return integer ID of current record
	 */
	public function getId ()
	{
		return $this->_id;
	}

	/**
	 * sets and returns the query
	 * @access public
	 * @return string The query to be used to retrieve the rows from the database
	 */
	public function getListQuery()
	{
		//	print "Hello from JoomoobaseModelJoomoobase::getListQuery()<br />\n";

		if ( $this->_listQuery == '' )
		{
			$this->_listQuery = 'SELECT * ' .
			                    ' FROM ' . $this->_tableName .
			                    $this->_buildQueryWhere() .
			                    $this->_buildQueryOrderBy();
		}

		//	print "getListQuery: this->_listQuery: $this->_listQuery<br />\n";
		return $this->_listQuery;
	}
	/**
	 * dummy/placeholder used when filtering is irrelevant for a db model (ie. it has no description or title cols)
	 * @access protected
	 * @return: where clause for query (empty)
	 */
	protected function _buildQueryWhere()
	{
		return ' ';
	}
	/**
	 * builds order by clause for _listquery (implements ordering)
	 * @access protected
	 * @return: order by clause for query
	 */
	protected function _getOrderByClause( $orderByColumns, $default_filter_order='id' )
	{
		//	print "Hello from JoomoobaseModelJoomoobase::_getOrderByClause()<br />\n";
		//	print "JoomoobaseModelJoomoobase::_getOrderByClause: default_filter_order = $default_filter_order<br />\n";
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		//
		// get the order field and direction
		// validate them, if invalid, use default value
		//
		$filter_order     = $app->getUserStateFromRequest( $option.'filter_order', 'filter_order', 'id' );
		$filter_order_Dir = strtoupper( $app->getUserStateFromRequest($option.'filter_order_Dir', 'filter_order_Dir', 'ASC') );

		if ( !in_array($filter_order, $orderByColumns) )
		{
			$filter_order = $default_filter_order;
		}

		if ( $filter_order_Dir != 'ASC' &&
		     $filter_order_Dir != 'DESC' )
		{
			$filter_order_Dir = 'ASC';
		}

		$orderByClause = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;

		return $orderByClause;
	}

	/*
	 * gets constraints for where clause to filter for search or state ([un]published)
	 * @access protected
	 * @return array contains search constraits for where clause
	 */
	protected function _getSearchAndStateConstraints()
	{
		// print "Hello from JoomoobaseModelJoomoobase::_getSearchAndStateConstraints()<br />\n";
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$whereConstraint = array();
		$filter_search = $app->getUserStateFromRequest( $option.'filter_search', 'filter_search' );
		$filter_search = trim( $filter_search );

		if ( $filter_search )
		{
			$whereConstraint['search'] = ' LOWER(title) LIKE "%' . $filter_search . '%" OR' .
			                             ' LOWER(description) LIKE "%' . $filter_search . '%"';
		}

		$filter_state = $app->getUserStateFromRequest( $option.'filter_state', 'filter_state' );

		if ( $filter_state == 'P' )
		{
			$whereConstraint['state'] = ' published = 1';
		}
		elseif ( $filter_state == 'U' )
		{
			$whereConstraint['state'] = ' published = 0';
		}

		return $whereConstraint;
	}

	/**
	 * Get a pagination object - from page 225 of Mastering... book
	 * @access public
	 * @return JPagination object based on current state
	 */
	public function getPagination()
	{
		//	print "Hello from JoomoobaseModelJoomoobase::getPagination()<br />\n";

		if ( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');        // import the pagination library

			$total = $this->getRowCount();            // get the pagination values
			$limitstart = $this->getState( 'limitstart' );
			$limit      = $this->getState( 'limit' );

			$this->_pagination = new JPagination( $total, $limitstart, $limit );
		}

		return $this->_pagination;
	}

	/**
	 * create lists array containing ordering and filtering lists
	 * @return array lists to use when outputing HTML to display the list of rows
	 */
	public function getLists( )
	{
		//	print "Hello from JoomoobaseModelJoomoobase::getLists()<br />\n";

		$this->_setupOrdering( );
		$this->_setupSearchFiltering( );
		$this->_setupStateFiltering ( );
		return $this->_lists;
	}

	/**
	 * Gets row of data corresponding to ID
	 * @access public
	 * @return array containing row of data
	 */
	public function getRow ()
	{
		//	print "Hello from JoomoobaseModelJoomoobase::getRow()<br />\n";

		$row =& $this->getTable();
		$row->load( $this->_id );
		$this->_row = $row;

		return $this->_row;
	}
	/**
	 * Retrieves rows from table in DB per constraints in $this->_listQuery
	 * @access public
	 * @return array Array of objects containing the rows from the database
	 */
	public function getRows()
	{
		$tableName = $this->getTableName();
		//	print "Hello from JoomoobaseModelJoomoobase::getRows() where tableName = \"" . $tableName . "\"<br />\n";
		//
		// load the data if it doesn't already exist
		//
		if ( empty($this->_rows) )
		{
			$query = $this->getListQuery();
			$limitstart = $this->getState( 'limitstart' );
			$limit      = $this->getState( 'limit' );
			$this->_rows = $this->_getList( $query, $limitstart, $limit );
		}

		//	print "JoomoobaseModelJoomoobase::this->_rows: " . print_r($this->_rows,true) . "<br />";
		return $this->_rows;
	}
	/**
	 * Clears out existing rows and related variables so getRows will retrieve new rows
	 * We do not want to re-run the same query so we call this when we want to run more than one query
	 * @access public
	 * @return void
	 */
	public function clearRows()
	{
		$this->_rows = null;
		$this->_rowCount = null;
		$this->_listQuery = '';
	}
	/**
	 * get total number of rows returned by current _listQuery
	 * @access public
	 * @return integer number of rows obtained by current query
	 */
	public function getRowCount( )
	{
		//	print "Hello from JoomoobaseModelJoomoobase::getRowCount()<br />\n";

		if ( $this->_rowCount == null )
		{
			$query = $this->getListQuery();
			// print "getRowCount: query = \"$query\"<br />\n";
			$this->_rowCount = $this->_getListCount( $query );
		}

		return $this->_rowCount;
	}

	/**
	 * Method to store a record - copied from tutorial
	 * called by the new save() function in current controller
	 * @access public
	 * @return boolean True on success else false
	 */
	public function store( $data=null )
	{
		$this->_row =& $this->getTable();

		if ( $data == null )
		{
			$data = JRequest::get( 'post' );
		}

		if ( !$this->_row->bind($data) )            // Bind the form fields to the table
		{
			$this->setError($this->_row->getError());
			return false;
		}

		if ( !$this->_row->check() )                // Make sure the data is valid
		{
			$this->setError($this->_row->getError());
			return false;
		}

		if ( !$this->_row->store() )                // Store the table to the database
		{
			$this->setError($this->_row->getError());
			return false;
		}

		$this->_id = $this->_row->id;

		return true;
	}
	/**
	 * sets published column for current row
	 * @access public
	 * @return void
	 */
	public function setPublished( $published )
	{
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$row =& $this->getTable();
		$row->publish( $cid, $published );
	}

	/**
	 * Method to delete record(s) - copied from tutorial
	 * called by new remove() function in current controller
	 * @access public
	 * @return boolean True on success else false
	 */
	//	public function delete( )
	public function delete( $id=0 )
	{
		$row =& $this->getTable();

		//
		// 2010/01/21: Added checks for id pass-in and set as memeber variable to support comment deletion
		//
		if ( 0 < $id )
		{
			$cids = array ( $id );
		}
		else if ( 0 < $this->_id )
		{
			$cids = array ( $this->_id );
		}
		else
		{
			$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		}

		if ( 0 < count($cids) )
		{
			foreach ( $cids as $cid )
			{
				if ( !$row->delete($cid) )
				{
					$this->setError( $row->getError() );
					return false;
				}
			}
		}
		else
		{
			$this->setError( 'No row(s) specified for deletion!' );
			return false;
		}

		return true;
	}
	//
	// --------------------------------------
	// Private functions - used by getLists()
	// --------------------------------------
	//
	/**
	 * Set up ordering
	 * @return array contains filter_order and filter_order_Dir
	 */
	private function _setupOrdering( )
	{
		//	print "Hello from _setupOrdering<br />\n";
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		//
		// prepare list array, get the user state of the order and direction
		//
		$filter_order     = $app->getUserStateFromRequest( $option, 'filter_order', 'filter_order', 'id' );
		$filter_order_Dir = $app->getUserStateFromRequest( $option, 'filter_order_Dir', 'filter_order_Dir', 'ASC' );

		$this->_lists['order'] = $filter_order;
		$this->_lists['order_Dir'] = $filter_order_Dir;

		return $this->_lists;
	}
	/**
	 * Set up filtering on search criteria
	 * @return array includes search
	 */
	private function _setupSearchFiltering( )
	{
		//	print "Hello from _setupSearchFiltering<br />\n";
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		//
		// get the state (published or unpublished or '' (both)) and create grid.state drop-down
		//
		$filter_search = $app->getUserStateFromRequest( $option.'filter_search', 'filter_search' );
		$this->_lists['search'] = $filter_search;

		return $this->_lists;
	}
	/**
	 * Set up filtering on state (Published vs. Unpublished)
	 * @return array including filter_state
	 */
	private function _setupStateFiltering( )
	{
		//	print "Hello from _setupStateFiltering<br />\n";
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		//
		// get the state (published or unpublished or '' (both)) and create grid.state drop-down
		//
		$filter_state = $app->getUserStateFromRequest( $option.'filter_state', 'filter_state' );
		$this->_lists['state'] = JHTML::_( 'grid.state', $filter_state );

		return $this->_lists;
	}
}
?>