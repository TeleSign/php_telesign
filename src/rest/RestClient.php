<?php

namespace telesign\sdk\rest;

use Eloquent\Composer\Configuration\ConfigurationReader;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

use const telesign\sdk\version\VERSION;

/**
 * The TeleSign RestClient is a generic HTTP REST client that can be extended to make requests against any of
 * TeleSign's REST API endpoints.
 *
 * RequestEncodingMixin offers the function _encode_params for url encoding the body for use in string_to_sign outside
 * of a regular HTTP request.
 *
 * See https://developer.telesign.com for detailed API documentation.
 */
class RestClient {

  protected $customer_id;
  protected $api_key;
  protected $user_agent;
  protected $client;
  protected $rest_endpoint;

  /**
   * TeleSign RestClient instantiation function
   *
   * @param string   $customer_id   Your customer_id string associated with your account
   * @param string   $api_key       Your api_key string associated with your account
   * @param string   $rest_endpoint Override the default rest_endpoint to target another endpoint string
   * @param float    $timeout       How long to wait for the server to send data before giving up
   * @param string   $proxy         URL of the proxy
   * @param callable $handler       Guzzle's HTTP transfer override
   */
  function __construct (
    $customer_id,
    $api_key,
    $rest_endpoint = "https://rest-api.telesign.com",
    $timeout = 10,
    $proxy = null,
    $handler = null
  ) {
    $this->customer_id = $customer_id;
    $this->api_key = $api_key;
    $this->rest_endpoint = $rest_endpoint;

    $this->client = new Client([
      "timeout" => $timeout,
      "proxy" => $proxy,
      "handler" => $handler
    ]);

    $sdk_version = VERSION;
    $php_version = PHP_VERSION;
    $guzzle_version = Client::MAJOR_VERSION;

    $this->user_agent = "TeleSignSDK/php-$sdk_version PHP/$php_version Guzzle/$guzzle_version";
  }

  function setRestEndpoint($rest_endpoint) {
    $this->rest_endpoint = $rest_endpoint;
  }

  /**
   * Generates the TeleSign REST API headers used to authenticate requests.
   *
   * Creates the canonicalized string_to_sign and generates the HMAC signature. This is used to authenticate requests
   * against the TeleSign REST API.
   *
   * See https://developer.telesign.com/docs/authentication-1 for detailed API documentation.
   *
   * @param string $customer_id        Your account customer_id
   * @param string $api_key            Your account api_key
   * @param string $method_name        The HTTP method name of the request, should be one of 'POST', 'GET', 'PUT' or
   *                                   'DELETE'
   * @param string $resource           The partial resource URI to perform the request against
   * @param string $url_encoded_fields HTTP body parameters to perform the HTTP request with, must be urlencoded
   * @param string $date               The date and time of the request
   * @param string $nonce              A unique cryptographic nonce for the request
   * @param string $user_agent         User Agent associated with the request
   * @param string $content_type       Content-Type to send in header
   * @param string $auth_method        Authentication method
   *
   * @return array The TeleSign authentication headers
   */
  static function generateTelesignHeaders (
    $customer_id,
    $api_key,
    $method_name,
    $resource,
    $url_encoded_fields,
    $date = null,
    $nonce = null,
    $user_agent = null,
    $content_type = null,
    $auth_method = "HMAC-SHA256"
  ) {
    if (!$date) {
      $date = gmdate("D, d M Y H:i:s T");
    }

    if (!$nonce) {
      $nonce = Uuid::uuid4()->toString();
    }
    
    if (!$content_type) {
        $content_type = in_array($method_name, ["POST", "PUT"]) ? "application/x-www-form-urlencoded" : "";
    }


    if ($auth_method === "Basic") {
      $credentials = base64_encode("$customer_id:$api_key");

      $authorization = "Basic $credentials";
    } else {
      $string_to_sign_builder = [
        $method_name,
        "\n$content_type",
        "\n$date",
        "\nx-ts-auth-method:$auth_method",
        "\nx-ts-nonce:$nonce"
      ];
  
      if ($content_type && $url_encoded_fields) {
        $string_to_sign_builder[] = "\n$url_encoded_fields";
      }
  
      $string_to_sign_builder[] = "\n$resource";
  
      $string_to_sign = join("", $string_to_sign_builder);
  
      $signature = base64_encode(
        hash_hmac("sha256", mb_convert_encoding($string_to_sign, "UTF-8", mb_detect_encoding($string_to_sign)), base64_decode($api_key), true)
      );
      $authorization = "TSA $customer_id:$signature";
    }
    
    $headers = [
      "Authorization" => $authorization,
      "Date" => $date,
      "Content-Type" => $content_type,
      "x-ts-auth-method" => $auth_method,
      "x-ts-nonce" => $nonce
    ];

    if ($user_agent) {
      $headers["User-Agent"] = $user_agent;
    }

    return $headers;
  }

  /**
   * Generic TeleSign REST API POST handler
   *
   * @param string $resource The partial resource URI to perform the request against
   * @param array  $fields   Body params to perform the POST request with
   * @param string $date     The date and time of the request
   * @param string $nonce    A unique cryptographic nonce for the request
   *
   * @return \telesign\sdk\rest\Response The RestClient Response object
   */
  function post (...$args) {
    return $this->execute("POST", ...$args);
  }

  /**
   * Generic TeleSign REST API GET handler
   *
   * @param string $resource The partial resource URI to perform the request against
   * @param array  $fields   Query params to perform the GET request with
   * @param string $date     The date and time of the request
   * @param string $nonce    A unique cryptographic nonce for the request
   *
   * @return \telesign\sdk\rest\Response The RestClient Response object
   */
  function get (...$args) {
    return $this->execute("GET", ...$args);
  }

  /**
   * Generic TeleSign REST API PUT handler
   *
   * @param string $resource The partial resource URI to perform the request against
   * @param array  $fields   Query params to perform the DELETE request with
   * @param string $date     The date and time of the request
   * @param string $nonce    A unique cryptographic nonce for the request
   *
   * @return \telesign\sdk\rest\Response The RestClient Response object
   */
  function put (...$args) {
    return $this->execute("PUT", ...$args);
  }

  /**
   * Generic TeleSign REST API DELETE handler
   *
   * @param string $resource The partial resource URI to perform the request against
   * @param array  $fields   Query params to perform the DELETE request with
   * @param string $date     The date and time of the request
   * @param string $nonce    A unique cryptographic nonce for the request
   *
   * @return \telesign\sdk\rest\Response The RestClient Response object
   */
  function delete (...$args) {
    return $this->execute("DELETE", ...$args);
  }

  /**
   * Generic TeleSign REST API request handler
   *
   * @param string $resource The partial resource URI to perform the request against
   * @param array  $fields   Body of query params to perform the HTTP request with
   * @param string $date     The date and time of the request
   * @param string $nonce    A unique cryptographic nonce for the request
   * @param string $content_type   Content-Type to send in header
   * @param string $auth_method    Authentication method
   *
   * @return \telesign\sdk\rest\Response The RestClient Response object
   */
  protected function execute ($method_name, $resource, $fields = [], $date = null, $nonce = null, $content_type = null, $auth_method = "HMAC-SHA256") {
    $content_is_json = $content_type === "application/json";

    if ($content_is_json) {
      $form_body = json_encode($fields, count($fields) === 0 ? JSON_FORCE_OBJECT : 0);
    } else {
      $url_encoded_fields = http_build_query($fields, "", "&");
    }

    $headers = $this->generateTelesignHeaders(
      $this->customer_id,
      $this->api_key,
      $method_name,
      $resource,
      $content_is_json ? null : $url_encoded_fields,
      $date,
      $nonce,
      $this->user_agent,
      $content_type,
      $auth_method
    );

    $option = in_array($method_name, [ "POST", "PUT" ]) ? "body" : "query";

    return new Response($this->client->request($method_name, $resource, [
      "headers" => $headers,
      $option => $content_is_json ? $form_body : $url_encoded_fields,
      "http_errors" => false,
      "base_uri" => $this->rest_endpoint,
    ]));
  }

}
