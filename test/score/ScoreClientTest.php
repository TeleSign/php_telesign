<?php

namespace telesign\sdk\score;

use telesign\sdk\Example;
use telesign\sdk\ClientTest;

final class ScoreClientTest extends ClientTest
{
  const EXAMPLE_PHONE_NUMBER = Example::PHONE_NUMBER;
  const EXAMPLE_EMAIL_ADDRESS = Example::EMAIL_ADDRESS;
  const EXAMPLE_ACCOUNT_LIFECYCLE_EVENT = Example::ACCOUNT_LIFECYCLE_EVENT;

  function getRequestExamples()
  {
    return 
    [
      [
        ScoreClient::class,
        "score",
          [
            self::EXAMPLE_PHONE_NUMBER,
            self::EXAMPLE_ACCOUNT_LIFECYCLE_EVENT,
            [ "optional_param" => "123" ]
          ],
          self::EXAMPLE_REST_ENDPOINT . "/intelligence/phone", 
          [
            "phone_number" => self::EXAMPLE_PHONE_NUMBER,      
            "account_lifecycle_event" => self::EXAMPLE_ACCOUNT_LIFECYCLE_EVENT,
            "optional_param" => "123"
          ]
        ],
      [
        ScoreClient::class,
        "emailIntelligence",
          [
            self::EXAMPLE_EMAIL_ADDRESS,
            self::EXAMPLE_ACCOUNT_LIFECYCLE_EVENT,
            [ "optional_param" => "123" ]
          ],
          self::EXAMPLE_REST_ENDPOINT . "/intelligence/email", 
          [
            "email_address" => self::EXAMPLE_EMAIL_ADDRESS,      
            "account_lifecycle_event" => self::EXAMPLE_ACCOUNT_LIFECYCLE_EVENT,
            "optional_param" => "123"
          ]
        ]
      ];
    }
}
