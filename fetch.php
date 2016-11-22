<?php
require 'helpers.php';
?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
</head>
<body>
<?php
mb_http_output('UTF-8');

require __DIR__ . '/vendor/autoload.php';
use Goutte\Client;
$client = new Client();

require 'db.php';

/* crawler */
if (PRODUCTION) {
    $url = "http://www.ilmateenistus.ee/ilm/ilmavaatlused/vaatlusandmed/tunniandmed/";
} else {
    $url = "http://kidplay-wingsuit.c9users.io/ilmateenistus.html";
}

/* prepwork */
if ($result = $mysqli->query("select id,stationName from weatherStations")) {
    while ($row = $result->fetch_assoc()) {
        $stationDbIds[$row['id']]=$row['stationName'];
    }
} else {
    logError("Error getting stationIDs:\n"+$mysqli->error);
};

try {
    $crawler = $client->request('GET', $url);
} catch (Exception $e) {
    logError('Caught exception: ',  $e->getMessage(), "\n");
}


    $stationData = array(
        "stationName" => "",
        "airTemp" => "",
        "humidity" => "",
        "airPressure" => "",
        "airPressureDelta" => "",
        "windDirection" => "",
        "windSpeed" => "",
        "windSpeedMax" => "",
        "cloudiness" => "",
        "currentWeatherSensor" => "",
        "currentWeatherObserver" => "",
        "precipitation" => "",
        "visibility" => "");
    $allStationsData=array();
    $stationValues=array();

try {    
    $crawler->filterXpath('//table/tbody/tr')->each(function ($node) {
        global $stationData;
        global $allStationsData;
        global $stationValues;
        global $todayDate;
        global $todayTime;
        global $i;
        
        
        $stationValues=array();

        $i=0;
        
        $node->filterXpath('//td')->each(function($td){
            global $i;
            global $stationDbIds;
            global $stationValues;
    
            $i++;
            $value = trim($td->text());
            
            if (($i==1) && ($index = array_search($value,$stationDbIds))) { // if first column (stationName) and stationName found in DB array
                $value = $index; // replace value with DB index
            }
            $stationValues[]=$value;    
        });
        
        $allStationsData[] = array_combine(array_keys($stationData),$stationValues);
    });
} catch (Exception $e) {
    logError('Caught exception: ',  $e->getMessage(), "\n");
}

$qry = "INSERT
            INTO
              `stationsData`(
                `id`,
                `dataTime`,
                `stationId`,
                `airTemp`,
                `humidity`,
                `airPressure`,
                `airPressureDelta`,
                `windDirection`,
                `windSpeed`,
                `windSpeedMax`,
                `cloudiness`,
                `currentWeatherSensorId`,
                `currentWeatherObserverId`,
                `rainfall`,
                `visibility`
              ) VALUES ";
  
    foreach ($allStationsData as $oneStation) {
        $partQry = "(0,NOW(),";
            foreach ($oneStation as $item) {
                $partQry .= "'".str_replace(',','.',$item)."',";
            }
        
        $partQry = rtrim($partQry, ",");        
        
        $qry .= $partQry . "),\n";
        }

    $qry = rtrim($qry, "\n");
    $qry = rtrim($qry, ",");

    //echo str_replace("\n","<br>", $qry);
    
    if (!$result = $mysqli->query($qry)) {
        logError("Executing query: ".$mysqli->error."<br>".$qry);
        exit;
    }

?>
</body>
</html>