<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seed extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->input->is_cli_request())
        {
            show_404();
        }
    }

    public function index()
    {
        echo PHP_EOL;
        echo "==========================================" . PHP_EOL;
        echo " Running Database Seeder" . PHP_EOL;
        echo "==========================================" . PHP_EOL;

        require_once APPPATH . 'seeds/DatabaseSeeder.php';

        $seeder = new DatabaseSeeder();
        $seeder->run();

        echo PHP_EOL;
        echo "[SUCCESS] Database seeded successfully." . PHP_EOL;
        echo PHP_EOL;
    }
}