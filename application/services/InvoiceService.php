<?php

class InvoiceService
{


protected $CI;


public function __construct()
{

$this->CI =& get_instance();


$this->CI->load->model(
'Invoice_model'
);

}



public function createInvoice(
$order
)
{


$existing =
$this->CI
->Invoice_model
->findByOrderId(
$order['id']
);



if($existing)
{
return $existing->id;
}




return $this->CI
->Invoice_model
->insert([


'order_id'=>
$order['id'],


'invoice_no'=>
'INV-'
.date('YmdHis'),


'amount'=>
$order['total_amount'],


'status_lookup_id'=>1,


'issued_at'=>
date(
'Y-m-d H:i:s'
),


'created_at'=>
date(
'Y-m-d H:i:s'
)

]);


}


}