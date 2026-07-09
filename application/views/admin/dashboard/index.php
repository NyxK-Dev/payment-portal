<?php

/**
 * @var array $stats
 * @var array $recent_orders
 */
?>

<!-- Stat Blocks Grid Section -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <a href="<?= site_url('admin/users'); ?>" class="stat-box stat-primary">
            <div class="stat-title">Total Users</div>
            <div class="stat-value"><?= number_format($stats['users']); ?></div>
            <div class="stat-icon"><i class="fas fa-users"></i></div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="<?= site_url('admin/products'); ?>" class="stat-box stat-success">
            <div class="stat-title">Active Products</div>
            <div class="stat-value"><?= number_format($stats['products']); ?></div>
            <div class="stat-icon"><i class="fas fa-box"></i></div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="<?= site_url('admin/orders'); ?>" class="stat-box stat-warning">
            <div class="stat-title">Total Orders</div>
            <div class="stat-value"><?= number_format($stats['orders']); ?></div>
            <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="<?= site_url('admin/payments'); ?>" class="stat-box stat-danger">
            <div class="stat-title">Total Payments</div>
            <div class="stat-value"><?= number_format($stats['payments']); ?></div>
            <div class="stat-icon"><i class="fas fa-credit-card"></i></div>
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Live Activity Data Panel -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title m-0">Recent Activity Logs</h3>
                <a href="<?= site_url('admin/orders'); ?>" class="btn btn-sm btn-light text-primary fw-semibold">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recent_orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td class="fw-semibold">#<?= $order['id']; ?></td>
                                        <td><?= html_escape($order['customer_name'] ?? 'Guest Customer'); ?></td>
                                        <td>$<?= number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?= ucfirst($order['status'] ?? 'Pending'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="text-muted mb-2"><i class="fas fa-box-open fa-2x"></i></div>
                        <div class="text-muted fw-medium">No system metrics recorded in this window.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- System Diagnostic Panel -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Infrastructure Health</h3>
            </div>
            <div class="card-body">
                <!-- Secondary Quick-Metric Box inside Diagnostic Panel -->
                <a href="<?= site_url('admin/refunds'); ?>" class="stat-box stat-secondary mb-4">
                    <div class="stat-title">Processed Refunds</div>
                    <div class="stat-value"><?= number_format($stats['refunds']); ?></div>
                    <div class="stat-icon"><i class="fas fa-undo"></i></div>
                </a>

                <table class="table table-sm table-borderless mx-1">
                    <tbody>
                        <tr class="border-bottom">
                            <td class="py-2 text-muted">Core Engine</td>
                            <td class="py-2 text-end">
                                <span class="status-indicator text-success">
                                    <span class="status-dot bg-success"></span> Online
                                </span>
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="py-2 text-muted">App Environment</td>
                            <td class="py-2 text-end text-dark fw-semibold text-uppercase" style="font-size: 0.85rem;">
                                <?= ENVIRONMENT; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2 text-muted">Stripe API Node</td>
                            <td class="py-2 text-end">
                                <span class="status-indicator text-success">
                                    <span class="status-dot bg-success"></span> Functional
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>