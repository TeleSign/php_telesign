<?php

namespace telesign\sdk;

class Example {

  const CUSTOMER_ID = "FFFFFFFF-EEEE-DDDD-1234-AB1234567890";
  const API_KEY = "ABC12345yusumoN6BYsBVkh+yRJ5czgsnCehZaOYldPJdmFh6NeX8kunZ2zU1YWaUw/0wV6xfw==";
  const AUTH_BASIC_STRING = "Basic RkZGRkZGRkYtRUVFRS1ERERELTEyMzQtQUIxMjM0NTY3ODkwOkFCQzEyMzQ1eXVzdW1vTjZCWXNCVmtoK3lSSjVjemdzbkNlaFphT1lsZFBKZG1GaDZOZVg4a3VuWjJ6VTFZV2FVdy8wd1Y2eGZ3PT0=";
  const REST_ENDPOINT = "https://www.example.com";
  const PHONE_NUMBER = "13103409700";
  const UCID = "OTHR";
  const REFERENCE_ID = "AEBC93B5898342F790E4E19FED41A7DA";
  const ACCOUNT_LIFECYCLE_EVENT = "create";

  static function objExampleVerification() {
    $obj = [
      "verification_policy" => [
        [ "method" => "sms" ]
      ],
      "recipient" => [
        "phone_number" => self::PHONE_NUMBER
      ]
    ];

    return $obj;
  }
}
