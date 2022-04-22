<?php

class TagManager {

  // indexed array of JSON objects
  protected $nodes;
  protected $structs;
  // associative array of id# keys and arrays of index numbers
  protected $post_indices_by_tag_id;

  public function __construct($json_nodes,$json_structs,$tag_index) {
    $this->nodes = $json_nodes;
    $this->structs = $json_structs;
    $this->post_indices_by_tag_id = $tag_index;
  }

  protected function getSubscribedEntities($tag_ids_arr,$entities_arr) {
    $found_entities = [];
    $found_indices = [];
    foreach($tag_ids_arr as $tag_id) {

      $post_index_arr = $this->post_indices_by_tag_id[ $tag_id ];

      foreach($post_index_arr as $post_index) {
        if (!in_array($post_index,$found_indices)) {
          $found_indices[] = $post_index;
          $found_entities[] = $entities_arr[ $post_index];
        }
      }
    }
    return $found_entities;
  }

  public function getSubscribedNodes($tag_ids_arr) {
    return $this->getSubscribedEntities($tag_ids, $this->nodes);
  }

  public function getSubscribedSructures($tag_ids_arr) {
    return $this->getSubscribedEntities($tag_ids, $this->structs);
  }



}
