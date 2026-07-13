<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<section class="content pt-4">
    <div class="container-fluid">

        <!-- Notification Layer -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= html_escape($this->session->flashdata('success')); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

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
                                <?php if (!empty($isAdmin) && $isAdmin): ?>
                                    <th class="py-3">Issued By</th>
                                <?php endif; ?>
                                <th width="160" class="pe-4 text-end py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($receipts)): ?>
                                <?php foreach ($receipts as $item): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">#<?= $item->id; ?></td>
                                        <td><code class="text-dark bg-light px-2 py-1 rounded fw-medium"><?= html_escape($item->receipt_no); ?></code></td>
                                        <td class="fw-bold text-dark"><?= html_escape($item->invoice_no ?? 'N/A'); ?></td>
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
                                                <?= html_escape($item->status_name ?? 'Pending'); ?>
                                            </span>
                                        </td>
                                        <?php if (!empty($isAdmin) && $isAdmin): ?>
                                            <td class="text-muted small"><?= html_escape($item->issuer_name ?? 'System'); ?></td>
                                        <?php endif; ?>
                                        <td class="pe-4 text-end text-nowrap">
                                            <div class="d-inline-flex gap-1">
                                                <a href="<?= site_url($receiptRoute . '/show/' . $item->id); ?>" class="btn btn-outline-dark btn-sm rounded-pill font-medium" style="font-size: 0.75rem; padding: 0.25rem 0.75rem;">
                                                    <i class="fas fa-eye me-1" style="font-size: 0.7rem;"></i> View Details
                                                </a>
                                                <a href="<?= site_url($receiptRoute . '/download/' . $item->id); ?>" class="btn btn-outline-danger btn-sm rounded-pill font-medium" style="font-size: 0.75rem; padding: 0.25rem 0.75rem;">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= (!empty($isAdmin) && $isAdmin) ? '7' : '6'; ?>" class="text-center py-5 text-muted">
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