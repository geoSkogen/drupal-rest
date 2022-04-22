<?php

require(__DIR__ . "/vendor/autoload.php");
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FeedParser {

  protected $client;
  protected $http_options;
  protected $url_origin;
  protected $rss_node_type;
  protected $rss_feed;
  protected $tag_index;
  public $data;


  public function __construct($url_origin,$rss_node_type,$http_options) {
    $this->client = new GuzzleHttp\Client();
    $this->url_origin = $url_origin;
    $this->rss_node_type = $rss_node_type;
    $this->http_options = is_array($http_options) ?
      $http_options : ["headers" => [], "body" => ''];
  }


  public function parseFeed() {
    $raw_xml = $this->requestFeedXML();
    $data_obj = $this->requestFeedJSON($raw_xml);
    return $data_obj;
  }


  protected function initFeed($feed_body) {
    $rss_feed = null;
    $xml_str = $this->cleanDevXML($feed_body);
    try {
      $rss_feed = new SimpleXMLElement($xml_str);
    } catch (Exception $e) {
      print $e->getMessage();
    }
    return $rss_feed;
  }


  protected function requestFeedXML() {
    $response = '';
    try {
      $rss_response = $this->client->request(
        'GET',
        $this->url_origin . '/' . $this->rss_node_type . 's-rss.xml',
        $this->http_options
      );
      $response = $rss_response->getBody();
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        $response = $e->hasResponse();
      }
    }
    return $response;
  }


  protected function requestFeedJSON($raw_xml) {
    $result = new stdClass;
    $result->json_nodes = [];
    $result->json_structs = [];
    $node_index = 0;

    $this->rss_feed = $this->initFeed($raw_xml);

    if ($this->rss_feed) {
      foreach ($this->rss_feed->channel->item as $rss_node) {
        // Call the REST resource associated with each item on the RSS feed
        try {
          $rest_response = $this->client->request(
            'GET',
            strval($rss_node->link . '?_format=json'),
            []
          );

          $result->json_nodes[] = $rest_response->getBody();
          $this->tagIndexFormat( $rest_response->getBody(), $node_index);

          if (method_exists($this, $this->rss_node_type . 'Format')) {
            $result->json_structs[] =
              $this->{$this->rss_node_type . 'Format'}( $rest_response->getBody());
          }
          $node_index++;

        } catch (RequestException $e) {
           if ($e->hasResponse()) {
             $rest_response = $e->hasResponse();
             print $e->hasResponse();
           }
        }
      }
    }
    $result->tag_index = $this->tag_index;
    $this->data = $result;
    return $result;
  }

  function tagIndexFormat($resp_json,$node_index) {
    $resp_obj = json_decode($resp_json,true);
    foreach ($resp_obj['field_content_hub_tag'] as $tag_arr) {
      if (isset($this->tag_index[ $tag_arr['target_id']])) {
        $this->tag_index[ $tag_arr['target_id']][] = $node_index;
      } else {
        $this->tag_index[ $tag_arr['target_id'] ] = [$node_index];
      }
    }
  }


  function eventFormat($resp_json) {
    $resp_obj = json_decode($resp_json,true);
    // Build data structure for FullCalendar JS API, et al.
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


  protected function cleanDevXML($xml_str) {
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

}
