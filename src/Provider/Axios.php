<?php

namespace App\Provider;

class AxiosClient {

  private array $options = [];

  function __construct($options = []) {
    $this->options = $options;
  }

  function sendRequest($method, $url, $options = []) {
    $options = array_merge(
      $this->options,
      $options,
      [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER, 1
      ]
    );

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);

    return $response;
  }
}

class Axios {

}

/*
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://brasilapi.com.br/api/cep/v1/'.$_POST['cep'],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = json_decode(curl_exec($curl));
*/