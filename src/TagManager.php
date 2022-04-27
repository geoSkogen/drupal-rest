<?php

class TagManager {

  // indexed array of JSON objects
  protected $nodes;
  protected $structs;
  // associative array of id# keys and arrays of index numbers
  protected $post_indices_by_tag_id;

  public function __construct($json_nodes,$json_structs,$tag_index) {
    $this->nodes = $this->validateFeedItems($json_nodes,'node');
    $this->structs = $this->validateFeedItems($json_structs,'struct');
    $this->post_indices_by_tag_id = $tag_index;
  }

  protected function validateFeedItems($data_arr,$feed_type) {
    $valid_feed = [];
    if (is_array($data_arr)) {
      foreach ($data_arr as $json_entity) {
        $data_node = json_decode($json_entity,true);
        switch ($feed_type) {
          case 'node' :
            if (!empty($data_node['nid'][0]['value'])) { $valid_feed[] = $json_entity; }
            break;
          case 'struct' :
            if (!empty($data_node['title'])) { $valid_feed[] = $json_entity; }
            break;
         default :
        }
      }
    }
    return $valid_feed;
  }

  protected function getSubscribedEntities($tag_ids_arr,$entities_arr) {
    $found_entities = [];
    $found_indices = [];
    if (count($entities_arr)) {
      foreach($tag_ids_arr as $tag_id) {

        $post_index_arr = !empty($this->post_indices_by_tag_id[ $tag_id ]) ?
          $this->post_indices_by_tag_id[ $tag_id ] : [];

        foreach($post_index_arr as $post_index) {
          if (!in_array($post_index,$found_indices)) {
            $found_indices[] = $post_index;
            $found_entities[] = $entities_arr[ $post_index];
          }
        }
      }
    }
    return $found_entities;
  }

  public function getSubscribedNodes($tag_ids) {
    $result = [];
    if (is_array($tag_ids)) {
      $result = $this->getSubscribedEntities($tag_ids, $this->nodes);
    }
    return $result;
  }

  public function getSubscribedStructures($tag_ids) {
    $result = [];
    if (is_array($tag_ids)) {
      $result = $this->getSubscribedEntities($tag_ids, $this->structs);
    }
    return $result;
  }

}
