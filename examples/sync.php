<?php

require __DIR__.'/../vendor/autoload.php';

use function chh\httpfetch\fetch;

$start = microtime(true);

$response1 = fetch('http://www.example.com', ['future' => false]);
echo $response1['status'], "\n";

$response2 = fetch('http://www.example.com', ['future' => false]);
echo $response2['status'], "\n";

echo microtime(true) - $start, "\n";
