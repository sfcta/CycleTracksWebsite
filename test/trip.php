<?php

require_once("TripFactory.php");
require_once("CoordFactory.php");

define( 'DATE_FORMAT', 'Y-m-d h:i:s' );
define( 'N_COORD',     100 );

$trip_id = 1;
$user_id = 1;
$purpose = 'work';

$time = time();

$coords = array();

for ( $i = 0; $i < N_COORD; $i++ )
{
	$coords[] = array(
		'recorded'  => date( DATE_FORMAT, $time++ ),
		'latitude'  => (   37.7733 + 0.0001 * $i ),
		'longitude' => ( -122.4178 + 0.0001 * $i ),
		/*
		'altitude'  => 0,
		'speed'     => 0,
		'hAccuracy' => 0,
		'vAccuracy' => 0,
		*/
	);
}

if ( $trip = TripFactory::getTrip( $trip_id ) )
{
	print_r( $trip );

	if ( $coords = CoordFactory::getCoordsByTrip( $trip_id ) )
		print_r( $coords );
}
elseif ( $trip_id = TripFactory::insert( $user_id, $purpose ) )
{
	foreach ( $coords as $coord )
	{
		$coord = (object) $coord;
		CoordFactory::insert(   $trip_id, 
								$coord->recorded, 
								$coord->latitude, 
								$coord->longitude );
								/* , 
								$coord->altitude, 
								$coord->speed, 
								$coord->hAccuracy, 
								$coord->vAccuracy );
								*/
	}

}

