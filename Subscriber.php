<?php

class Subscriber {

  protected $tags_arr;
  protected $nodes;
  protected $structs;

  public function __construct($tags_arr) {
    $this->tags_arr = $tags_arr;
  }
}
