<?php
error_reporting(E_ALL);
ini_set("display_errors","on");

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fasterfy\Fasterfy;

$fasterfy = new Fasterfy(dirname(__FILE__)."/../files",1);
$filename = "../files/compressed.tar";
if (file_exists($filename)) {
    unlink($filename);
}
if (file_exists($filename.".gz")) {
    unlink($filename.".gz");
}

$filename = $fasterfy->compress($filename);
$result = array("fileUrl"=>null);
if (file_exists($filename)) {
  $result["fileUrl"] = "http://localhost/fasterfyPhpSdk/files/compressed.tar.gz";
}
echo json_encode($result);
