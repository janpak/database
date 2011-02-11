<?php



class Kohana_Database_Query_Builder_SelectTest extends Unittest_TestCase{

	private $_config = array();

	public function setUp(){
		$this->_config =  array(
			'default' => array
			(
				'type'       => 'mysql',
				'connection' => array(
					/**
					 * The following options are available for MySQL:
					 *
					 * string   hostname
					 * integer  port
					 * string   socket
					 * string   username
					 * string   password
					 * boolean  persistent
					 * string   database
					 */
					'hostname'   => 'test',
					'username'   => 'test',
					'password'   => 'testpass',
					'persistent' => FALSE,
					'database'   => 'testdb',
				),
				'table_prefix' => '',
				'charset'      => 'utf8',
				'caching'      => FALSE,
				'profiling'    => TRUE,
			)
		);	

		parent::setUp();

	}


	public function getMockDatabase($database = 'default', array $config = array()){ 
		return $this->getMockForAbstractClass('Database', array($database,$config), '',false,true,true);

	}

	public function provider_select(){
		return array(
			array(
					array('col1','col2','col3'),//select 
					array('foo','bar'),//from
					array(),//joins
					array(),//where
					array(),//group
					array(),//order
					'SELECT `col1`, `col2`, `col3` FROM `foo` AS `bar`'

				)
			);
	}

	/**
	 * Tests Kohana_Database_Query_Builder_Select::test_select() 
	 *
	 * @test
	 * @dataProvider provider_select
	 * @param array  $cols  select columns  
	 * @param array  $table table to apply from 
	 */
	public function test_select(array $select,array $from,array $join,array $where,array $group, array $order, $expected){
		$db = $this->getMockDatabase();
		$query = $this->getMock('Database_Query_Builder_Select',array(),array($select));
		$query->from(array($from));
		$query->expects($this->once())
		         ->method('compile')
		         ->with($db)
		         ->will($this->returnValue($expected));

		$query->compile($db);

	}



}



?>
