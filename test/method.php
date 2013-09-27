<?php

require("Util.php");

Util::log( "test" );

class test {
	public function foo()
	{
		Util::log( __METHOD__ );
	}

	public static function bar()
	{
		Util::log( __METHOD__ );
	}
}

test::bar();

$test = new test;
$test->foo();

