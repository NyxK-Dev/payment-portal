<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PaypalService
{
    private $clientId;
    private $secret;
    private $isSandbox;

    public function __construct()
    {
        $CI = &get_instance();
        $CI->config->load('paypal');

        $this->clientId  = $CI->config->item('paypal_client_id');
        $this->secret    = $CI->config->item('paypal_secret');
        $this->isSandbox = $CI->config->item('paypal_mode') === 'sandbox';
    }

    /**
     * Get PayPal API base URL
     */
    private function getApiBaseUrl()
    {
        return $this->isSandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    /**
     * Get OAuth2 access token
     */
    private function getAccessToken()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getApiBaseUrl() . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->clientId . ':' . $this->secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_POST, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Failed to get PayPal access token: ' . $response);
        }

        $data = json_decode($response, true);
        return $data['access_token'];
    }

    /**
     * Create a PayPal order
     */
    public function createOrder($order, $payment)
    {
        $accessToken = $this->getAccessToken();

        // Build the request body
        $total = $order['total'];
        if (is_array($total)) {
            $total = isset($total['amount']) ? $total['amount'] : reset($total);
        }
        $total = number_format((float)$total, 2, '.', '');

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'order_' . $order['id'],
                    'description'  => 'Order #' . $order['id'],
                    'amount' => [
                        'currency_code' => 'USD',
                        'value'         => $total
                    ]
                ]
            ],
            'application_context' => [

                'return_url' => site_url('payment/paypal/success'),

                'cancel_url' => site_url('payment/paypal/cancel')

            ]
        ];
        

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getApiBaseUrl() . '/v2/checkout/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            $error = json_decode($response, true);
            $message = $error['message'] ?? $response;
            throw new Exception('PayPal API error: ' . $message);
        }

        $data = json_decode($response, true);

        // Extract approval URL
        $approvalUrl = null;
        foreach ($data['links'] as $link) {
            if ($link['rel'] === 'approve') {
                $approvalUrl = $link['href'];
                break;
            }
        }

        if (!$approvalUrl) {
            throw new Exception('No approval URL found in PayPal response.');
        }

        return [
            'success'      => true,
            'approval_url' => $approvalUrl,
            'order_id'     => $data['id']
        ];
    }

    /**
     * Capture an approved order
     */
    public function captureOrder($orderId)
    {
        $accessToken = $this->getAccessToken();
        $url = $this->getApiBaseUrl() . '/v2/checkout/orders/' . $orderId . '/capture';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            $error = json_decode($response, true);
            throw new Exception($error['message'] ?? 'Capture failed');
        }

        return [
            'success' => true,
            'data'    => json_decode($response, true)
        ];
    }
}
