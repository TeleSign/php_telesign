<?php

require __DIR__ . "/../../vendor/autoload.php";

use telesign\sdk\score\ScoreClient;

$customer_id = "FFFFFFFF-EEEE-DDDD-1234-AB1234567890";
$api_key = "ABC12345yusumoN6BYsBVkh+yRJ5czgsnCehZaOYldPJdmFh6NeX8kunZ2zU1YWaUw/0wV6xfw==";

$email_address = "support@vero-finto.com";
$account_lifecycle_event = "create";

$scoreClient = new ScoreClient($customer_id, $api_key);
$response = $scoreClient->emailIntelligence($email_address, $account_lifecycle_event);

if ($response->ok) {
    $intelligenceDetails = $response->json['intelligence_details'];
    $risk = $intelligenceDetails['risk'];

    printf("Email address %s intelligence report:\n", $email_address);
    printf("  Risk Level: %s\n", $risk['level']);
    printf("  Risk Score: %d\n", $risk['score']);
    printf("  Recommendation: %s\n", $risk['recommendation']);
} else {
    echo "Request failed with status: {$response->status_code}";
}