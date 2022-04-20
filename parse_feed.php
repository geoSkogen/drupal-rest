<?php

require(__DIR__ . '/FeedParser.php');

$rss_node_type = !empty($argv[1]) ? $argv[1] : 'event';
$rss_host = !empty($argv[2]) ? $argv[2] : 'syndication.ddev.site';
$feed_parser = new FeedParser('https://' . $rss_host, $rss_node_type, null);
$feed_parser->parseFeed();
