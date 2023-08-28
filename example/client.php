<?php

use JsonRpc\Client;
use Http\Request;

require_once __DIR__ . '/../vendor/autoload.php';

/* Build the request */
$client = new Client();
$client->buildRequest(1, 'getCars', [
    //'last_changed' => '2023-08-27 01:53:21',
    //'page' => 4
]);
$dataEncoded = $client->encode();

/* Make the request to our localhost */
$response = Request::sendJson("http://127.0.0.1:4321", $dataEncoded);
echo "$response\n";
