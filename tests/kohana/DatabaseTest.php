<?php

class Kohana_DatabaseTest extends Unittest_TestCase{ 

	public function getMockDatabase($database = 'default', array $config = array()){ 
		//TODO: Database::instance needs to be dupped to run a the mock database instance

		return $this->getMockForAbstractClass('Database', array($database,$config), '',false,true,true);

	}



}


?>
