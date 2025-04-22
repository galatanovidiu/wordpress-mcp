<?php

class Test_Mcp extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function testToolRegistration() {
		// Replace this with some actual testing code.
		print_r( WPMCP()->list_tools() );
		$this->assertTrue( true );
	}
}
