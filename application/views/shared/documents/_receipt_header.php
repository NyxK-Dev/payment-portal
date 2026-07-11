<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row align-items-start mb-4">
    <div class="col-sm-6 mb-3 mb-sm-0">
        <h5 class="text-primary fw-bold mb-1">PAYMENT PORTAL, INC.</h5>
        <span class="text-muted small text-uppercase tracking-wider" style="font-size: 0.7rem;">Official Payment Acknowledgment</span>
    </div>
    <div class="col-sm-6 text-sm-end">
        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Receipt Statement</h6>
        <div class="d-inline-block border rounded px-2 py-1 bg-light">
            <span class="text-muted font-monospace small" style="font-size: 0.75rem;">
                LINKED INVOICE REF: <span class="text-dark fw-bold"><?= html_escape($receipt->invoice_no ?? 'N/A'); ?></span>
            </span>
        </div>
    </div>
</div>

<hr class="opacity-25 my-4">

<div class="row g-4 mb-4">
    <div class="col-sm-6">
        <span class="text-uppercase text-muted fw-bold small d-block mb-1" style="font-size: 0.7rem;">Transaction Context</span>
        <div class="mb-1 small"><span class="text-muted">Receipt Reference ID:</span> <strong class="text-dark">#<?= $receipt->id; ?></strong></div>
        <div class="small"><span class="text-muted">Authorized Agent:</span> <span class="text-dark font-monospace"><?= html_escape($receipt->issuer_name ?? 'Online Payment'); ?></span></div>
    </div>
    <div class="col-sm-6 text-sm-end">
        <span class="text-uppercase text-muted fw-bold small d-block mb-1" style="font-size: 0.7rem;">Receipt Details</span>
        <div class="mb-1 small"><span class="text-muted">Receipt No:</span> <strong class="text-dark"><?= html_escape($receipt->receipt_no); ?></strong></div>
        <div class="mb-1 small"><span class="text-muted">Issued Date:</span> <span class="text-dark"><?= $receipt->issued_at ? date('Y-m-d H:i', strtotime($receipt->issued_at)) : 'N/A'; ?></span></div>
        <div class="small"><span class="text-muted">Currency:</span> <strong class="text-dark">USD ($)</strong></div>
    </div>
</div>