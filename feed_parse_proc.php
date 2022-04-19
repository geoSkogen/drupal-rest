<?php

require(__DIR__ . "/vendor/autoload.php");
use GuzzleHttp\Client;

// Accepts one command line arugument for node type, e.g.:
// php feed-parser.php event

function clean_dev_xml($xml_str) {
  $html_comment_regex = [
    '/\<\!\-\-.*(\\r)?(\\n)?\-\-\>/',
    '/\<\!\-\-\sFILE\sNAME\sSUGGESTIONS\:/',
    '/(\*|x)\s.*\.twig/',
    '/\-\-\>/'
  ];
  $xml_str = preg_replace(
    $html_comment_regex,
    '',
    html_entity_decode($xml_str)
  );
  $xml_str = str_replace('&', '&amp;',$xml_str);
  $clean_xml_str = substr($xml_str,strpos($xml_str,'<'),strlen($xml_str));
  return $clean_xml_str;
}


function format_event($resp_json) {
  $resp_obj = json_decode($resp_json,true);
  // Build data structure for FullCalendar JS API
  $json_struct = array(
    'tags' => [],
    'title' => $resp_obj['title'][0]['value'],
    'start' => $resp_obj['field_event_datetime_range_all'][0]['value'],
    'end' => $resp_obj['field_event_datetime_range_all'][0]['end_value'],
    'desc' => $resp_obj['field_description'][0]['value'],
  );

  $json_struct['monthly'] = !empty($resp_obj['field_monthly_event'][0]) ? $resp_obj['field_monthly_event'][0]['value'] : '';
  $json_struct['weekly'] = !empty($resp_obj['field_weekly_event'][0]) ? $resp_obj['field_weekly_event'][0]['value'] : '';

  foreach ($resp_obj['field_content_hub_tag'] as $tag_arr) {
    $json_struct['tags'][] = $tag_arr['target_id'];
  }
  return json_encode($json_struct);
}

// Custom Dupal View as RSS endpoint
// https://portlandstate.atlassian.net/wiki/spaces/WEBCOMM/pages/2387477334/RSS+Feeds+in+Drupal#A-Feed-is-a-view
$url_origin = 'https://syndication.ddev.site';
$http_options = ["headers" => [], "body" => ''];
$req_method = 'GET';

$rss_node_type = !empty($argv[1]) ? $argv[1] : 'event';
$rss_uri = '/' . $rss_node_type . 's-rss.xml';

$json_nodes = [];
$json_structs = [];

//
$client = new GuzzleHttp\Client();
$rss_response = $client->request(
  $req_method,
  $url_origin . $rss_uri,
  $http_options
);
$xml_str = clean_dev_xml($rss_response->getBody());

$rss_feed = new SimpleXMLElement($xml_str);

foreach ($rss_feed->channel->item as $rss_node) {
  // Call the REST resource associated with each item on the RSS feed
  $rest_response = $client->request(
    $req_method,
    strval($rss_node->link . '?_format=json'),
    []
  );

  $json_nodes[] = $rest_response->getBody();
  switch($rss_node_type) {
    case 'event' :
      $json_structs[] = format_event( $rest_response->getBody());
      break;
    default :
  }

  print("EVENT FIELDS\r\n");
  print_r($json_structs[count($json_structs)-1]);
  print("\r\n");
}
