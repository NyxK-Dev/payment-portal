<section class="content pt-4">
    <div class="container-fluid">
        <!-- Compact Clean Header Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <a href="<?= site_url('admin/invoices'); ?>" class="text-secondary text-decoration-none small fw-medium d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-2 text-muted"></i> Back to Invoices
            </a>
            <div>
                <a href="<?= site_url('admin/invoices/download/' . $invoice->id); ?>" class="btn btn-sm btn-outline-dark px-3 rounded-pill fw-medium" style="font-size: 0.85rem;">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>

        <!-- Document Main Body Sheet Frame -->
        <div class="card border-0 shadow-sm p-4 p-md-5 bg-white">
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

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted text-uppercase small fw-bold border-bottom">
                        <tr>
                            <th>Item Description</th>
                            <th class="text-center" style="width: 10%;">Qty</th>
                            <th class="text-end" style="width: 20%;">Unit Price</th>
                            <th class="text-end" style="width: 20%;">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <span class="d-block text-dark fw-bold mb-1" style="font-size: 0.95rem;"><?= html_escape($item->product_name); ?></span>
                                    <small class="text-muted d-block mb-1"><?= html_escape($item->product_description); ?></small>
                                    <small class="text-muted font-monospace bg-light px-2 py-0.5 rounded" style="font-size: 0.7rem;">SKU: <?= html_escape($item->sku); ?></small>
                                </td>
                                <td class="text-center font-monospace text-secondary"><?= $item->quantity; ?></td>
                                <td class="text-end font-monospace text-muted">$<?= $item->formatted_unit_price; ?></td>
                                <td class="text-end font-monospace fw-bold text-dark">$<?= $item->formatted_line_total; ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <tr>
                            <td colspan="2" class="border-0"></td>
                            <td class="text-end fw-medium text-muted border-0 pt-4">Subtotal:</td>
                            <td class="text-end text-muted font-monospace border-0 pt-4">$<?= $invoice->subtotal_aggregate; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-0"></td>
                            <td class="text-end fw-bold text-dark border-0 align-middle">Total Balance Due:</td>
                            <td class="text-end text-primary fw-bold border-0 fs-5 font-monospace">$<?= $invoice->formatted_total_due; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>