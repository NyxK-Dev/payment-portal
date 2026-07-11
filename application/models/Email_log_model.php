<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_log_model extends CI_Model
{
    protected $table = 'email_logs';

    public function __construct()
    {
        parent::__construct();
    }
}