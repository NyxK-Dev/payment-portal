<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Receipts extends MY_Controller
{
    /**
     * Logged in customer ID.
     *
     * @var int
     */
    protected $userId;

    public function __construct()
    {
        parent::__construct();

        // Require authentication
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }

        $this->userId = (int) $this->session->userdata('user_id');

        $this->load->service('ReceiptService');

        $this->load->library('PdfDocument');
    }

    /**
     * -------------------------------------------------------------
     * My Receipts
     *
     * GET:
     * user/receipts
     * -------------------------------------------------------------
     */
    public function index()
    {
        $data = [

            'title' => 'My Receipts',

            'receipts' => $this->receiptservice
                ->getCustomerReceipts($this->userId)

        ];

        $this->render(
            'user/receipts/index',
            $data
        );
    }

    /**
     * -------------------------------------------------------------
     * Receipt Details
     *
     * GET:
     * user/receipts/show/{id}
     * -------------------------------------------------------------
     */
    public function show($id)
    {
        $receipt = $this->receiptservice
            ->getCustomerReceipt(
                $id,
                $this->userId
            );

        if (!$receipt) {
            show_404();
        }

        $this->render(
            'user/receipts/show',
            [

                'title' => 'Receipt #' . $receipt->receipt_no,

                'receipt' => $receipt

            ]
        );
    }

    /**
     * -------------------------------------------------------------
     * Download Receipt PDF
     *
     * GET:
     * user/receipts/download/{id}
     * -------------------------------------------------------------
     */
    public function download($id)
    {
        $receipt = $this->receiptservice
            ->getCustomerReceipt(
                $id,
                $this->userId
            );

        if (!$receipt) {
            show_404();
        }

        $this->pdfdocument->streamFromView(

            'shared/pdf/receipt',

            [

                'receipt' => $receipt

            ],

            'Receipt-' .
                $receipt->receipt_no .
                '.pdf'

        );
    }
}
