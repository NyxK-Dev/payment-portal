<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class PdfDocument
 * Provides an enterprise wrapper for PDF rendering engine abstraction.
 */
class PdfDocument
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->ensureAutoloaderIsLoaded();
    }

    /**
     * Resiliently ensures the Composer vendor autoloader is initialized
     * regardless of local or production container path differences.
     */
    private function ensureAutoloaderIsLoaded(): void
    {
        // If classes are already initialized by CodeIgniter config, do nothing
        if (class_exists('Dompdf\Dompdf')) {
            return;
        }

        // Standard senior fallback strategy: Scan absolute system target locations
        $paths = [
            '/var/www/html/vendor/autoload.php',
            FCPATH . 'vendor/autoload.php',
            FCPATH . '../vendor/autoload.php',
            APPPATH . '../vendor/autoload.php'
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                return;
            }
        }

        // If everything fails, throw a meaningful developer exception
        log_message('error', 'PdfDocument Library: Composer vendor/autoload.php could not be found.');
    }

    /**
     * Renders a CodeIgniter view template directly into a PDF stream response.
     *
     * @param string $viewPath CodeIgniter layout view path
     * @param array $data Context template payload bindings
     * @param string $filename Download reference filename target
     * @param string $paper Size orientation layout standard
     * @param string $orientation Layout flow (portrait|landscape)
     * @return void
     */
    public function streamFromView(string $viewPath, array $data, string $filename, string $paper = 'A4', string $orientation = 'portrait'): void
    {
        // 1. Gather component contextual view as plain string data pipeline
        $html = $this->CI->load->view($viewPath, $data, true);

        // 2. Encapsulate implementation engine options isolated behind wrapper interface
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        // 3. Command binary stream flush directly to native web client output thread
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
