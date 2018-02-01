<?php

//-- GRAPHQL request

class Digitransit {
  
  private $apiUrl = "https://api.digitransit.fi/routing/v1/routers/hsl/index/graphql";
  private $geocodingSearchUrl = "https://api.digitransit.fi/geocoding/v1/search";
  private $geocodingReverseGeocodingUrl = "http://api.digitransit.fi/geocoding/v1/reverse";
  private $cdn = "https://cdn.digitransit.fi/map/v1/hsl-map/";
  
  public function setApiUrl($url){
    $this->apiUrl = $url;
  }
  public function setGeocodingUrl($url){
    $this->geocodingUrl = $url;
  }
  
  /**
  * post fetch QL
  */
  private function fetchQL($query){
    $json = json_encode(['query' => $query]);
    $chObj = curl_init();
    curl_setopt($chObj, CURLOPT_URL, $this->apiUrl);
    curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chObj, CURLOPT_CUSTOMREQUEST, 'POST');
    // curl_setopt($chObj, CURLOPT_HEADER, true);
    curl_setopt($chObj, CURLOPT_POSTFIELDS, $json);
    curl_setopt($chObj, CURLOPT_HTTPHEADER,
       array(
              "Content-Type: application/json",
              "User-agent: https://github.com/duukkis/digitransit" 
          )
      ); 
    curl_setopt($chObj, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($chObj);
    return $response;
  }
  
  /**
  *
  */
  private function fetchGeo($url, $params){
    $url .= "?";
    if(!empty($params)){
      foreach($params AS $key => $value){
        $url .= $key."=".$value."&";
      }
    }
    $chObj = curl_init();
    curl_setopt($chObj, CURLOPT_URL, $url);
    curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true);
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
    return $this->fetchQL($query);
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
    return $this->fetchQL($query);
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
    return $this->fetchQL($query);
  }
  
  public function getPlan($from, $to, $itiniaries = 3){
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
    return $this->fetchQL($query);
  }
  
  /**
  * address search
  * https://www.digitransit.fi/en/developers/apis/2-geocoding-api/address-search/
  */
  public function addressSearch($params){
    $allowed = array("text", "size", "boundary.rect.min_lon", "boundary.rect.max_lon", "boundary.rect.min_lat", "boundary.rect.max_lat", "boundary.circle.lat", "boundary.circle.lon", "boundary.circle.radius", "focus.point.lat", "focus.point.lon", "sources", "layers", "boundary.country", "lang");
    if(!empty($params)){
      foreach($params AS $key => $value){
        if(!in_array($key, $allowed)){
          unset($params[$key]);
        }
      }
    }
    return $this->fetchGeo($this->geocodingSearchUrl, $params);
  }
  
  /**
  * reverce geocode
  * https://www.digitransit.fi/en/developers/apis/2-geocoding-api/address-lookup/
  */
  public function addressGeoCode($params){
    $allowed = array("point.lat", "point.lon", "lang", "boundary.circle.radius", "size", "layers", "sources", "boundary.country");
    if(!empty($params)){
      foreach($params AS $key => $value){
        if(!in_array($key, $allowed)){
          unset($params[$key]);
        }
      }
    }
    return $this->fetchGeo($this->geocodingReverseGeocodingUrl, $params);
  }
  
  /**
  * z int Zoom level
  * x int x-coordinate
  * y int y-coordinate
  * size string ‘@2x’ for retina tiles or empty value for normal
  * https://www.digitransit.fi/en/developers/apis/3-map-api/background-map/
  */
  public function getMapUrl($z, $x, $y, $size = ""){
    return $this->cdn.$z."/".$x."/".$y.$size.".png";
  }
  
} // end Class

