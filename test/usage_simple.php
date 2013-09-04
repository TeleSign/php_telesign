<?php

/*
  Copyright (c) TeleSign Corporation 2012.
  License: MIT
  Support email address "support@telesign.com"
  Author: lqm1010
 */

require_once("../telesign/api.class.php");
require_once("config_test.php");

$customer_id = TEST_CUSTOMER_ID;
$secret_key = TEST_SECRET_KEY;

$verify = new Verify($customer_id, $secret_key);

$phone_number = "13105551234";
$result = $verify->call($phone_number);
// $result = $verify->two_way_sms($phone_number, "TRVF", "123456", "1m");
// $result = $verify->sms($phone_number, "123123");
// $result = $verify->soft_token($phone_number, "", "123123");

print_r($result);