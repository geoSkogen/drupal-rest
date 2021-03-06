<?php

/**
* CLI Arguments -
* 1 : rss feed content type
* 2 : syndication domain name
* 3 : comma-separated value string of taxon ids
* 4 : data format of feed (REST, XML)
*/

require(__DIR__ . '/src/FeedParser.php');
require(__DIR__ . '/src/TagManager.php');
require(__DIR__ . '/src/Subscriber.php');

$rss_node_type = !empty($argv[1]) ? $argv[1] : 'event';
$rss_host = !empty($argv[2]) ? $argv[2] : 'syndication.ddev.site';
$tag_ids_arr = !empty($argv[3]) ? explode(',',$argv[3]) : ['831','3096','3171','826'];
$feed_format = empty($argv[4]) ? $argv[4] : 'rest';

$feed_parser = new FeedParser('https://' . $rss_host, $rss_node_type, null);
$feed_data = $feed_parser->parseFeed($feed_format);

$tag_manager = new TagManager(
  $feed_data->json_nodes, $feed_data->json_structs, $feed_data->tag_index
);

$subscriber = new Subscriber($tag_ids_arr);

$subscriber->addSubscribedNodes(
  $tag_manager->getSubscribedNodes( $subscriber->getTags())
);

$subscriber->addSubscribedStructures(
  $tag_manager->getSubscribedStructures( $subscriber->getTags())
);

// Test pattern for data tailored to a specific API : default is 'event'
// To format data for another API, add a {rss_node_type}Format method to FeedParser, e.g. FeedParser::articleFormat
/*
$i = 0;
foreach ($subscriber->getStructuresJSON() as $json_struct) {
  print("\r\n");
  print("Subbed custom structured {$rss_node_type} JSON {$i}: \r\n");
  print($json_struct);
  print("\r\n");
  $i++;
}
*/

// More Test Patterns :
// Test subscription to a tag collection - same as above but with Drupal JSON
$i = 0;
foreach ($subscriber->getNodesJSON() as $json_node) {
  print("\r\n");
  print("Subbed content type {$rss_node_type} Drupal Node JSON {$i}: \r\n");
  print($json_node);
  print("\r\n");
  $i++;
}

// View all Drupal Node objects in the feed
/*
$i = 0;
foreach($feed_data->json_nodes as $json_node) {
  print("\r\n");
  print("Drupal Node JSON {$i} : \r\n");
  print($json_node);
  print("\r\n");
  $i++;
}
*/
/*
// View all data specially structured using the feed
foreach($feed_data->json_structs as $json_struct) {
  print("\r\n");
  print("Structured {$rss_node_type} JSON: \r\n");
  print_r($json_struct);
  print("\r\n");
}
*/
/*
// View lists of node array index numbers by sorted by tag
foreach($feed_data->tag_index as $tag_id => $node_index_arr) {
  print("\r\n");
  print("NODE INDICES FOR TAG: ". strval($tag_id) . "\r\n");
  print_r($node_index_arr);
  print("\r\n");
}
*/
/*
$all_tags = array_keys($feed_data->tag_index);
asort($all_tags);
print 'ALL TAG IDS:';
print_r($all_tags);
*/
