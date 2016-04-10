<?php

require_once (__DIR__ . '/vendor/autoload.php');
require_once (__DIR__ . '/Checker.php');

echo "\n";

$result = ((new Checker())->fetch());

if (count($result) !== 0) {
    var_export($result);
    exit(1);
}

echo "ipaddress was not found.\n";
