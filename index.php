<?php

require(__DIR__ . '/FeedParser.php');

$feed_parser = new FeedParser('https://syndication.ddev.site','event',null);
$feed_parser->parseFeed();
