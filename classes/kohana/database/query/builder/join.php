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

		// Quote the table name that is being joined
		$sql .= ' '.$db->quote_table($this->_table);
		
		if ( ! empty($this->_using))
		{
			// Quote and concat the columns
			$sql .= ' USING ('.implode(', ', array_map(array($db, 'quote_column'), $this->_using)).')';
		}
		else{
			$sql .= ' ON ' . $this->_compile_conditions($db, $this->_on);

		}
	
		return $sql;
	}

	/**
	 * Compiles an array of conditions into an SQL partial. Used for WHERE
	 * and HAVING.
	 *
	 * @param   object  Database instance
	 * @param   array   condition statements
	 * @return  string
	 */
	protected function _compile_conditions(Database $db, array $conditions)
	{
		$last_condition = NULL;

		$sql = '';
		foreach ($conditions as $group)
		{
			// Process groups of conditions
			foreach ($group as $logic => $condition)
			{
				if ($condition === '(')
				{
					if ( ! empty($sql) AND $last_condition !== '(')
					{
						// Include logic operator
						$sql .= ' '.$logic.' ';
					}

					$sql .= '(';
				}
				elseif ($condition === ')')
				{
					$sql .= ')';
				}
				else
				{
					if ( ! empty($sql) AND $last_condition !== '(')
					{
						// Add the logic operator
						$sql .= ' '.$logic.' ';
					}

					// Split the condition
					list($column, $op, $column2) = $condition;

					// Database operators are always uppercase
					$op = strtoupper($op);
					

					if ($column)
					{
						// Apply proper quoting to the column
						$column = $db->quote_column($column);
					}

					if(is_string($column2) ){
						// Apply proper quoting to the column
						$column2 = $db->quote_column($column2);
					}
					else if(is_array($column2)){
						$column2 = $db->quote($column2);

					}

					// Append the statement to the query
					$sql .= trim($column.' '.$op.' '.$column2);
				}

				$last_condition = $condition;
			}
		}
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
