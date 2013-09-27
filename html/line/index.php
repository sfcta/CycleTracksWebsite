<?php

require_once("TripFactory.php");
require_once("CoordFactory.php");


$trip_id = isset( $_GET['trip_id'] ) ? $_GET['trip_id'] : null; 
Util::log( $trip_id );


if ( $trip = TripFactory::getTrip( $trip_id ) )
{
	// output trip info
	//print_r( $trip );
?>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>SFCTA Bike Model Trip Viewer v0.1</title>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  function initialize() {
    var myLatlng = new google.maps.LatLng( 37.7733, -122.4178 );
<?php
	if ($coords = CoordFactory::getCoordsByTrip( $trip_id))
		echo "
		myLatlng = new google.maps.LatLng( {$coords[0]->latitude}, {$coords[0]->longitude} );
";
?>
    var myOptions = {
      zoom: 11,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var myPoints = new Array();

<?php
	// add a marker for each coordinate
	if ( $coords = CoordFactory::getCoordsByTrip( $trip_id ) )
		foreach ( $coords as $coord )
			echo "
				myLatlng = new google.maps.LatLng( {$coord->latitude}, {$coord->longitude} );
				myPoints.push(myLatlng);
";

	echo "
	finishPoint = new google.maps.Marker({
		clickable: true,
		position: myLatlng,
		title: 'Finish: {$coord->recorded}',
		map: map
	});
	
	myLatlng = new google.maps.LatLng( {$coords[0]->latitude}, {$coords[0]->longitude} );
	startPoint = new google.maps.Marker({
		clickable: true,
		position: myLatlng,
		title: 'Start: {$coords[0]->recorded}',
		map: map
	});
";

?>
    var myLine = new google.maps.Polyline({
		clickable: true,
		map: map,
		path: myPoints,
		strokeColor: "#80c",
		strokeOpacity: 0.7,
		strokeWeight: 5
    });


  }
</script>
</head>
<body style="margin:0px; padding:0px;" onload="initialize()">
  <div id="map_canvas" style="width: 100%; height: 100%;"></div>
</body>
</html>
<?php

}
else
	Util::log( "WARNING failed to find trip {$trip_id}" );

