<?php
//error_reporting(E_ALL);
$context = new ZMQContext();
$client = new ZMQSocket($context, ZMQ::SOCKET_REQ);

$identity = sprintf ("%04X", rand(0, 0x10000));
$client->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $identity);

$client->connect("tcp://127.0.0.1:15000");

//  Send request, get reply
$arrSendData[strProvidedURL] = urldecode($_GET["strProvidedURL"]);
$arrSendData[strEndPoint] = $_GET["strEndPoint"];

# let's serialize the array. That way we can decode on the worker side and do what we need to
$client->send(json_encode($arrSendData));
$reply = $client->recv();


header('Content-Type: application/json; charset=utf-8');

// Deliver formatted data
echo $reply;

?>