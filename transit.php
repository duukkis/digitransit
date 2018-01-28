<?php

//-- GRAPHQL request

class Digitransit {
  // 
  private $apiUrl = "https://api.digitransit.fi/routing/v1/routers/hsl/index/graphql";
  
  private function fetch($query){
    $json = json_encode(['query' => $query]);
    $chObj = curl_init();
    curl_setopt($chObj, CURLOPT_URL, $this->apiUrl);
    curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chObj, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($chObj, CURLOPT_HEADER, true);
    curl_setopt($chObj, CURLOPT_POSTFIELDS, $json);
    curl_setopt($chObj, CURLOPT_HTTPHEADER,
       array(
              "Content-Type: application/json"
          )
      ); 
    curl_setopt($chObj, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($chObj);
    return $response;
  }
  
  public function getStop($id){
    $query = '{
       stop(id: "'.$id.'") {
        name
        lat
        lon
        wheelchairBoarding
      }
    }';
    return $this->fetch($query);
  }
  
  public function getRoute($name, $mode){
    $query = '{
      routes(name: "'.$name.'", modes: "'.$mode.'") {
        id
        agency {
          id
        }
        shortName
        longName
        desc
      }
    }';
    return $this->fetch($query);
  }
  
  /**
  * id = HSL:1050:1:01
  */
  public function getStops($id){
   $query = '{
      pattern(id:"'.$id.'") {
        name
        stops{
          name  
        }
      }
    }';
    return $this->fetch($query);
  }
  
  public function getPlan($from, $to, $itiniaries){
    $query = '{
    plan(
      from: {lat: '.$from["lat"].', lon: '.$from["lon"].'}
      to: {lat: '.$to["lat"].', lon: '.$to["lon"].'}
      numItineraries: '.$itiniaries.'
    ) {
    itineraries {
      legs {
        startTime
        endTime
        mode
        duration
        realTime
        distance
        transitLeg
      }
    }
  }
}';
  }
  
} // end Class


$id = "HSL:1040129";
$dt = new Digitransit();
// $stop = $dt->getStop($id);
$route = $dt->getRoute("58", "BUS");
print_r($route);
