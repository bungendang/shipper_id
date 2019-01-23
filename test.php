<?php

require_once __DIR__ . '/vendor/autoload.php';

// include 'src/Shipper.php';
use Bungendang\Shipper;
// private $shipper;

$config['api_key'] = "9f97034bf732bbe8bcb9f23d12c581e1";
$shipper = new Shipper($config);

$countries = $shipper->getAreas(1231);

var_dump($countries);