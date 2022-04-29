## GET the Drupal REST resources from a custom JSON or XML RSS feed
### using Views & REST UI
### A prototype to illustrate proof of concept:
#### Subscribing to specifically tagged content on a Syndication site in a local dev environment.
***
#### Publish a custom RSS feed in Drupal by [creating a View in RSS format.](https://portlandstate.atlassian.net/wiki/spaces/WEBCOMM/pages/2387477334/RSS+Feeds+in+Drupal)
##### (This example script uses custom content type 'event.')
#### [Enable REST endpoints](https://portlandstate.atlassian.net/wiki/spaces/WEBCOMM/pages/2388918467/REST+endpoints+in+Drupal) for the resources used in the RSS View.
##### (For this example, allow GET requests for content - i.e `/node` - because 'event' is a custom content type.)
***
#### Run `composer install` for the Guzzle HTTP library.
***
### Import a subscription on the CLI by running the 'parse_feed' script with 3 arguments:*
#### *RSS_feed_content type*
#### *Syndication_domain_name*  
#### *CSV_of_tag_ids*
#### *Data format of RSS feed*
#### Examples:
#### `php parse_feed.php event syndication.ddev.site 3171,826 rest`
#### `php parse_feed.php article syndication.ddev.site 831,3096 xml`
***
#### `FeedParser::parseFeed` accepts a string argument for the feed type, e.g. -
##### `$feedParser->parseFeed('xml')` or `$feedParser->parseFeed('rest')`
#### `Subscriber` exposes the array of Drupal Node objects it's subscribed to via `getNodesJSON`.
***
## Dev Notes
### Custom Data Structures
#### `FeedParser::eventFormat` returns JSON tailored to the FullCalendar JS API
`Subscriber` exposes this tailored data array via `getStructuresJSON` when 'event' is passed to the parser as the RSS feed type.
#### Add custom parsing methods for additional content types, e.g. 'article'
If 'article' is passed to the parser as the RSS feed type,
`FeedParser` will search for and call an `articleFormat` method.
Use the logic in {RSS_Feed_Type}Format methods to shape the custom data that will be exposed in `Subscriber::getStructuresJSON`
### Integration
*The CLI command introduces a future layer of abstraction,
where each subscribubg site may have a `Syndication` object,
executing the logic in this repo's parse_feed.php script, but via a public method that accepts the same arguments,
 e.g.
##### `$feed_nodes = Syndication::getFeed($rss_feed_type,$syndication_domain_name,$tag_ids_arr,$response_format)`
