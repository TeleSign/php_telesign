<?php

namespace telesign\sdk\rest;

use telesign\sdk\Example;
use PHPUnit\Framework\TestCase;
use telesign\sdk\rest\Response as TelesignSdkRestResponse;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;

use Psr\Http\Message\RequestInterface;

final class RestClientTest extends TestCase {

  const EXAMPLE_CUSTOMER_ID = Example::CUSTOMER_ID;
  const EXAMPLE_API_KEY = Example::API_KEY;
  const EXAMPLE_REST_ENDPOINT = Example::REST_ENDPOINT;
  const EXAMPLE_RESOURCE = "/v1/resource";
  const EXAMPLE_FIELDS = [ "test" => "param" ];
  const EXAMPLE_URL_ENCODED_FIELDS = "test=param";
  const EXAMPLE_DATE = "Mon, 27 Jan 2017 23:59:59 GMT";
  const EXAMPLE_NONCE = "5ffb243e-8e2a-4a7b-bf22-2b3925b3d6ef";

  /**
   * @dataProvider getRequestExamples
   */
  function testTelesignHeadersMatchExample ($data) {
    $auth_method = $data["auth_method"] ?? "HMAC-SHA256";
    $actual_headers = RestClient::generateTelesignHeaders(
      self::EXAMPLE_CUSTOMER_ID,
      self::EXAMPLE_API_KEY,
      $data["method_name"],
      self::EXAMPLE_RESOURCE,
      self::EXAMPLE_URL_ENCODED_FIELDS,
      self::EXAMPLE_DATE,
      self::EXAMPLE_NONCE,
      null,
      $data["request"]["headers"]["content-type"],
      $auth_method
    );

    $expected_headers = [
      "Authorization" => $data["request"]["headers"]["authorization"],
      "Date" => self::EXAMPLE_DATE,
      "Content-Type" => $data["request"]["headers"]["content-type"],
      "x-ts-auth-method" => $auth_method,
      "x-ts-nonce" => self::EXAMPLE_NONCE
    ];

    $this->assertEquals($expected_headers, $actual_headers);
  }

  function getRequestExamples () {
    return [
      [[
        "method_name" => "POST",
        "request" => [
          "uri" => self::EXAMPLE_REST_ENDPOINT . self::EXAMPLE_RESOURCE,
          "body" => self::EXAMPLE_URL_ENCODED_FIELDS,
          "headers" => [
            "authorization" => "TSA FFFFFFFF-EEEE-DDDD-1234-AB1234567890:dzREwjKg3/o02ABsf8itcNiNzzKWM293qOavWhfkkok=",
            "content-type" => "application/x-www-form-urlencoded",
          ]
        ]
      ]],
      [[
        "method_name" => "GET",
        "request" => [
          "uri" => self::EXAMPLE_REST_ENDPOINT . self::EXAMPLE_RESOURCE . "?" . self::EXAMPLE_URL_ENCODED_FIELDS,
          "body" => "",
          "headers" => [
            "authorization" => "TSA FFFFFFFF-EEEE-DDDD-1234-AB1234567890:rtUnnJ8wPWEq/pxxT5H+Pj78WHicDzkVYP+dIStuKiQ=",
            "content-type" => ""
          ]
        ]
      ]],
      [[
        "method_name" => "PUT",
        "request" => [
          "uri" => self::EXAMPLE_REST_ENDPOINT . self::EXAMPLE_RESOURCE,
          "body" => self::EXAMPLE_URL_ENCODED_FIELDS,
          "headers" => [
            "authorization" => "TSA FFFFFFFF-EEEE-DDDD-1234-AB1234567890:U2s4H/VqFVnb/flIk9ynhUn6no+VBi2Jlc4YUh4H08k=",
            "content-type" => "application/x-www-form-urlencoded"
          ]
        ]
      ]],
      [[
        "method_name" => "DELETE",
        "request" => [
          "uri" => self::EXAMPLE_REST_ENDPOINT . self::EXAMPLE_RESOURCE . "?" . self::EXAMPLE_URL_ENCODED_FIELDS,
          "body" => "",
          "headers" => [
            "authorization" => "TSA FFFFFFFF-EEEE-DDDD-1234-AB1234567890:PjtMTR0t1JzTZEuB7GKlOpdxpsyaX4Zy+4MBWnwii4w=",
            "content-type" => ""
          ]
        ]
      ]],
      [[
        "method_name" => "PATCH",
        "auth_method" => "Basic",
        "request" => [
          "uri" => self::EXAMPLE_REST_ENDPOINT . self::EXAMPLE_RESOURCE,
          "body" => self::EXAMPLE_FIELDS,
          "headers" => [
            "authorization" => Example::AUTH_BASIC_STRING,
            "content-type" => "application/json"
          ]
        ]
      ]],
    ];
  }

  /**
   * @dataProvider getTelesignHeadersAffectedByOptionalArguments
   */
  function testGeneratesTelesignHeadersAffectedByOptionalArguments ($header_name) {
    $headers = RestClient::generateTelesignHeaders(
      self::EXAMPLE_CUSTOMER_ID,
      self::EXAMPLE_API_KEY,
      "POST",
      self::EXAMPLE_RESOURCE,
      self::EXAMPLE_URL_ENCODED_FIELDS
    );

    $this->assertArrayHasKey($header_name, $headers);
    $this->assertNotEmpty($headers[$header_name]);
  }

  function getTelesignHeadersAffectedByOptionalArguments () {
    return [ [ "Date" ], [ "x-ts-nonce" ] ];
  }

  function testUserAgentMatchesFormat () {
    $mock = new MockHandler([ new Response() ]);

    $client = new RestClient(
      self::EXAMPLE_CUSTOMER_ID, self::EXAMPLE_API_KEY, self::EXAMPLE_REST_ENDPOINT, "php_telesign", null, null, 10, null, $mock
    );
    $client->get(self::EXAMPLE_RESOURCE);

    $user_agent = $mock->getLastRequest()->getHeader("user-agent")[0];
    $php_version = PHP_VERSION;
    $guzzle_version = Client::MAJOR_VERSION;
    $pattern = "`^TeleSignSDK/php PHP/$php_version Guzzle/$guzzle_version OriginatingSDK/php_telesign SDKVersion/[a-zA-Z0-9.\-_]+$`";

    $this->assertRegExp($pattern, $user_agent);
  }

  /**
   * @dataProvider getRequestExamples
   */
  function testSendsRequest ($data) {
    $mock = new MockHandler([ new Response() ]);

    $client = new RestClient(
      self::EXAMPLE_CUSTOMER_ID, self::EXAMPLE_API_KEY, self::EXAMPLE_REST_ENDPOINT, "php_telesign", null, null, 10, null, $mock
    );
    $auth_method = $data["auth_method"] ?? "HMAC-SHA256";
    $content_type = $data["request"]["headers"]["content-type"];
    $client->{$data["method_name"]}(
      self::EXAMPLE_RESOURCE, self::EXAMPLE_FIELDS, self::EXAMPLE_DATE, self::EXAMPLE_NONCE, $content_type, $auth_method
    );

    $request = $mock->getLastRequest();

    $this->assertInstanceOf(RequestInterface::class, $request);
    $this->assertEquals($data["request"]["uri"], $request->getUri());
    $this->assertTrue($request->hasHeader("authorization"));
    $this->assertEquals($data["request"]["headers"]["authorization"], $request->getHeader("authorization")[0]);

    $bodyContent = $request->getBody();
    if ($content_type === "application/json") {
      $parsedBody = json_decode($bodyContent, true);
      $this->assertEquals($data["request"]["body"], $parsedBody);
    } else {
      $this->assertEquals($data["request"]["body"], $bodyContent);
    }
  }

  /**
   * @dataProvider getRequestExamples
   */
  function testReturnsResponse ($data) {
    $mock = new MockHandler([ new Response() ]);

    $client = new RestClient(
      self::EXAMPLE_CUSTOMER_ID, self::EXAMPLE_API_KEY, self::EXAMPLE_REST_ENDPOINT, "php_telesign", null, null, 10, null, $mock
    );
    $response = $client->{$data["method_name"]}(
      self::EXAMPLE_RESOURCE, self::EXAMPLE_FIELDS, self::EXAMPLE_DATE, self::EXAMPLE_NONCE
    );

    $this->assertInstanceOf(TelesignSdkRestResponse::class, $response);
  }

}
