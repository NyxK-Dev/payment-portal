<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Receipts extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service('ReceiptService');
        $this->load->library('PdfDocument');
    }

    public function index()
    {
        $data = [
            'title'    => 'Manage Receipts',
            'content'  => 'admin/receipts/index',
            'receipts' => $this->receiptservice->getAllWithRelations()
        ];
        $this->load->view('layouts/app_layout', $data);
    }

    public function show($id)
    {
        $receipt = $this->receiptservice->findWithRelations($id);
        if (!$receipt) {
            show_404();
        }

        $data = [
            'title'   => 'Receipt Details - ' . $receipt->receipt_no,
            'content' => 'admin/receipts/show',
            'receipt' => $receipt
        ];
        $this->load->view('layouts/app_layout', $data);
    }

    public function download($id)
    {
        $receipt = $this->receiptservice->findWithRelations($id);
        if (!$receipt) {
            show_404();
        }

        $this->pdfdocument->streamFromView(
            'admin/receipts/print_pdf',
            ['receipt' => $receipt],
            "Receipt-{$receipt->receipt_no}.pdf"
        );
    }
}
