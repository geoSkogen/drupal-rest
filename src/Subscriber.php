<?php

class Subscriber {

  protected $tags_arr;
  protected $nodes;
  protected $node_ids;
  protected $structs;
  protected $struct_titles;

  public function __construct($tags_arr) {
    $this->tags_arr = [];
    $this->node_ids = [];
    $this->struct_titles = [];
    $this->tags_arr = $this->validateTags($tags_arr);
  }

  protected function validateTags($data_arr) {
    $valid_tags_arr = [];
    if (is_array($data_arr)) {
      foreach($data_arr as $tag_id) {
        if (intval($tag_id) && !in_array($tag_id,$this->tags_arr)) {
          $valid_tags_arr[] = $tag_id;
        }
      }
    }
    return $valid_tags_arr;
  }

  public function addSubscribedNodes( $json_items ) {
    foreach ($json_items as $json_node) {

      $data_node = json_decode($json_node,true);
      $nid = !empty($data_node['nid'][0]['value']) ?
        $data_node['nid'][0]['value'] : null;

      if (!in_array($nid, $this->node_ids)) {
        $this->nodes[] = $json_node;
      }
    }
    return $this->nodes;
  }

  public function addSubscribedStructures( $json_items ) {
    foreach ($json_items as $json_struct) {

      $data_struct = json_decode($json_struct,true);
      $title = !empty($data_struct['title']) ?
        $data_struct['title'] : null;

      if (!in_array($title, $this->struct_titles)) {
        $this->structs[] = $json_struct;
      }
    }
    return $this->structs;
  }

  public function getTags() {
    return $this->tags_arr;
  }

  public function addTags($data_arr) {
    $this->tags_arr = array_merge(
      $this->tags_arr, $this->validateTags($data_arr)
    );
  }

  public function getNodesJSON() {
    return $this->nodes;
  }

  public function getStructuresJSON() {
    return $this->structs;
  }
}
