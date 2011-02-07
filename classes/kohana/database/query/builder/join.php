<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database query builder for JOIN statements. See [Query Builder](/database/query/builder) for usage and examples.
 *
 * @package    Kohana/Database
 * @category   Query
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Database_Query_Builder_Join extends Database_Query_Builder {

	// Type of JOIN
	protected $_type;

	// JOIN ...
	protected $_table;

	// ON ...
	protected $_on = array();

	// USING ...
	protected $_using = array();

	/**
	 * Alias of and_join_open()
	 *
	 * @return  $this
	 */
	public function join_open()
	{
		return $this->and_join_open();
	}

	/**
	 * Opens a new "AND WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function and_join_open()
	{
		$this->_on[] = array('AND' => '(');

		return $this;
	}

	/**
	 * Opens a new "OR WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function or_join_open()
	{
		$this->_on[] = array('OR' => '(');

		return $this;
	}

	/**
	 * Closes an open "AND WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function join_close()
	{
		return $this->and_join_close();
	}

	/**
	 * Closes an open "AND WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function and_join_close()
	{
		$this->_on[] = array('AND' => ')');

		return $this;
	}

	/**
	 * Closes an open "OR WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function or_join_close()
	{
		$this->_on[] = array('OR' => ')');

		return $this;
	}

	/**
	 * Adds a new condition for joining.
	 *
	 * @param   mixed   column name or array($column, $alias) or object
	 * @param   string  logic operator
	 * @param   mixed   column name or array($column, $alias) or object
	 * @return  $this
	 */
	public function on($c1, $op, $c2,$conjunction = 'AND')
	{
		if ( ! empty($this->_using))
		{
			throw new Kohana_Exception('JOIN ... ON ... cannot be combined with JOIN ... USING ...');
		}

		$this->_on[] = array($conjunction => array($c1, $op, $c2));

		return $this;
	}

	/**
	 * Adds a new condition for joining.
	 *
	 * @param   string  column name
	 * @param   ...
	 * @return  $this
	 */
	public function using($columns)
	{
		if ( ! empty($this->_on))
		{
			throw new Kohana_Exception('JOIN ... ON ... cannot be combined with JOIN ... USING ...');
		}

		$columns = func_get_args();

		$this->_using = array_merge($this->_using, $columns);

		return $this;
	}

	/**
	 * Compile the SQL partial for a JOIN statement and return it.
	 *
	 * @param   object  Database instance
	 * @return  string
	 */
	public function compile(Database $db)
	{
		if ($this->_type)
		{
			$sql = strtoupper($this->_type).' JOIN';
		}
		else
		{
			$sql = 'JOIN';
		}
		if(is_array($this->_table) && ($this->_table[0] instanceof Kohana_Database_Query)){
			// Quote the table name that is being joined
			$sql .= ' ( '.$this->_table[0].' ) AS `'.$this->_table[1].'` ON ';
		}
		else{
			// Quote the table name that is being joined
			$sql .= ' '.$db->quote_table($this->_table).' ON ';
		}
		
		$sql .= parent::_compile_conditions($db, $this->_on);
	
		return $sql;
	}

	/**
	 * Creates a new JOIN statement for a table. Optionally, the type of JOIN
	 * can be specified as the second parameter.
	 *
	 * @param   mixed   column name or array($column, $alias) or object
	 * @param   string  type of JOIN: INNER, RIGHT, LEFT, etc
	 * @return  void
	 */
	public function __construct($table, $type = NULL)
	{
		// Set the table to JOIN on
		$this->_table = $table;

		if ($type !== NULL)
		{
			// Set the JOIN type
			$this->_type = (string) $type;
		}
	}
	

	public function reset()
	{
		$this->_type =
		$this->_table = NULL;

		$this->_on = array();
	}

} // End Database_Query_Builder_Join
