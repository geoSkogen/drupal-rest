<?php
require_once('vendor/autoload.php');

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;

class RemotePublisher {

  private $token;
  private $cookie_jar;
  protected $host;
  protected $client;


  public function __construct($host,$key,$secret) {
    $this->cookie_jar = new CookieJar();
    $this->host = 'https://' . $host;
    $this->client = new Client([
      'base_url' => $host,
      'cookies' => true,
      'allow_redirects' => true,
      'debug' => true
    ]);
    $this->authToken($key,$secret);
  }


  private function authToken($key,$secret) {
    $token = null;
    try {
      $response = $this->client->post($this->host . '/user/login', [
        "form_params" => [
        "name"=> $key,
        "pass"=> $secret,
        'form_id' => 'user_login_form'
       ],
       'cookies' => $this->cookie_jar
      ]);

      $token = $this->client->get($this->host . '/session/token', [
        'cookies' => $this->cookie_jar
      ])->getBody(TRUE);

      if ($token) {
        $this->token = $token->__toString();
        print('Your CSRF token is: ');
        print($this->token);
      }
    } catch (RequestException $e) {
       if ($e->hasResponse()) {
         $response = $e->hasResponse();
         print $e->hasResponse();
       }
    }
  }


  public function postRequest($data_arr) {
    $data_arr['_links'] = array(
      'type' => array(
        'href' => $this->host . '/rest/type/node/article'
      )
    );
    try {
      $response = $this->client->post($this->host . '/node?_format=hal_json', [
        'cookies' => $this->cookie_jar,
        'headers' => [
      //    'Accept' => 'application/json',
          'Content-type' => 'application/hal+json',
          'X-CSRF-Token' => $this->token,
        ],
        'json' => $data_arr,
      ]);
      // server response code 201: "Created"
      if ($response->getStatusCode() == 201) {
        print 'Node creation successful!';
      } else {
        print "unsuccessful... keep trying";
        print_r($response, true);
      }
    } catch (RequestException $e){
       if ($e->hasResponse()) {
         $response = $e->hasResponse();
         print $e->hasResponse();
       }
    }
    return $response;
  }
  //
}
