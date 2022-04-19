# Custom RSS Feed Client using Drupal Views and REST
## - Prototype for Proof of Concept
***
#### Publish a custom RSS feed in Drupal by
[creating a View in RSS format:](https://portlandstate.atlassian.net/wiki/spaces/WEBCOMM/pages/2387477334/RSS+Feeds+in+Drupal)
##### (This example script uses custom content type 'event.')
#### [Enable REST endpoints](https://portlandstate.atlassian.net/wiki/spaces/WEBCOMM/pages/2388918467/REST+endpoints+in+Drupal) for the resources used in the RSS View
##### (For this example, allow GET requests for content, i.e `/node` because 'event' is a custom content type.)
***
#### Run `composer install` for the Guzzle HTTP library.
***
#### Execute RSS-to-JSON parsing on the CLI with `parse_feed` - e.g.
`php parse_feed.php event`
##### (For this example, the feed defaults to 'event' if no argument is supplied)
***
#### `FeedParser::parseFeed` returns an object consisting of two arrays:
#### `result->json_nodes` contains the raw JSON responses to the GET requests for each node in the feed
#### `result->json_structs` contains the data with custom-formatting based on the feed type
##### (For this example using events, the custom formatting conforms to the FullCalendar JavaScript API)
