<?php
defined('BASEPATH') or exit('No direct script access allowed');


class EmailLogs extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->service('EmailLogService');
    }



    public function index()
    {
        $data = [];

        $data['title'] = 'Email Logs';

        $data['logs'] = $this->emaillogservice->getLogs();

        $this->render(
            'admin/email_logs/index',
            $data
        );
    }
}
