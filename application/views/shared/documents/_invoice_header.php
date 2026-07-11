<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row align-items-start mb-4">
    <div class="col-sm-6 mb-3 mb-sm-0">
        <h5 class="text-primary fw-bold mb-1">PAYMENT PORTAL, INC.</h5>
        <span class="text-muted small text-uppercase tracking-wider" style="font-size: 0.7rem;">Official Commercial Statement</span>
    </div>
    <div class="col-sm-6 text-sm-end">
        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Invoice Statement</h6>
        <div class="d-inline-block border rounded px-2 py-1 bg-light">
            <span class="text-muted font-monospace small" style="font-size: 0.75rem;">
                ORDER REF: <span class="text-dark fw-bold"><?= html_escape($invoice->order_no); ?></span>
            </span>
        </div>
    </div>
</div>

<hr class="opacity-25 my-4">

<div class="row g-4 mb-4">
    <div class="col-sm-6">
        <span class="text-uppercase text-muted small d-block mb-1 fw-bold" style="font-size: 0.7rem;">Bill To</span>
        <div class="fw-bold text-dark mb-1">Customer Account</div>
        <span class="text-secondary font-monospace small"><?= html_escape($invoice->customer_email); ?></span>
    </div>
    <div class="col-sm-6 text-sm-end">
        <span class="text-uppercase text-muted small d-block mb-1 fw-bold" style="font-size: 0.7rem;">Invoice Details</span>
        <div class="mb-1 small"><span class="text-muted">Invoice No:</span> <strong class="text-dark"><?= html_escape($invoice->invoice_no); ?></strong></div>
        <div class="mb-1 small"><span class="text-muted">Issued Date:</span> <span class="text-dark"><?= date('Y-m-d H:i', strtotime($invoice->issued_at)); ?></span></div>
        <div class="small"><span class="text-muted">Currency:</span> <span class="text-dark">USD ($)</span></div>
    </div>
</div>