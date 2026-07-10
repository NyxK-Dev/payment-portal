<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Read SMTP/mail configuration from environment variables
$config = array();

$config['protocol'] = getenv('MAIL_PROTOCOL') ?: 'smtp';
$config['smtp_host'] = getenv('SMTP_HOST') ?: '';
$config['smtp_port'] = getenv('SMTP_PORT') ? (int)getenv('SMTP_PORT') : 587;
$config['smtp_user'] = getenv('SMTP_USER') ?: '';
$config['smtp_pass'] = getenv('SMTP_PASS') ?: '';
$config['smtp_crypto'] = getenv('SMTP_CRYPTO') ?: getenv('SMTP_ENCRYPTION') ?: 'tls';
$config['smtp_timeout'] = getenv('SMTP_TIMEOUT') ? (int)getenv('SMTP_TIMEOUT') : 5;

$config['mailtype'] = getenv('MAIL_TYPE') ?: 'html';
$config['charset'] = getenv('MAIL_CHARSET') ?: 'utf-8';
$config['wordwrap'] = TRUE;
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";

// Additional defaults
$config['validate'] = FALSE;

return $config;
