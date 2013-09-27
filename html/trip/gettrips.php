<?php

require_once("TripFactory.php");
require_once("CoordFactory.php");

// arg processing
$where        = isset($_GET['where']) ? $_GET['where'] : '';
$type         = isset($_GET['type']) ? $_GET['type'] : 'map';
$count        = isset($_GET['count']) ? $_GET['count'] : 100;
$startidx     = isset($_GET['startidx']) ? $_GET['startidx'] : 0;
$lat_maxdist  = isset($_GET['lat_maxdist']) ? trim($_GET['lat_maxdist']) : 1.0;
$long_maxdist = isset($_GET['long_maxdist']) ? trim($_GET['long_maxdist']) : 1.0;
$submit       = isset($_GET['submit']) ? true : false;

if ($where == "austin") {
  $lat_center = 30.267074;
  $long_center = -97.742958;
}
elseif ($where == "pugetsound") {
  $lat_center = 47.820543;
  $long_center = -122.442647;
}
elseif ($where == "santaclara") {
  $lat_center = 37.369407;
  $long_center = -122.037506;
}
elseif ($where == 'monterey') {
  $lat_center=36.649691;
  $long_center=-121.567154;
}
elseif ($where=="sanmateo") {
  $lat_center=37.562992;
  $long_center = -122.325527;
}
elseif ($where=="fortcollins") {
  $lat_center=40.5586;
  $long_center=-104.992;
}
elseif ($where=="twincities") {
  $lat_center=44.98333;
  $long_center=-93.2666;
}
elseif ($where=="raleigh-nc") {
  $lat_center=35.772143;
  $long_center=-78.638763;
}
elseif ($where=="sanfrancisco" || $submit==false) {
  $lat_center = 37.7733;
  $long_center = -122.4178;
} else {
  $lat_center = trim($_GET['lat_center']);
  $long_center = trim($_GET['long_center']);
  error_log($_GET['long_center']);
}

$form = '<div id="gettrips_form"><form>
<div class="inputdiv">
<label>Where:</label>
 <input type="radio" name="where" value="santaclara" ' . ($where=='santaclara' ? 'CHECKED' : '') . ' />Santa Clara
 <input type="radio" name="where" value="sanfrancisco" ' . ($where=='sanfrancisco' ? 'CHECKED' : '') . ' />San Francisco
 <input type="radio" name="where" value="sanmateo" ' . ($where=='sanmateo' ? 'CHECKED' : '') . ' />San Mateo
 <input type="radio" name="where" value="monterey" ' . ($where=='monterey' ? 'CHECKED' : '') . ' />Monterey
 <input type="radio" name="where" value="austin" ' . ($where=='austin' ? 'CHECKED' : '') . ' />Austin
 <input type="radio" name="where" value="pugetsound" ' . ($where=='pugetsound' ? 'CHECKED' : '') . ' />Puget Sound
 <br /><label>&nbsp;</label>
 <input type="radio" name="where" value="fortcollins" ' . ($where=='fortcollins' ? 'CHECKED' : '') . ' />Fort Collins
 <input type="radio" name="where" value="twincities" ' . ($where=='twincities' ? 'CHECKED' : '') . ' />Minneapolis-Saint Paul
 <input type="radio" name="where" value="raleigh-nc" ' . ($where=='raleigh-nc' ? 'CHECKED' : '') . ' />Raleigh, NC
 <br /><label>&nbsp;</label>
 <input type="radio" name="where" value="" ' . ($where=='' ? 'CHECKED' : '') .' />Or enter in latitude: <input type="text" name="lat_center" value="'.$lat_center.'" size="10" /> and longitude: <input type="text" name="long_center" value="'.$long_center.'" size="10" />
</div>
<div class="inputdiv"><label>Latitude Max Dist:</label><input type="text" name="lat_maxdist" value="'.$lat_maxdist.'" /></div>
<div class="inputdiv"><label>Longitude Max Dist:</label><input type="text" name="long_maxdist" value="'.$long_maxdist.'" /></div>
<div class="inputdiv">
<label>Type:</label>
 <input type="radio" name="type" value="map" ' . ($type=='map' ? 'CHECKED' : '') . ' />Map
 <input type="radio" name="type" value="table" ' . ($type=='table' ? 'CHECKED' : '') . ' />Table
 <input type="radio" name="type" value="csv" ' . ($type=='csv' ? 'CHECKED' : '') . ' />CSV (will trigger file download; recommended for large counts)
</div>
<div class="inputdiv"><label>Count:</label><input type="text" name="count" value="'.$count.'" /><br /></div>
<div class="inputdiv"><label>Start index:</label><input type="text" name="startidx" value="'.$startidx.'" /></div>
<div class="inputdiv"><input type="submit" name="submit" value="submit" /></div>
</form></div>';

Util::log("where=".$where.
  "; type=" . $type .
  "; lat_maxdist=" . $lat_maxdist .
  "; long_maxdist=" . $long_maxdist .
  "; startidx=" . $startidx . 
  "; count=".$count . 
  "; submit=" . $submit);
if ($submit) {
  $trips = TripFactory::getTripsByBoundingBox($lat_center, $lat_maxdist, $long_center, $long_maxdist);
} else { 
  $trips = Array(); 
}



// function to return the trip data for the given type
// - for maps, returns a list of (status, dataString)
// - otherwise, prints the result
function getTripData($trips, $startidx, $count, $type) {

  $maxidx = min(count($trips)-1,$startidx+$count);
  $status = "Found " . count($trips) . " trips.";
  if (count($trips)>0) { 
    $status .= " Showing " . $startidx . " - " . ($maxidx-1) . "."; 
  }
  $showtrips = array_slice($trips, $startidx, $count);

  if (count($showtrips)==0) {
    if ($type=="table" || $type=="csv") {
      print($status);
      return;
    } else {
      return array($status, "");
    }
  }

  if ($type=="table") { 
    $tablestart = "<table>\n";
    $init       = "<tr><td>";
    $hinit      = "<tr><th>";
    $delim      = "</td><td>";
    $hdelim     = "</th><th>";
    $rowend     = "</td></tr>\n";
    $hrowend    = "</th></tr>\n";
    $tableend   = "</table>\n";

    print($status . "User attributes table <a href='#user'>below</a>.<br /><br />\n" . $tablestart);
  } elseif($type=="csv") {
    $tablestart = "";
    $init       = "";
    $hinit      = $init;
    $delim      = ",";
    $hdelim     = $delim;
    $rowend     = "\n";
    $hrowend    = $rowend;
    $tableend   = "\n";

    print($status . "\nUser attributes table below.\n\n");
  }

  if ($type=="table" || $type=="csv") {
    // header row
    print($hinit . "trip_id" . $hdelim . "latitude" . $hdelim . "longitude" . 
          $hdelim . "altitude" . $hdelim . "hAccuracy" . $hdelim . "vAccuracy" . $hdelim . "speed" .
          $hdelim . "recorded" . $hrowend);
    $userstable = $tablestart . $hinit . "trip_id" . $hdelim . "user_id" . 
                  $hdelim . "age" . $hdelim . "gender" . 
                  $hdelim . "homeZIP" . $hdelim . "schoolZIP" . $hdelim . "workZIP" . 
                  $hdelim . "cycling_freq" . $hdelim . "purpose" . // $hdelim . "device" . i
                  $hrowend;
  }

  // TRIP LOOP ---------------------------------------------------------------------------------------
  $coords_result = CoordFactory::getCoordsByTrip($showtrips);
  $last_trip_id = -1;

  while ( $coord = $coords_result->fetch_object( 'Coord' ) ) {
    $trip_id = $coord->trip_id;

    // new trip
    if (($trip_id != $last_trip_id) &&  ($type=="map")) {
      $dataString .= "var myPoints".$trip_id." = new Array();\n";
    }

    if ($type=="table" || $type=="csv") {
      print($init . $coord->trip_id .
            $delim . $coord->latitude .
            $delim . $coord->longitude .
            $delim . $coord->altitude .
            $delim . $coord->hAccuracy .
            $delim . $coord->vAccuracy .
            $delim . $coord->speed .
            $delim . $coord->recorded .
            $rowend);
    } else {
      // map type - add a marker for each coordinate
      $dataString .= "myPoints" . $coord->trip_id . ".push(new google.maps.LatLng(" .
                     $coord->latitude . "," . $coord->longitude . "));\n";
    }
    
    // new trip
    if ($trip_id != $last_trip_id) {
      if ($type=="map") {
        $dataString .= "var myLine" . $coord->trip_id . " = new google.maps.Polyline({
          clickable: true,
          map: map,
          path: myPoints" . $coord->trip_id . ",
          strokeColor: '#'+Math.floor(Math.random()*16777215).toString(16),
          strokeOpacity: 0.7,
          strokeWeight: 3 });\n";
      }

      // users table only for table and csvs
      if ($type=="table" || $type=="csv") {
        $attrs = TripFactory::getTripAttrsByTrips($trip_id);
        $userstable .= $init . $attrs[0] .
                       $delim . $attrs[1] .
                       $delim . $attrs['age'] .
                       $delim . $attrs['gender'] .
                       $delim . $attrs['homezip'] .
                       $delim . $attrs['schoolzip'] .
                       $delim . $attrs['workzip'] .
                       $delim . $attrs['text'] .
                       $delim . $attrs['purpose'] .
                       // $delim . (substr($attrs['device'],0,7)=="android" ? "android" : "") .
                       $rowend;
      }
    }
    $last_trip_id = $trip_id;
    unset($coord);
  }
  $coords_result->close();
  
  // for maps, we're done
  if ($type=="map") {
    return array($status, $dataString);
  }

  print($tableend);
  if ($type=="table") {
    print("<br /><br />\n<a name='user'></a>");
  }
  print($userstable);
  print($tableend);
}
?>

<?php if ($type=="csv" && $submit) {
  // for CSV requests, no html.... let's just do this
  header('Content-type: text/csv');
  header('Content-Disposition: attachment; filename="bikedata' . ($where=='' ? '' : '_' . $where) . '.csv"');
  getTripData($trips, $startidx, $count, $type);
  return;
}?> 
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<?php if ($type=="map" && $submit) { ?>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<?php } ?>
<title>SFCTA Bike Model Trip Viewer v1.0</title>
<style type="text/css">
  body { font-family:"Lucida Console", Monaco, monospace; }
  div.inputdiv { padding:5px; }
  div#gettrips_form { border:1px solid gray; border-radius:10px; background-color:#efeeff; margin:10px;}
  form { margin:0px; padding:10px; }
  label { width: 200px; display:block; float:left; text-align:right; }
  table { font-size:10px; border-collapse:collapse; }
  th { color:#ffffff; background-color:#a7c942; font-size:12px; padding: 7px; }
  td { border: 1px solid #98bf21; padding: 3px 7px 2px 7px; }
  div#data_nm { font-size:10px;}
</style>

<?php if ($type=="map" && $submit) { ?>
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
  <script type="text/javascript">
  function initialize() {
    var myLatlng = new google.maps.LatLng(<?php print $lat_center;?>,<?php print $long_center;?>);
    var myOptions = {
      zoom: 11,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var bbPoints = new Array();
    bbPoints.push(new google.maps.LatLng(<?php print($lat_center-$lat_maxdist)?>, <?php print($long_center-$long_maxdist)?>));
    bbPoints.push(new google.maps.LatLng(<?php print($lat_center+$lat_maxdist)?>, <?php print($long_center-$long_maxdist)?>));
    bbPoints.push(new google.maps.LatLng(<?php print($lat_center+$lat_maxdist)?>, <?php print($long_center+$long_maxdist)?>));
    bbPoints.push(new google.maps.LatLng(<?php print($lat_center-$lat_maxdist)?>, <?php print($long_center+$long_maxdist)?>));
    bbPoints.push(new google.maps.LatLng(<?php print($lat_center-$lat_maxdist)?>, <?php print($long_center-$long_maxdist)?>));
    var bbLine = new google.maps.Polyline({clickable:true, map:map, path:bbPoints, strokeColor:'#666666', strokeOpacity:1.0, strokeWeight:5});

  <?php 
    list($status, $dataString) = getTripData($trips, $startidx, $count, $type);
    print $dataString;
  ?>
  }
  </script>
</head>
  <body style="margin:0px; padding:0px;" onload="initialize()">
  <?php print $form;?>
  <div id="data" style="display:block; width:100%;"><?php print $status;?>  The lat/long bounding box is shown in dark grey. <br />
  <br /></div>
  <div id="map_canvas" style="height:800px;"></div>
  <!-- <div id="map_click_info" style="height:100%">Click on a trip for more info.</div> -->
<?php } else { 
  print "<body>\n";
  print $form;
  getTripData($trips, $startidx, $count, $type);
  print "</body>\n";
} ?>
</html>
