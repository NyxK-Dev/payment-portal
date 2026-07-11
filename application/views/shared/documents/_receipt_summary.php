<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-muted text-uppercase small fw-bold border-bottom">
            <tr>
                <th>Transaction Description</th>
                <th class="text-center" style="width: 20%;">Status</th>
                <th class="text-end" style="width: 25%;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Payment Received against Invoice #<?= html_escape($receipt->invoice_no ?? 'N/A'); ?></div>
                    <div class="text-muted font-monospace small" style="font-size: 0.75rem;">Receipt Reference Key: <?= html_escape($receipt->receipt_no); ?></div>
                </td>
                <td class="text-center">
                    <?php
                    $status = strtolower($receipt->status_name ?? 'pending');
                    $badgeClass = 'bg-light text-dark border';
                    if (in_array($status, ['paid', 'completed', 'approved', 'success'])) {
                        $badgeClass = 'bg-success-subtle text-success border-0';
                    } elseif (in_array($status, ['cancelled', 'void', 'failed'])) {
                        $badgeClass = 'bg-danger-subtle text-danger border-0';
                    }
                    ?>
                    <span class="badge rounded-pill px-3 py-2 <?= $badgeClass; ?>">
                        <?= html_escape($receipt->status_name ?? 'Pending'); ?>
                    </span>
                </td>
                <td class="text-end fw-bold font-monospace text-dark" style="font-size: 14px;">
                    $<?= number_format($receipt->amount, 2); ?>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="text-end text-muted border-0 pt-4">Subtotal:</td>
                <td class="text-end text-muted font-monospace border-0 pt-4">$<?= number_format($receipt->amount, 2); ?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-end fw-bold text-dark border-0 align-middle">Total Amount Settled:</td>
                <td class="text-end text-primary fw-bold border-0 fs-5 font-monospace">$<?= number_format($receipt->amount, 2); ?></td>
            </tr>
        </tbody>
    </table>
</div>