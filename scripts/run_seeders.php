<?php
require_once __DIR__ . '/../vendor/autoload.php';

$basePath = realpath(__DIR__ . '/..');

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'CLI';

require_once $basePath . '/system/core/CodeIgniter.php';

$CI =& get_instance();
$CI->load->database();

$seeder = new DatabaseSeeder();
$seeder->run();

echo "Seeders completed successfully.\n";
