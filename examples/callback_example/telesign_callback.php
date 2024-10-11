<?php
const CUSTOMER_ID = "Your 40 Character Customer ID Goes here";
const API_KEY = "YOUR API KEY GOES HERE";

function _log($log_level, $log_message) {
  //syslog($log_level, $log_message);
  $log_level_sanitized = htmlspecialchars($log_level, ENT_QUOTES, 'UTF-8');
  $log_message_sanitized = htmlspecialchars($log_message, ENT_QUOTES, 'UTF-8');

  // Your log path goes here
  $log_file_path = '/path/to/log/file.log';

  $log_file_handle = fopen($log_file_path, 'a');
  
  if ($log_file_handle) {
    fwrite($log_file_handle, sprintf("%s: %s\n", $log_level_sanitized, $log_message_sanitized));
    
    fclose($log_file_handle);
  } else {
    error_log("Error: Unable to open log file for writing.");
  }
}

// TeleSign sends callback for Delivery Reports, Status Updates, and Mobile Originating SMS using the POST HTTP method.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Generate a signature based on the contents of the callback request body signing with your API Secret Key.
  $postBody = file_get_contents('php://input');
  $signature = base64_encode(
    hash_hmac("sha256", mb_convert_encoding($postBody, "UTF-8", mb_detect_encoding($postBody)), base64_decode(API_KEY), true)
  );
  list($authType, $authToken) = explode(' ', getallheaders()['Authorization'], 2);
  list($requestCustomerID, $requestSignature) = explode(':', $authToken, 2);
  if (hash_equals(CUSTOMER_ID, $requestCustomerID)) {
    if (hash_equals($signature, $requestSignature)) {
      $callbackContents = json_decode($postBody, true);
      // You have now validated the request, and may use the contents of the callback to take some action.
      _log(LOG_DEBUG, sprintf(
        "Received callback for Transaction %s with status %s (%s): %s",
        $callbackContents['reference_id'], $callbackContents['status']['description'], $callbackContents['status']['code'], $postBody
      ));
      header('HTTP/1.1 200 OK', true, 200);
    } else {
      _log(LOG_INFO, sprintf("Invalid signature: %s != %s", $signature, $requestSignature));
      header('HTTP/1.1 401 Unuthorized', true, 401);
    }
  } else {
    _log(LOG_INFO,"Request for unknown Customer ID");
    header('HTTP/1.1 401 Unuthorized', true, 401);
  }
} else {
  _log(LOG_INFO, "Unexpected HTTP Method");
  header('HTTP/1.1 405 Method Not Allowed', true, 405);
}
