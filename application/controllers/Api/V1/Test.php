<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Api_Controller.php';

class Test extends MY_Api_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->sendResponse(
            [
                'project' => 'Payment Portal',
                'version' => 'v1',
                'status'  => 'running'
            ],
            'API is working.'
        );
    }
}
