<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$api_key = 'YOUR_PAYMOB_API_KEY';

// Step 1: Get authentication token
$auth_url = 'https://accept.paymobsolutions.com/api/auth/tokens';
$auth_data = json_encode([
    'api_key' => $api_key,
]);

$auth_response = file_get_contents($auth_url, false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $auth_data,
    ],
]));

$auth_result = json_decode($auth_response, true);
if (!$auth_result || !isset($auth_result['token'])) {
    echo json_encode(['error' => 'Authentication failed']);
    exit;
}

$token = $auth_result['token'];

// Step 2: Create an order
$order_url = 'https://accept.paymobsolutions.com/api/ecommerce/orders';
$order_data = json_encode([
    'auth_token' => $token,
    'delivery_needed' => 'false',
    'amount_cents' => '1000', // Amount in cents (e.g., 10.00 EGP)
    'currency' => 'EGP',
    'items' => [],
]);

$order_response = file_get_contents($order_url, false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $order_data,
    ],
]));

$order_result = json_decode($order_response, true);
if (!$order_result || !isset($order_result['id'])) {
    echo json_encode(['error' => 'Order creation failed']);
    exit;
}

$order_id = $order_result['id'];

// Step 3: Generate payment key
$payment_key_url = 'https://accept.paymobsolutions.com/api/acceptance/payment_keys';
$payment_key_data = json_encode([
    'auth_token' => $token,
    'amount_cents' => '1000',
    'expiration' => 3600,
    'order_id' => $order_id,
    'billing_data' => [
        'apartment' => 'NA',
        'email' => 'email@example.com',
        'floor' => 'NA',
        'first_name' => 'FirstName',
        'street' => 'NA',
        'building' => 'NA',
        'phone_number' => '0123456789',
        'shipping_method' => 'NA',
        'postal_code' => 'NA',
        'city' => 'Cairo',
        'country' => 'EGY',
        'last_name' => 'LastName',
        'state' => 'NA'
    ],
    'currency' => 'EGP',
    'integration_id' => 'YOUR_INTEGRATION_ID'
]);

$payment_key_response = file_get_contents($payment_key_url, false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $payment_key_data,
    ],
]));

$payment_key_result = json_decode($payment_key_response, true);
if (!$payment_key_result || !isset($payment_key_result['token'])) {
    echo json_encode(['error' => 'Payment key generation failed']);
    exit;
}

$payment_key = $payment_key_result['token'];

echo json_encode([
    'payment_key' => $payment_key
]);
