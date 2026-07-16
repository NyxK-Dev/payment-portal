<?php

defined('BASEPATH') or exit('No direct script access allowed');


class AccountingService
{
    protected $invoiceRepository;
    protected $receiptRepository;
    protected $lookupRepository;


    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        ReceiptRepositoryInterface $receiptRepository,
        LookupRepositoryInterface $lookupRepository
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->receiptRepository = $receiptRepository;
        $this->lookupRepository = $lookupRepository;
    }



    /**
     * Create an initial pending invoice for an order
     */
    public function createPendingInvoice(array $order): int
    {

        $invoicePendingStatus =
            $this->lookupRepository
                ->findByGroupAndCode(
                    6,
                    'pending'
                )
                ->id;



        $invoiceNo =
            $this->generateInvoiceNumber();



        return $this->invoiceRepository
            ->create([
                'order_id'         => $order['id'],
                'invoice_no'       => $invoiceNo,
                'amount'           => $order['total'],
                'status_lookup_id' => $invoicePendingStatus,
                'issued_at'        => date('Y-m-d H:i:s'),
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s')
            ]);
    }





    /**
     * Settle invoice and create receipt
     */
    public function fulfillInvoiceAndReceipt(
        int $orderId,
        float $amount
    ): void {


        $paidStatus =
            $this->lookupRepository
                ->findByGroupAndCode(
                    6,
                    'paid'
                )
                ->id;



        $invoice =
            $this->invoiceRepository
                ->findByOrderId($orderId);



        if (!$invoice) {


            $invoiceId =
                $this->invoiceRepository
                    ->create([
                        'order_id'         => $orderId,
                        'invoice_no'       => $this->generateInvoiceNumber(),
                        'amount'           => $amount,
                        'status_lookup_id' => $paidStatus,
                        'issued_at'        => date('Y-m-d H:i:s'),
                        'created_at'       => date('Y-m-d H:i:s')
                    ]);


        } else {


            $invoiceId = $invoice->id;


            $this->invoiceRepository
                ->update(
                    $invoiceId,
                    [
                        'status_lookup_id' => $paidStatus,
                        'updated_at'       => date('Y-m-d H:i:s')
                    ]
                );
        }




        $this->receiptRepository
            ->create([
                'invoice_id'       => $invoiceId,
                'receipt_no'       => $this->generateReceiptNumber(),
                'amount'           => $amount,
                'status_lookup_id' => $paidStatus,
                'issued_at'        => date('Y-m-d H:i:s'),
                'created_at'       => date('Y-m-d H:i:s')
            ]);

    }





    private function generateInvoiceNumber(): string
    {
        return
            'INV-' .
            date('YmdHis') .
            '-' .
            random_int(100,999);
    }




    private function generateReceiptNumber(): string
    {
        return
            'RCT-' .
            date('YmdHis') .
            '-' .
            random_int(100,999);
    }

}