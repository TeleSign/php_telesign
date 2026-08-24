<?php

namespace telesign\sdk\phoneid;

use telesign\sdk\rest\RestClient;
use telesign\sdk\rest\Response;
use telesign\sdk\rest\RouteAutoLoader;

use Ramsey\Uuid\Uuid;

/**
 * A set of APIs that deliver deep phone number data attributes that help optimize the end user
 * verification process and evaluate risk.
 */
class PhoneIdClient extends RestClient {

  const PHONEID_RESOURCE = "/v1/phoneid/%s";

  use RouteAutoLoader;

  public function __construct($customer_id, $api_key, $rest_endpoint = self::REST_ENDPOINT, ...$other) {
    parent::__construct($customer_id, $api_key, $rest_endpoint, ...$other);
    $this->routeMap = $this->loadAutomatedRoutes();
  }

  /**
   * The PhoneID API provides a cleansed phone number, phone type, and telecom carrier information to determine the
   * best communication method - SMS or voice.
   *
   * See https://developer.telesign.com/docs/phoneid-api for detailed API documentation.
   */
  function phoneid ($phone_number, array $fields = []) {
    return $this->post(sprintf(self::PHONEID_RESOURCE, $phone_number), $fields, null, null, "application/json", "Basic");
  }

}
