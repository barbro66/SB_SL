<?php

define('api_key', $_GET['key']);
define('station',$_GET['station']);
define('site_key', $_GET['sitekey']);
define('not', $_GET['not']);

if(isset($_GET['dist'])):
$distance = $_GET['dist'];
else:
    $distance = 0;
endif;

$api_key = api_key;
$site_key = site_key;
$station = station;
$not = not;


require 'coreylib.php';
if($distance >= 30){
	$api = new clApi('https://api.sl.se/api2/realtimedepartures.xml?key='.$api_key.'&siteid='.$station.'&timewindow=60', false);
    //$api_buses = new clApi('https://api.trafiklab.se/sl/realtid/GetDpsDepartures.xml?&siteId='.station.'&key='.$api_key.'&timeWindow=60', false);
} else {
	$api = new clApi('https://api.sl.se/api2/realtimedepartures.xml?key='.$api_key.'&siteid='.$station, false);
    //$api_buses = new clApi('https://api.trafiklab.se/sl/realtid/GetDpsDepartures.xml?&siteId='.station.'&key='.$api_key, false);
}

//$slbuses = $api_buses->parse();

//$api_metros = new clApi('https://api.trafiklab.se/sl/realtid/GetDepartures.xml?siteId='.station.'&key='.$api_key, false);
//$slmetros = $api_metros->parse();

$sl1 = $api->parse();
$sl = $sl1->get('ResponseData');


if ($sl) {

//$api_station = new clApi('https://api.trafiklab.se/sl/realtid/GetSite.xml?stationSearch='.station.'&key='.$api_key);
$api_station = new clApi('https://api.sl.se/api2/typeahead.xml?searchstring='.$station.'&maxresults=1&key='.$site_key);

$slstation1 = $api_station->parse();
$slstation = $slstation1->get('ResponseData');

$slbusesmetros = array();

foreach($sl->get('Bus') as $bus):

  $diff = strtotime($bus->get('ExpectedDateTime'))-mktime(date("s"), date("i"), date("h"), date("m")  , date("d"), date("Y"));
	$years   = floor($diff / (365*60*60*24)); 
	$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
	$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

	$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24)/ (60*60)); 

	$minutes  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24 - $hours*60*60)/ 60); 

	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24 - $hours*60*60 - $minutes*60));
	if (intval($minutes)<15 && !(strpos($not,"bus") !== false)) {
    array_push($slbusesmetros, array("transport" => $bus->get('TransportMode'),
                                     "name" => $bus->get('Destination'),
                                     "line" => $bus->get('LineNumber'),
                                     //"departure" => (date("i", strtotime($bus->get('ExpectedDateTime'))) - date("i"))-1
                                     "departure" => $minutes
                                    ));
}
endforeach;
//print(($sl->get('Metros xmlns="http://sl.se/Departures.xsd"'));

foreach ($sl->get('Metro') as $metro):
    
    $testname=$metro->get('Destination');

    $diff = strtotime($metro->get('DisplayTime'))-mktime(date("s"), date("i"), date("h"), date("m")  , date("d"), date("Y"));
	$years   = floor($diff / (365*60*60*24)); 
	$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
	$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

	$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24)/ (60*60)); 

	$minutes  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24 - $hours*60*60)/ 60); 

	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24 - $hours*60*60 - $minutes*60));
//	print($not);
//	print($metro->get('Destination'));
//	if ((strpos("Akalla",($metro->get('Destination')))!==false)) {print 'found';}
	
//	print(strpos($not,$metro->get('Destination')));
	if (intval($minutes)<25 && !(strpos($not,"metro") !== false)  ) {
    array_push($slbusesmetros, array("transport" => $metro->get('TransportMode'),
                                     "name" => $metro->get('Destination'),
                                     "line" => $metro->get('GroupOfLine'),
                                     //"departure" => (date("i", strtotime($bus->get('ExpectedDateTime'))) - date("i"))-1
                                     "departure" => $minutes
                                    ));
    }
endforeach;

foreach ($sl->get('Tram') as $tram):
	$diff = strtotime($tram->get('ExpectedDateTime'))-mktime(date("s"), date("i"), date("h"), date("m")  , date("d"), date("Y"));
	$years   = floor($diff / (365*60*60*24)); 
	$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
	$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

	$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24)/ (60*60)); 

	$minutes  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24 - $hours*60*60)/ 60); 

	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24 - $hours*60*60 - $minutes*60));
	
    array_push($slbusesmetros, array("transport" => $tram->get('TransportMode'),
                                     "name" => $tram->get('Destination'),
                                     "line" => $tram->get('GroupOfLine'),
                                     //"departure" => (date("i", strtotime($bus->get('ExpectedDateTime'))) - date("i"))-1
                                     "departure" => $minutes
                                    ));
endforeach;

foreach ($sl->get('Train') as $train):
	$diff = strtotime($train->get('ExpectedDateTime'))-mktime(date("s"), date("i"), date("h"), date("m")  , date("d"), date("Y"));
	$years   = floor($diff / (365*60*60*24)); 
	$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
	$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

	$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24)/ (60*60)); 

	$minutes  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24 - $hours*60*60)/ 60); 

	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - 	$days*60*60*24 - $hours*60*60 - $minutes*60));
	if (intval($minutes)<25 && !(strpos($not,"train") !== false) )  {
    array_push($slbusesmetros, array("transport" => $train->get('TransportMode'),
                                     "name" => $train->get('Destination'),
                                     "line" => $train->get('GroupOfLine'),
                                     //"departure" => (date("i", strtotime($bus->get('ExpectedDateTime'))) - date("i"))-1
                                     "departure" => $minutes
                                    ));
	}
endforeach;


function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

aasort($slbusesmetros,"departure");

?>
<table id="SLRealtime" data-refresh-every-n-seconds=30>
    <tr>
        <th style="width:3%"></th>
        <th style="width:75px;text-align:center"><img src="http://images2.wikia.nocookie.net/__cb20100824161519/logopedia/images/c/ca/SL_logo.svg" height="30px" /></th>
        <th style="padding-left:20"><?php echo $slstation[0]->get('Name').' om '.$distance.' min'; ?></th>
        <th style="width:12%;text-align:center">min.</th>
    </tr>
<?php foreach($slbusesmetros as $dps): 
	//if ($dps['transport'] == 'METRO'){
	//	$distance = 0;
	//}
?>
<?php if ($dps['departure'] >= $distance){ ?>
    <tr>
        <?php switch ($dps['transport']){
        		case 'BLUEBUS': { ?>
        			<td style="background-color:blue"></td>
        			<td class="projectLine" style="color:lightGray"><?php echo $dps['line']; ?></td>
        <?php	} break;
        		case 'BUS': { ?> 
        			<td style="background-color:red"></td>
        			<td class="projectLine" style="color:lightGray"><?php echo $dps['line']; ?></td>
        <?php	} break;
        		case 'METRO': {
        			switch ($dps['line']){
	        			case 'Tunnelbanans gröna linje': { ?>
	        				<td style="background-color:green"></td>
                                                <td class="projectLine" style="color:lightGray"><img src="http://www.carlfranzon.com/wp-content/uploads/T-gron.png" height="40px" /></td>
	     <?php			} break;   				
	     				case 'Tunnelbanans röda linje': { ?>
	     					<td style="background-color:red"></td>
                                                <td class="projectLine" style="color:lightGray"><img src="http://www.carlfranzon.com/wp-content/uploads/T-rod.png" height="40px" /></td>
	     <?php			} break;
	     				case 'Tunnelbanans blå linje': { ?>
	     					<td style="background-color:blue"></td>
                                                <td class="projectLine" style="color:lightGray"><img src="http://www.carlfranzon.com/wp-content/uploads/T-bla.png" height="40px" /></td>
	     <?php			} break;
	     			}
                	} break;
	     		case 'TRAM': { ?>
	     			<td style="background-color:mediumGray"></td>
	     <?php 		switch ($dps['line']){
	     				case 'Spårväg City': { ?>
	     					<td class="projectLine" style="color:lightGray"><img src="http://www.carlfranzon.com/wp-content/uploads/S.png" height="40px" /></td>
	     <?php			} break;
	     				default: { ?>
	     					<td class="projectLine" style="color:lightGray"><img src="http://www.carlfranzon.com/wp-content/uploads/L.png" height="40px" /></td>
	     <?php			}
	     			}
	     		} break;
                        case 'TRAIN': { ?>
                                <td style="background-color:mediumGray"></td>
                                <td class="projectLine" style="color:lightGray"><img src="http://www.carlfranzon.com/wp-content/uploads/J.png" height="40px" /></td>
             <?php      }
	     		} ?>
	     
        
        <td class="projectDestination"><?php echo $dps['name']; ?></td>
        <td class="projectTime" style="text-align:center"><?php echo $dps['departure']; ?></td>
    </tr>
    <?php } //endif ?>
  <?php endforeach ?>
  


</table>
<?php

} else {
  // something went wrong
  echo 'Error';
}
?>
