<?php

// use Bungendang\Shipper;

include 'src/Shipper.php';

// private $shipper;

$config['api_key'] = "9f97034bf732bbe8bcb9f23d12c581e1";
$shipper = new Shipper($config);

$shipper->getCountries();