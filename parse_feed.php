<?php

require(__DIR__ . '/FeedParser.php');
require(__DIR__ . '/TagManager.php');
require(__DIR__ . '/Subscriber.php');

$rss_node_type = !empty($argv[1]) ? $argv[1] : 'event';
$rss_host = !empty($argv[2]) ? $argv[2] : 'syndication.ddev.site';
$tag_ids_arr = ['831','2706','3096','3171','826'];

$subscriber = new Subscriber($tag_ids_arr);
$feed_parser = new FeedParser('https://' . $rss_host, $rss_node_type, null);
$feed_data = $feed_parser->parseFeed();
$tag_manager = new TagManager($feed_data->json_nodes,$feed_data->json_structs,$feed_data->tag_index);

foreach($feed_parser->data->json_structs as $json_struct) {

  print("\r\n");
  print("Structured JSON: \r\n");
  print_r($json_struct);
  print("\r\n");

}

/*
foreach($feed_parser->data->tag_index as $tag_id => $node_index_arr) {

  print("\r\n");
  print("NODE INDICES FOR TAG: ". strval($tag_id) . "\r\n");
  print_r($node_index_arr);
  print("\r\n");

}
*/
/*
$all_tags = array_keys($feed_parser->data->tag_index);
asort($all_tags);
print_r($all_tags);
*/
