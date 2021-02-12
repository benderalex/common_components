<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload


$client = new \CashBerry\UnifiedBusClient\Client();

$request = new \CashBerry\UnifiedBusClient\Request\HighRiskDbCheckRequest();

$request->setAddress('Киев');
$request->setBirthday('1947-12-17');
$request->setFullName('Азаров Микола Янович');
$request->setDoc('МР223344');
$request->setIpn('3333123456');


$rr = $client->sendRequest('sdsds', 112121212, 'high-risk-db-check', $request);

var_dump($rr);