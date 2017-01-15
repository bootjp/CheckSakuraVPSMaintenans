<?php

require_once (__DIR__ . '/Checker.php');

$iniFilePath = getenv('ENV_INI');
$checker = new SakuraVpsMaintenance\Checker($iniFilePath !== false ? $iniFilePath : null);
$result = $checker->fetch();

if (count($result) !== 0) {
    var_export($result);
    $checker->getException();
    exit(1);
}

echo "ipaddress was not found.\n";
$checker->getException();
