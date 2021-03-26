<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$reader = IOFactory::load("italy_geo.xlsx");
$data = $reader->getActiveSheet()->toArray(null, true, true, true);

echo '<pre>';
print_r($data);
echo'</pre>';