<?php

require_once Kohana::find_file('tests','kohana/DatabaseTest');

class Kohana_Database_Query_Builder_SelectTest extends Kohana_DatabaseTest{

	public function provider_select(){
		return array(
			array(
					array('col1','col2','col3'),//select 
					array('foo','bar'),//from
					array(
						array('join_table1','on',array('join_table1.col1','=','bar.col1'),'on',array('join_table.col2','=','bar.col2')),
						array(array('join_table2','jt2'),'on',array('jt2.col2','=','bar.col2'))
					),//joins
					array(),//where
					array(),//group
					array(),//order
					'SELECT "col1", "col2", "col3" FROM "foo" AS "bar" JOIN "join_table1" ON "join_table1"."col1" = "bar"."col1" AND "join_table"."col2" = "bar"."col2" JOIN "join_table2" AS "jt2" ON "jt2"."col2" = "bar"."col2"'

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
	public function test_select(array $select,array $from,array $joins, array $where,array $group, array $order, $expected){
		$db = $this->getMockDatabase();
		//create an instance of Database_Query_Builder_Select, dupping the compile method with null funcitonality, passing the select arguments
		$query = DB::select()->select_array($select);//$this->getMock('Database_Query_Builder_Select',,array($select));
		$this->assertAttributeSame($select, '_select', $query);//assert the select array was applied to the object

		$query->from($from);
		$this->assertAttributeSame(array($from), '_from', $query);//assert the from array was applied to the object

		$this->_apply_joins($query,$joins);
		//$this->assertAttributeSame($joins,'_join',$query);
		

		$sql = $query->compile($db);

		$this->assertEquals($expected,$sql);

	}

	private function _apply_joins($query,$joins){
		foreach($joins as $join){ 
			$table = $join[0];
			$query->join($table);

			$query_reflect = new ReflectionClass($query);
			for($i = 1;$i<count($join);$i++)
			{
				$func = null;
				$args = array();
				if(is_string($join[$i]) && $query_reflect->hasMethod($join[$i])){
					$func = $join[$i];
					$i++;
					$args = $join[$i];
					$query_reflect->getMethod($func)->invokeArgs($query,$args);
				}
				

			}

		}

	}
	
	private function _apply_where($query,$where){




	}



}



?>
