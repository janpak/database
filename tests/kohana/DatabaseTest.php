<?php

class Kohana_DatabaseTest extends Unittest_TestCase{ 

	public function getMockDatabase($database = 'default', array $config = array()){ 
		//TODO: Database::instance needs to be dupped to run a the mock database instance

		$db =  $this->getMockForAbstractClass('DummyDatabase', array($database,$config), '',false,true,true);

		return $db;

	}

	


}

abstract class DummyDatabase extends Database{

	public function escape($value)
	{
		if(is_int($value)){
			return $value;

		}
		else{
			// SQL standard is to use single-quotes for all values
			return "'$value'";
		}
	}

}


?>
