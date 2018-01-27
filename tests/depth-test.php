<?php
error_reporting(E_ALL);
ini_set("display_errors","on");

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fasterfy\Fasterfy;

$fasterfy = new Fasterfy(dirname(__FILE__)."/../files",1);
$while = $fasterfy->track("while","First while of test");
function goDeep($fasterfy, $maxDepth, $currentDepth=1){
  $event = $fasterfy->track("teste", "Teste $currentDepth");
  if ($currentDepth < $maxDepth) {
    goDeep($fasterfy,$maxDepth,$currentDepth+1);
  }
  $event->stop();
}
goDeep($fasterfy,3);
goDeep($fasterfy,3);
// $while->stop();
$fasterfy->end();
