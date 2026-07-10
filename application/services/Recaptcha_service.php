<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recaptcha_service
{
    protected $secret;
    protected $lastError = null;

    public function __construct()
    {
        $this->secret = getenv('RECAPTCHA_SECRET') ?: '';
    }

    public function verify($token, $remoteIp = null)
    {
        $this->lastError = null;

        if (empty($this->secret)) {
            $this->lastError = 'secret_missing';
            return false;
        }

        if (empty($token)) {
            $this->lastError = 'token_missing';
            return false;
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify';

        $data = http_build_query([
            'secret' => $this->secret,
            'response' => $token,
            'remoteip' => $remoteIp,
        ]);

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $data,
                'timeout' => 5,
            ]
        ];

        $context  = stream_context_create($opts);
        $result = @file_get_contents($url, false, $context);

        if ($result === false) {
            // fallback to cURL when allow_url_fopen is disabled
            if (function_exists('curl_version')) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $result = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);
                if ($result === false || $result === '') {
                    $this->lastError = $err ?: 'network_error';
                    return false;
                }
            } else {
                $this->lastError = 'network_unavailable';
                return false;
            }
        }

        $json = json_decode($result);

        if (!isset($json->success) || $json->success !== true) {
            $this->lastError = isset($json->{'error-codes'}) ? implode(',', (array)$json->{'error-codes'}) : 'verification_failed';
            return false;
        }

        // for v3 we might want to inspect score, but accept any success by default
        return true;
    }

    public function isConfigured()
    {
        return !empty($this->secret);
    }

    public function getLastError()
    {
        return $this->lastError;
    }
}
