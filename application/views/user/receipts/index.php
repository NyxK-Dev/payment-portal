<section class="content pt-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <!-- Removed duplicate <h3> heading -->
                <p class="text-muted mb-0">
                    View and download receipts for your completed payments.
                </p>
            </div>
        </div>

        <?php if (empty($receipts)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body py-5 text-center">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <h5>No Receipts Found</h5>
                    <p class="text-muted mb-0">
                        Your receipts will appear after successful payments.
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt No</th>
                                <th>Invoice</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                                <th>Issued</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($receipts as $receipt): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= html_escape($receipt->receipt_no); ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <?= html_escape($receipt->invoice_no); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badge = 'bg-secondary';
                                        if (strtolower($receipt->status_name) == 'paid') {
                                            $badge = 'bg-success';
                                        } elseif (strtolower($receipt->status_name) == 'pending') {
                                            $badge = 'bg-warning text-dark';
                                        } elseif (strtolower($receipt->status_name) == 'cancelled') {
                                            $badge = 'bg-danger';
                                        }
                                        ?>
                                        <span class="badge <?= $badge; ?>">
                                            <?= html_escape($receipt->status_name); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        $<?= number_format($receipt->amount, 2); ?>
                                    </td>
                                    <td>
                                        <?= date('Y-m-d H:i', strtotime($receipt->issued_at)); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('user/receipts/show/' . $receipt->id); ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('user/receipts/download/' . $receipt->id); ?>"
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