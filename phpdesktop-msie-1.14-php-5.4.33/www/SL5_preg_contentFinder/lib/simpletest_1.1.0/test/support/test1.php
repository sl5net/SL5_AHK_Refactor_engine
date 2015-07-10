<?php
include_once '../../unit_tester.php';
class test1 extends UnitTestCase {
	function test_pass(){
		$this->assertEqual(3,1+2, "pass1");
	}
}
?>
