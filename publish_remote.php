<?php

/**
* CLI ARGS --
* 1 : Domain of the RSS host
* 2 : API client user name (key)
* 3 : API client password (secret)
*/

require(__DIR__ . '/RemotePublisher.php');

$data_arr =  array(
  'title' => array(0 => array('value' => 'New Node Cookie')),
);

if (!empty($argv[1]) && !empty($argv[2]) && !empty($argv[3]) ) {

  $remote_publisher = new RemotePublisher($argv[1],$argv[2],$argv[3]);
  $post_response = $remote_publisher->postRequest($data_arr);
}
