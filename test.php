<?php

require_once __DIR__ . '/vendor/autoload.php';

// include 'src/Shipper.php';
use Bungendang\Shipper;
// private $shipper;

$config['api_key'] = "9f97034bf732bbe8bcb9f23d12c581e1";
$shipper = new Shipper($config);

$data = [
	"o"=>4567,
	"d"=>4342,
	"wt"=>1.5,
	"l"=>10,
	"w"=>10,
	"h"=>10,
	"v"=>1000,
	"type"=>2
];

$countries = $shipper->getCourier($data);



// var_dump($countries);

$allcities = $shipper->getCitiesAll();

var_dump($allcities);