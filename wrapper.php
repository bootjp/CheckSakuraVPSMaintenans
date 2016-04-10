<?php

require_once (__DIR__ . '/vendor/autoload.php');
require_once (__DIR__ . '/Checker.php');

$iniFilePath = getenv('ENV_INI');

$result = ((new Checker($iniFilePath !== false ? $iniFilePath : null))->fetch());

if (count($result) !== 0) {
    var_export($result);
    exit(1);
}

echo "ipaddress was not found.\n";
