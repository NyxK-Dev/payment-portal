<aside class="app-sidebar premium-sidebar shadow-sm">

    <!-- Sidebar Brand -->
    <div class="sidebar-brand-wrapper">
        <a href="<?= site_url('dashboard'); ?>" class="brand-logo-link">
            <div class="brand-icon-box">
                <i class="fas fa-wallet"></i>
            </div>
            <span class="brand-text-premium">
                Payment<span class="fw-bold text-primary">Portal</span>
            </span>
        </a>
    </div>

    <!-- Sidebar Scroll Container -->
    <div class="sidebar-wrapper">
        <nav class="mt-3">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= site_url('dashboard'); ?>" class="nav-link <?= (current_url() == site_url('dashboard')) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-th-large"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Administration -->
                <li class="nav-header-premium">Administration</li>
                <li class="nav-item <?= (strpos(current_url(), 'admin/users') !== false || strpos(current_url(), 'admin/roles') !== false || strpos(current_url(), 'admin/permissions') !== false || strpos(current_url(), 'admin/role_permissions') !== false || strpos(current_url(), 'admin/api_tokens') !== false) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>
                            Users Control
                            <i class="fas fa-angle-left menu-arrow"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= site_url('admin/users'); ?>" class="nav-link <?= (current_url() == site_url('admin/users')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Manage Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/roles'); ?>" class="nav-link <?= (current_url() == site_url('admin/roles')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/permissions'); ?>" class="nav-link <?= (current_url() == site_url('admin/permissions')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/role_permissions'); ?>" class="nav-link <?= (current_url() == site_url('admin/role_permissions')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Role Permissions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/api_tokens'); ?>" class="nav-link <?= (current_url() == site_url('admin/api_tokens')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>API Tokens</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Catalog & Sales -->
                <li class="nav-header-premium">Catalog & Sales</li>
                <li class="nav-item">
                    <a href="<?= site_url('admin/products'); ?>" class="nav-link <?= (current_url() == site_url('admin/products')) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-cubes"></i>
                        <p>Products</p>
                    </a>
                </li>
                <li class="nav-item <?= (strpos(current_url(), 'admin/orders') !== false || strpos(current_url(), 'admin/order_items') !== false) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-bag"></i>
                        <p>
                            Orders
                            <i class="fas fa-angle-left menu-arrow"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= site_url('admin/orders'); ?>" class="nav-link <?= (current_url() == site_url('admin/orders')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/order_items'); ?>" class="nav-link <?= (current_url() == site_url('admin/order_items')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Order Items</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Payments -->
                <li class="nav-header-premium">Payments</li>
                <li class="nav-item <?= (strpos(current_url(), 'admin/payments') !== false || strpos(current_url(), 'admin/payment_attempts') !== false || strpos(current_url(), 'admin/stripe_transactions') !== false || strpos(current_url(), 'admin/stripe_webhook_events') !== false || strpos(current_url(), 'admin/payment_events') !== false || strpos(current_url(), 'admin/idempotency_keys') !== false || strpos(current_url(), 'admin/refunds') !== false) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>
                            Transactions
                            <i class="fas fa-angle-left menu-arrow"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= site_url('admin/payments'); ?>" class="nav-link <?= (current_url() == site_url('admin/payments')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Payments Log</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/payment_attempts'); ?>" class="nav-link <?= (current_url() == site_url('admin/payment_attempts')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Payment Attempts</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/stripe_transactions'); ?>" class="nav-link <?= (current_url() == site_url('admin/stripe_transactions')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Stripe Transactions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/stripe_webhook_events'); ?>" class="nav-link <?= (current_url() == site_url('admin/stripe_webhook_events')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Stripe Webhooks</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/payment_events'); ?>" class="nav-link <?= (current_url() == site_url('admin/payment_events')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Payment Events</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/idempotency_keys'); ?>" class="nav-link <?= (current_url() == site_url('admin/idempotency_keys')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Idempotency Keys</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/refunds'); ?>" class="nav-link <?= (current_url() == site_url('admin/refunds')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Refunds</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Documents -->
                <li class="nav-header-premium">Documents</li>
                <li class="nav-item">
                    <a href="<?= site_url('admin/invoices'); ?>" class="nav-link <?= (current_url() == site_url('admin/invoices')) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>Invoices</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('admin/receipts'); ?>" class="nav-link <?= (current_url() == site_url('admin/receipts')) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Receipts</p>
                    </a>
                </li>

                <!-- System Logs & Configuration -->
                <li class="nav-header-premium">System</li>
                <li class="nav-item <?= (strpos(current_url(), 'admin/audit_logs') !== false || strpos(current_url(), 'admin/activity_logs') !== false || strpos(current_url(), 'admin/email_logs') !== false) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shield-alt"></i>
                        <p>
                            Logs
                            <i class="fas fa-angle-left menu-arrow"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= site_url('admin/audit_logs'); ?>" class="nav-link <?= (current_url() == site_url('admin/audit_logs')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Audit Logs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/activity_logs'); ?>" class="nav-link <?= (current_url() == site_url('admin/activity_logs')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Activity Logs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('admin/email_logs'); ?>" class="nav-link <?= (current_url() == site_url('admin/email_logs')) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Email Logs</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?= (strpos(current_url(), 'admin/lookupgroups') !== false || strpos(current_url(), 'admin/lookups') !== false) ? 'menu-open' : ''; ?>">

                    <a href="#" class="nav-link <?= (strpos(current_url(), 'admin/lookupgroups') !== false || strpos(current_url(), 'admin/lookups') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-search"></i>
                        <p>
                            Lookups
                            <i class="fas fa-angle-left menu-arrow"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!-- Lookup Groups Link -->
                        <li class="nav-item">
                            <a href="<?= site_url('admin/lookupgroups'); ?>"
                                class="nav-link <?= (strpos(current_url(), 'admin/lookupgroups') !== false) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Lookup Groups</p>
                            </a>
                        </li>

                        <!-- Lookups Link -->
                        <li class="nav-item">
                            <a href="<?= site_url('admin/lookups'); ?>"
                                class="nav-link <?= (strpos(current_url(), 'admin/lookups') !== false) ? 'active' : ''; ?>">
                                <i class="fas fa-minus sub-nav-icon"></i>
                                <p>Lookups Values</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= site_url('admin/settings'); ?>" class="nav-link <?= (current_url() == site_url('admin/settings')) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-sliders-h"></i>
                        <p>Settings</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>