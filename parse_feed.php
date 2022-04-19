<?php

require(__DIR__ . '/FeedParser.php');

$rss_node_type = !empty($argv[1]) ? $argv[1] : 'event';
$feed_parser = new FeedParser('https://syndication.ddev.site',$rss_node_type,null);
$feed_parser->parseFeed();
