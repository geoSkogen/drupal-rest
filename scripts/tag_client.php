<?php

require(__DIR__ . '/src/Schema.php');
require(__DIR__ . '/vendor/autoload.php');
use GuzzleHttp\Client;

$tag_schema = new Schema('new_tags','/../');
$tag_table = [];
$client = new GuzzleHttp\Client();


foreach ($tag_schema->data_index as $tag_row) {
  $response = '';
  try {
    $rss_response = $client->request(
      'GET',
      'https://syndication.ddev.site/taxonomy/term/' . $tag_row[0] . '?_format=hal_json',
      []
    );
    $response = $rss_response->getBody();
    $json = json_decode($response,true);
    $tag_table[] = [$tag_row[0],$json['name'][0]['value']];
    print($json['name'][0]['value']);
    print("\r\n");
  } catch (RequestException $e) {
    if ($e->hasResponse()) {
      $response = $e->hasResponse();
    }
  }
}

$tag_export_csv = Schema::make_export_str($tag_table);

Schema::export_csv($tag_export_csv,'news-tags','../resources');

?>
