<section class="content pt-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="text-muted small">
                Monitor payment allocations, check status changes, and track generated transaction receipts.
            </div>
        </div>

        <!-- Ledger View Data Card Component -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-nowrap border-bottom text-muted small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4 py-3" width="100">ID</th>
                                <th class="py-3">Receipt No</th>
                                <th class="py-3">Invoice Reference</th>
                                <th class="text-end py-3">Amount</th>
                                <th class="text-center py-3">Status</th>
                                <th class="py-3">Issued By</th>
                                <th width="160" class="pe-4 text-end py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($receipts)): ?>
                                <?php foreach ($receipts as $item): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">#<?= $item->id; ?></td>
                                        <td><code class="text-dark bg-light px-2 py-1 rounded fw-medium"><?= htmlspecialchars($item->receipt_no); ?></code></td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($item->invoice_no ?? 'N/A'); ?></td>
                                        <td class="text-end text-success fw-bold font-monospace">$<?= number_format($item->amount, 2); ?></td>
                                        <td class="text-center">
                                            <?php
                                            $status = strtolower($item->status_name ?? 'pending');
                                            $badgeClass = 'bg-light text-dark border';
                                            if (in_array($status, ['paid', 'completed', 'approved', 'success'])) {
                                                $badgeClass = 'bg-success-subtle text-success border-0';
                                            } elseif (in_array($status, ['cancelled', 'void', 'failed'])) {
                                                $badgeClass = 'bg-danger-subtle text-danger border-0';
                                            }
                                            ?>
                                            <span class="badge rounded-pill px-3 py-2 <?= $badgeClass; ?>">
                                                <?= htmlspecialchars($item->status_name ?? 'Pending'); ?>
                                            </span>
                                        </td>

                                        <td class="text-muted small"><?= htmlspecialchars($item->issuer_name ?? 'System'); ?></td>
                                        <td class="pe-4 text-end">
                                            <a href="<?= site_url('admin/receipts/show/' . $item->id); ?>" class="btn btn-outline-dark btn-sm rounded-pill font-medium" style="font-size: 0.75rem; padding: 0.25rem 0.75rem; white-space: nowrap;">
                                                <i class="fas fa-eye me-1" style="font-size: 0.7rem;"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-receipt fa-3x mb-3 text-light"></i>
                                        <p class="fw-bold mt-2 mb-1">No receipts found</p>
                                        <p class="small mb-0">Receipts are automatically generated when an invoice is paid.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>