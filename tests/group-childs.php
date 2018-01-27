<?php
error_reporting(E_ALL);
ini_set("display_errors","on");

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fasterfy\Fasterfy;

$fasterfy = new Fasterfy(dirname(__FILE__)."/../files",1);

$parent1 = $fasterfy->track("parent","Parent");
$a = 1;
while ($a <= 10) {
    $childId = rand(1,3);
    $child = $fasterfy->track("child","Child $childId");
    usleep(50000);
    $child->stop();
    $a++;

}
$parent1->stop();
$parent2 = $fasterfy->track("parent","Parent 2");
$a = 1;
while ($a <= 10) {
    $childId = rand(1,3);
    $child = $fasterfy->track("child","Child $childId");
    usleep(50000);
    $child->stop();
    $a++;

}
$parent2->stop();
$fasterfy->end();
