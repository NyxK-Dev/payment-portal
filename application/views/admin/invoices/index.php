<section class="content pt-4">
    <div class="container-fluid">
        <!-- Notification Layer -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= html_escape($this->session->flashdata('success')); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Search Framework (Normalized Sizes) -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="<?= site_url('admin/invoices'); ?>" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Search Invoices</label>
                        <input type="text" name="search" class="form-control bg-light border-0" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;" placeholder="Invoice No or Order No..." value="<?= html_escape($this->input->get('search')); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Filter Status</label>
                        <select name="status_lookup_id" class="form-select bg-light border-0" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                            <option value="">All Statuses</option>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status->id; ?>" <?= $this->input->get('status_lookup_id') == $status->id ? 'selected' : ''; ?>>
                                    <?= html_escape($status->value); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 border-0 fw-medium" style="font-size: 0.9rem; padding: 0.5rem 1rem;"><i class="fas fa-filter me-1"></i>Apply Filters</button>
                        <a href="<?= site_url('admin/invoices'); ?>" class="btn btn-light text-secondary fw-medium border" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Grid -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-nowrap border-bottom text-muted small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4 py-3">Invoice Number</th>
                                <th class="py-3">Order Number</th>
                                <th class="py-3">Customer Account</th>
                                <th class="text-end py-3">Total Bill</th>
                                <th class="text-center py-3">Status</th>
                                <th class="py-3">Issued Timestamp</th>
                                <th class="pe-4 text-end py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($invoices)): ?>
                                <?php foreach ($invoices as $row): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary"><?= html_escape($row->invoice_no); ?></td>
                                        <td><span class="badge bg-light text-dark border font-monospace"><?= html_escape($row->order_no); ?></span></td>
                                        <td><?= html_escape($row->customer_name); ?></td>
                                        <td class="text-end fw-bold font-monospace">$<?= html_escape($row->formatted_amount); ?></td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-3 py-2 <?= html_escape($row->badge_class); ?>">
                                                <?= html_escape($row->status_name); ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small"><?= html_escape($row->formatted_created_at); ?></td>
                                        <td class="pe-4 text-end text-nowrap">
                                            <a href="<?= site_url('admin/invoices/view/' . $row->id); ?>" class="btn btn-outline-dark btn-sm rounded-pill font-medium" style="font-size: 0.75rem; padding: 0.25rem 0.75rem; white-space: nowrap;">
                                                <i class="fas fa-eye me-1" style="font-size: 0.7rem;"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-folder-open fa-2x mb-2 text-light"></i>
                                        <p class="mb-0 small">No matching system invoice ledger elements located.</p>
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