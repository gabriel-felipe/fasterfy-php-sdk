<?php
error_reporting(E_ALL);
ini_set("display_errors","on");

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fasterfy\Fasterfy;

$fasterfy = new Fasterfy(dirname(__FILE__)."/../files",1);
$while = $fasterfy->track("while","First while of test");
$while2 = $fasterfy->track("while","First while of test");
$while3 = $fasterfy->track("while","First while of test");

// $while->stop();
$fasterfy->end();
