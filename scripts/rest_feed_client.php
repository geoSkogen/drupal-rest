<?php
require(__DIR__ . '/../vendor/autoload.php');
use GuzzleHttp\Client;
$client = new GuzzleHttp\Client();

try {
  $rss_response = $client->request(
    'GET',
    'https://syndication.ddev.site/article-rss?_format=hal_json',
    []
  );
  $response = $rss_response->getBody();
  $json = json_decode($response,true);

} catch (RequestException $e) {
  if ($e->hasResponse()) {
    $response = $e->hasResponse();
  }
}

print($response);
