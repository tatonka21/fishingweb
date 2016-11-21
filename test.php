<?php
require __DIR__ . '/vendor/autoload.php';
use Goutte\Client;
use MongoDB\Client as MyMongo;


$client = new Client();

$mondoDBuser = 'collection';
$mondoDBpassword = 'sd!v908%w2k2m2f8f#';
//$mongoDBurl = 'mongodb://'.$mondoDBuser.':'.$mondoDBpassword.'@ds013290.mlab.com:13290';


//$mongo = new MyMongo($mongoDBurl);*/

/*$mongoDBurl = 'mongodb://ds013290.mlab.com:13290';
$mongo = new MyMongo($mongoDBurl);
$mongo->User = $mondoDBuser;
$mongo->Password = $mondoDBpassword;*/

 $host='ds013290.mlab.com:13290';
 $userdb='ilmateenistus';
 $database=$userdb.".stationsData";
 
   
    try{
        $manager = new MongoDB\Driver\Manager("mongodb://{$host}/{$userdb}", array("username" => $mondoDBuser, "password" => $mondoDBpassword));
        if ($manager) {
            $bulk = new MongoDB\Driver\BulkWrite;
        }
    } catch(Exception $e){
        echo  "<center><h1>Doesn't work</h1></center>";
        print_r($e);
        exit;
    }       
    
date_default_timezone_set("Europe/Tallinn");

$todayTime = date('H:i');
$todayDate = date('d/m/Y');



$url = "http://www.ilmateenistus.ee/ilm/ilmavaatlused/vaatlusandmed/tunniandmed/";
$crawler = $client->request('GET', $url);

    $stationData = array(
        "dataDate" => "",
        "dataTime" => "",
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
    
$crawler->filterXpath('//table/tbody/tr')->each(function ($node) {
    global $stationData;
    global $allStationsData;
    global $stationValues;
    global $todayDate;
    global $todayTime;
    global $i;
    
    //echo "in tr loop<br>";
    $stationValues=array();
    $stationValues[]=$todayDate;
    $stationValues[]=$todayTime;
    
    $i=0;
    
    $node->filterXpath('//td')->each(function($td){
        global $i;
        $i++;
        global $stationValues;
        $stationValues[]=trim($td->text());    
    });
    
    $allStationsData[] = array_combine(array_keys($stationData),$stationValues);
});


    if ($bulk) {
        foreach ($allStationsData as $oneStation) {
            $bulk->insert($oneStation);
        }
        
        $manager->executeBulkWrite($database, $bulk);
    }

/*
$result = $stationsDataCollection->insertMany ($allStationsData );
echo "Inserted with Object ID '{$result->getInsertedId()}'";
*/

/*        $filter = ['stationName' == "Paldiski"];
        $options = [
            'projection' => ['_id' => 0],
            'sort' => ['x' => -1],
        ];
 
        $query = new MongoDB\Driver\Query($filter, $options);
        $cursor = $manager->executeQuery($database, $query);
        foreach ($cursor as $document) {
            var_dump($document);
        }*/


/* mongoDB view desc 

{
    "_id": "id",
    "Date" : "dataDate",
    "Time" : "dataTime",
    "Station" : "stationName",
    "Airtemp" : "airTemp",
    "Humidity":"humidity",
    "AirPressure":"airPressure",
    "AP change" : "airPressureDelta",
    "WindDir":"windDirection",
    "WindSpeed" : "windSpeed",
    "WindMax" : "windSpeedMax",
    "Cloudiness":"cloudiness",
    "Current weather" : "currentWeatherSensor",
    "Current (obs)" : "currentWeatherObserver",
    "Rain":"precipitation",
    "Visibility":"visibility"
}
*/
?>
