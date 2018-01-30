<?php
include("transit.php");

$dt = new Digitransit();

/*
// get stop
$id = "HSL:1040129";
$stop = $dt->getStop($id);
print_r($stop);
sleep(1);

// get route
$route = $dt->getRoute("58", "BUS");
print_r($route);
sleep(1);

// get stops
$stops = $dt->getStops("HSL:1050:1:01");
print_r($stops);
sleep(1);
*/

// get plan
$from = array("lat" => 60.168992, "lon" => 24.932366);
$to = array("lat" => 60.175294, "lon" => 24.684855);
$plan = $dt->getPlan($from, $to, 3);
print_r($plan);
sleep(1);

