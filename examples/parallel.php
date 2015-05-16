<?php

require __DIR__.'/../vendor/autoload.php';

use function chh\httpfetch\fetch;

$start = microtime(true);

$response1 = fetch('http://www.example.com', ['future' => 'lazy']);
$response2 = fetch('http://www.example.com', ['future' => 'lazy']);

echo microtime(true) - $start, "\n";

$start = microtime(true);

echo $response1['status'], "\n";
echo $response2['status'], "\n";

echo microtime(true) - $start, "\n";
