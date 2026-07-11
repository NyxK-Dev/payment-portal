<section class="content pt-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <!-- Removed duplicate <h3> heading -->
                <p class="text-muted mb-0">
                    View and download all invoices related to your orders.
                </p>
            </div>
        </div>

        <?php if (empty($invoices)): ?>
            <div class="card shadow-sm border-0">
                <div class="card-body py-5 text-center">
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                    <h5>No invoices found</h5>
                    <p class="text-muted mb-0">
                        Your invoices will appear after your orders are processed.
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm border-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice No</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                                <th>Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= html_escape($invoice->invoice_no); ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <?= html_escape($invoice->order_no); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= html_escape($invoice->badge_class); ?>">
                                            <?= html_escape($invoice->status_name); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        $<?= $invoice->formatted_amount; ?>
                                    </td>
                                    <td>
                                        <?= $invoice->formatted_created_at; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('user/invoices/show/' . $invoice->id); ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('user/invoices/download/' . $invoice->id); ?>"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>