<?php

namespace telesign\sdk\score;

use telesign\sdk\rest\RestClient;

/**
 * ScoreClient for TeleSign Intelligence Cloud.
 * Supports POST /intelligence/phone endpoint only (Cloud migration).
 * Sends phone number and parameters in the request body encoded as application/x-www-form-urlencoded.
 * See https://developer.telesign.com/enterprise/docs/intelligence-cloud-get-started for documentation.
 */
class ScoreClient extends RestClient
{
    const DETECT_HOST = "https://detect.telesign.com";
    const INTELLIGENCE_RESOURCE = "/intelligence/phone";

    public function __construct($customer_id, $api_key, $rest_endpoint = self::DETECT_HOST)
    {
        parent::__construct($customer_id, $api_key, $rest_endpoint);
    }

    /**
     * Obtain a risk recommendation for a phone number using Telesign Intelligence Cloud API.
     * Required parameters:
     *   - phone_number
     *   - account_lifecycle_event ("create", "sign-in", "transact", "update", "delete")
     * Optional parameters include account_id, device_id, email_address, external_id, originating_ip.
     * API: POST https://detect.telesign.com/intelligence/phone
     * 
     * See https://developer.telesign.com/enterprise/reference/submitphonenumberforintelligencecloud for detailed API documentation.
     */
    public function score($phone_number, $account_lifecycle_event, array $optional = [])
    {
        if (empty($phone_number)) {
            throw new \InvalidArgumentException("phone_number cannot be null or empty");
        }

        if (empty($account_lifecycle_event)) {
            throw new \InvalidArgumentException("account_lifecycle_event cannot be null or empty");
        }

        $params = array_merge($optional, [
            "phone_number" => $phone_number,
            "account_lifecycle_event" => $account_lifecycle_event
        ]);

        return $this->post(
            self::INTELLIGENCE_RESOURCE,
            $params,
            null,
            null,
            "application/x-www-form-urlencoded",
            "HMAC-SHA256"
        );
    }
}