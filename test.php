<?php

require 'vendor/autoload.php';

require_once("Municipalities.php");

use PhpOffice\PhpSpreadsheet\IOFactory;

/*$reader = IOFactory::load("abr_geo.xlsx");
$data = $reader->getActiveSheet()->toArray(null, true, true, true);


echo '<pre>';
print_r($data);
echo'</pre>';*/

$municipalities = new Municipalities("pgsql", "ec2-54-216-48-43.eu-west-1.compute.amazonaws.com", "5432", "ygokedofbdwibn", "5d6a167d165e3b2f58bb679199ce1265f645ff8c661045fe788790d11d42cb9e", "dqse8add65rhh");
//$mun->storeMunicipalities();

$alive = $municipalities->selectAll();


//print_r($alive[32]["name"]);
//$l = $alive[rand(0,sizeof($alive)-1)];
$l = $alive[32];


 print_r($municipalities->getMunicipalityByName("Sant'Eufemia a Maiella"));