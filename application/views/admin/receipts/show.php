<section class="content pt-4">
    <div class="container-fluid">
        <!-- Compact Clean Header Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <a href="<?= site_url('admin/receipts'); ?>" class="text-secondary text-decoration-none small fw-medium d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-2 text-muted"></i> Back to Receipts
            </a>
            <div>
                <a href="<?= site_url('admin/receipts/download/' . $receipt->id); ?>" class="btn btn-sm btn-outline-dark px-3 rounded-pill fw-medium" style="font-size: 0.85rem;">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>

        <!-- Document Main Body Sheet Frame -->
        <div class="card border-0 shadow-sm p-4 p-md-5 bg-white">
            <div class="row align-items-start mb-4">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <h5 class="text-primary fw-bold mb-1">PAYMENT PORTAL, INC.</h5>
                    <span class="text-muted small text-uppercase tracking-wider" style="font-size: 0.7rem;">Official Payment Acknowledgment</span>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h6 class="text-muted text-uppercase fw-bold mb-2 small">Receipt Statement</h6>
                    <div class="d-inline-block border rounded px-2 py-1 bg-light">
                        <span class="text-muted font-monospace small" style="font-size: 0.75rem;">
                            LINKED INVOICE REF: <span class="text-dark fw-bold"><?= htmlspecialchars($receipt->invoice_no ?? 'N/A'); ?></span>
                        </span>
                    </div>
                </div>
            </div>

            <hr class="opacity-25 my-4">

            <div class="row g-4 mb-4">
                <div class="col-sm-6">
                    <span class="text-uppercase text-muted fw-bold small d-block mb-1" style="font-size: 0.7rem;">Transaction Context</span>
                    <div class="mb-1 small"><span class="text-muted">System Reference ID:</span> <strong class="text-dark">#<?= $receipt->id; ?></strong></div>
                    <div class="small"><span class="text-muted">Authorized Agent:</span> <span class="text-dark font-monospace"><?= htmlspecialchars($receipt->issuer_name ?? 'System Processing'); ?></span></div>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <span class="text-uppercase text-muted fw-bold small d-block mb-1" style="font-size: 0.7rem;">Receipt Details</span>
                    <div class="mb-1 small"><span class="text-muted">Receipt No:</span> <strong class="text-dark"><?= htmlspecialchars($receipt->receipt_no); ?></strong></div>
                    <div class="mb-1 small"><span class="text-muted">Issued Date:</span> <span class="text-dark"><?= $receipt->issued_at ? date('Y-m-d H:i', strtotime($receipt->issued_at)) : 'N/A'; ?></span></div>
                    <div class="small"><span class="text-muted">Currency:</span> <strong class="text-dark">USD ($)</strong></div>
                </div>
            </div>

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
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Payment Received against Invoice #<?= htmlspecialchars($receipt->invoice_no ?? 'N/A'); ?></div>
                                <div class="text-muted font-monospace small" style="font-size: 0.75rem;">Receipt Reference Key: <?= htmlspecialchars($receipt->receipt_no); ?></div>
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
                                    <?= htmlspecialchars($receipt->status_name ?? 'Pending'); ?>
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
        </div>
    </div>
</section>