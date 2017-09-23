<?php
error_reporting(E_ALL);
ini_set("display_errors","on");

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fasterfy\Fasterfy;

$fasterfy = new Fasterfy(dirname(__FILE__)."/../files",1);
$while = $fasterfy->track("while","First while of test");
$a = 0;
while ($a <= 2) {
        $secondWhile = $fasterfy->track("while");
        $b = 0;
        while ($b <= 2) {
            sleep(0.5);
            $b++;
        }
        $secondWhile->stop();

    sleep(0.5);



    $a++;
}
// $while->stop();
$fasterfy->end();
