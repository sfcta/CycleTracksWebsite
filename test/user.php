<?php

require_once("UserFactory.php");

/*
$devices = array(
	'abc' => 'test@email.com',
	'123' => 'email@test.com',
	'xyz' => 'email@test.com',
	'foo' => 'foo@foo.com',
	'bar' => 'bar@bar.com',
);

foreach ( $devices as $device => $email )
{
	// try to lookup user by this device ID
	if ( $user = UserFactory::getUserByDevice( $device ) )
		print_r( $user );
	else
		UserFactory::insert( $device, $email );
}
*/

// get user record to update
if ( $old = UserFactory::getUser( 3 ) )
{
	// faux REST data to update
	$obj = new stdClass;

	/*
	$obj->email     = 'mattpaul@gmail.com';
	$obj->age       = '31';
	$obj->gender    = 'M';
	$obj->homeZIP   = '94116';
	$obj->schoolZIP = '90210';
	$obj->workZIP   = '12345';
	*/
	$obj->cycling_freq = 0;

	$new = new User( $obj );

	if ( UserFactory::update( $old, $new ) )
		echo "update successful\n";
	else
		echo "update failed\n";
}

