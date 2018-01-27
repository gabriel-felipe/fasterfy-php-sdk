<?php
error_reporting(E_ALL);
ini_set("display_errors","on");

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fasterfy\Fasterfy;

Fasterfy::setLogFile("../log.log");
$fasterfy = new Fasterfy(dirname(__FILE__)."/../files",1);

$event = $fasterfy->track("event","oie");
$event->toArray();

echo "oie";
