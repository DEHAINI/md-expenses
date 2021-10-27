<?php
require(dirname(__FILE__)."/../../vendor/autoload.php");
$openapi = \OpenApi\Generator::scan([dirname(__FILE__).'/../../src/Controller']);
header('Content-Type: application/json');
echo $openapi->toJson();