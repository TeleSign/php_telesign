<?php

namespace telesign\sdk\voice;

use telesign\sdk\rest\RestClient;
use telesign\sdk\rest\RouteAutoLoader;

/**
 * TeleSign's Voice API allows you to easily send voice messages. You can send alerts, reminders, and notifications,
 * or you can send verification messages containing time-based, one-time passcodes (TOTP).
 */
class VoiceClient extends RestClient {

  const VOICE_RESOURCE = "/v1/voice";
  const VOICE_STATUS_RESOURCE = "/v1/voice/%s";

  use RouteAutoLoader;

  public function __construct($customer_id, $api_key, $rest_endpoint = self::REST_ENDPOINT, ...$other) {
    parent::__construct($customer_id, $api_key, $rest_endpoint, ...$other);
    $this->routeMap = $this->loadAutomatedRoutes();
  }

  /**
   * Send a voice call to the target phone_number.
   *
   * See https://developer.telesign.com/docs/voice-api for detailed API documentation.
   */
  function call ($phone_number, $message, $message_type, array $other = []) {
    return $this->post(self::VOICE_RESOURCE, array_merge($other, [
      "phone_number" => $phone_number,
      "message" => $message,
      "message_type" => $message_type
    ]));
  }

  /**
   * Retrieves the current status of the voice call.
   *
   * See https://developer.telesign.com/docs/voice-api for detailed API documentation.
   */
  function status ($reference_id, array $params = []) {
    return $this->get(sprintf(self::VOICE_STATUS_RESOURCE, $reference_id), $params);
  }

}
