<?php

require_once (__DIR__ . '/vendor/autoload.php');
require_once (__DIR__ . '/Checker.php');

echo "\n";

$result = ((new Checker())->fetch());

var_export($result);

if (count($result) !== 0) {
    exit(1);
}