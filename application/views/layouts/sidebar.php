<?php
$CI =& get_instance();
$CI->load->library('auth');

$isAdmin    = $CI->auth->isAdmin();
$isCustomer = $CI->auth->isCustomer();

// Small helper so every link (admin + customer) gets a consistent active/current-section state.
if (!function_exists('cx_is_active')) {
    function cx_is_active($needle)
    {
        return (strpos(current_url(), $needle) !== false);
    }
}

/**
 * The "Administration" group is only ever rendered inside the $isAdmin
 * block, so gating its individual items behind granular permissions
 * (manage_roles, manage_permissions, ...) just hides them from admins
 * whose role wasn't given every one of those grants. Set this to false
 * if you introduce a limited-admin role and want per-item checks back.
 */
$ADMIN_SEES_FULL_ADMINISTRATION = true;

/**
 * Each admin group is declared once here: [key, label, icon, items[]]
 * item = [permission|null, url segment, icon, label, iconSet?]
 * A group auto-expands on load if one of its links is the active page.
 */
$adminGroups = [
    [
        'key'   => 'administration',
        'label' => 'Administration',
        'icon'  => 'fa-user-shield',
        'items' => [
            ['manage_users',            'admin/users',            'fa-users',        'Manage Users'],
            ['manage_roles',            'admin/roles',            'fa-user-tag',     'Roles'],
            ['manage_permissions',      'admin/permissions',      'fa-key',          'Permissions'],
            ['manage_role_permissions', 'admin/role_permissions', 'fa-user-lock',    'Role Permissions'],
            ['manage_api_tokens',       'admin/api_tokens',       'fa-code',         'API Tokens'],
        ],
    ],
    [
        'key'   => 'catalog',
        'label' => 'Catalog & Sales',
        'icon'  => 'fa-store',
        'items' => [
            ['manage_products', 'admin/products',    'fa-boxes',         'Products'],
            ['manage_orders',   'admin/orders',       'fa-shopping-cart', 'Orders'],
            // ['manage_orders',   'admin/order_items',  'fa-list',          'Order Items'],
        ],
    ],
    [
        'key'   => 'payments',
        'label' => 'Payments',
        'icon'  => 'fa-wallet',
        'items' => [
            ['manage_payments', 'admin/payments',              'fa-credit-card',   'Payments'],
            ['manage_payments', 'admin/payment_attempts',      'fa-history',       'Payment Attempts'],
            ['manage_payments', 'admin/stripe_transactions',   'fa-stripe',        'Stripe Transactions', 'fab'],
            ['manage_payments', 'admin/stripe_webhook_events', 'fa-bolt',          'Stripe Webhooks'],
            ['manage_payments', 'admin/payment_events',        'fa-exchange-alt',  'Payment Events'],
            ['manage_payments', 'admin/idempotency_keys',      'fa-fingerprint',   'Idempotency Keys'],
            ['manage_payments', 'admin/refunds',                'fa-undo',         'Refunds'],
        ],
    ],
    [
        'key'   => 'documents',
        'label' => 'Documents',
        'icon'  => 'fa-file-invoice',
        'items' => [
            [null, 'admin/invoices', 'fa-file-invoice', 'Invoices'],
            [null, 'admin/receipts', 'fa-receipt',      'Receipts'],
        ],
    ],
    [
        'key'   => 'system',
        'label' => 'System',
        'icon'  => 'fa-shield-alt',
        'items' => [
            [null, 'admin/auditlogs',    'fa-shield-alt', 'Audit Logs'],
            [null, 'admin/activitylogs', 'fa-history',    'Activity Logs'],
            [null, 'admin/emaillogs',    'fa-envelope',   'Email Logs'],
        ],
    ],
    [
        'key'   => 'configuration',
        'label' => 'Configuration',
        'icon'  => 'fa-cog',
        'items' => [
            [null, 'admin/lookupgroups', 'fa-layer-group', 'Lookup Groups'],
            [null, 'admin/lookups',      'fa-list-alt',    'Lookup Values'],
            [null, 'admin/settings',     'fa-cog',         'Settings'],
        ],
    ],
];

$customerGroups = [
    [
        'key'   => 'shopping',
        'label' => 'Shopping',
        'icon'  => 'fa-store',
        'items' => [
            ['view_products',     'user/products', 'fa-store',         'Products'],
            ['purchase_products', 'user/cart',      'fa-shopping-cart', 'My Cart'],
            ['purchase_products', 'orders/history', 'fa-shopping-bag', 'My Orders'],
            [null, 'user/products', 'fa-store',         'Products'],
            [null, 'user/cart',     'fa-shopping-cart', 'My Cart'],
            [null, 'user/orders/history',    'fa-shopping-bag',  'My Orders'],
        ],
    ],
    [
        'key'   => 'documents',
        'label' => 'Documents',
        'icon'  => 'fa-file-invoice',
        'items' => [
            [null, 'user/invoices', 'fa-file-invoice', 'My Invoices'],
            [null, 'user/receipts', 'fa-receipt',      'My Receipts'],
        ],
    ],
];

// Decide which groups should render open on load (the one containing the active page).
function cx_group_has_active($group)
{
    foreach ($group['items'] as $item) {
        if (cx_is_active($item[1])) return true;
    }
    return false;
}
?>

<aside class="app-sidebar cx-sidebar shadow-sm" data-bs-theme="dark" id="cxSidebar">

    <div class="sidebar-brand cx-brand">
        <a href="<?= site_url('dashboard'); ?>" class="brand-link cx-brand__link">
            <span class="cx-brand__mark"><i class="fas fa-wallet"></i></span>
            <span class="cx-brand__text">Payment<b>Portal</b></span>
        </a>
    </div>

    <div class="sidebar-wrapper cx-scroll">
        <nav class="cx-nav">

            <a href="<?= site_url('dashboard'); ?>"
               class="cx-link cx-link--pinned <?= cx_is_active('dashboard') ? 'is-active' : ''; ?>">
                <span class="cx-link__icon"><i class="fas fa-house"></i></span>
                <span class="cx-link__label">Dashboard</span>
            </a>

            <?php if ($isAdmin): ?>

                <?php foreach ($adminGroups as $group):
                    $groupItems = array_filter($group['items'], function ($item) use ($CI, $group, $ADMIN_SEES_FULL_ADMINISTRATION) {
                        if ($group['key'] === 'administration' && $ADMIN_SEES_FULL_ADMINISTRATION) return true;
                        return $item[0] === null || $CI->auth->can($item[0]);
                    });
                    if (empty($groupItems)) continue;
                    $open = cx_group_has_active($group);
                ?>
                <div class="cx-section <?= $open ? 'is-open' : ''; ?>" data-cx-section>
                    <button type="button" class="cx-section__toggle" data-cx-toggle aria-expanded="<?= $open ? 'true' : 'false'; ?>">
                        <span class="cx-section__icon"><i class="fas <?= $group['icon']; ?>"></i></span>
                        <span class="cx-section__label"><?= $group['label']; ?></span>
                        <i class="fas fa-chevron-right cx-section__chevron"></i>
                    </button>
                    <div class="cx-section__panel">
                        <div class="cx-section__inner">
                            <?php foreach ($groupItems as $item):
                                $perm    = $item[0];
                                $url     = $item[1];
                                $icon    = $item[2];
                                $label   = $item[3];
                                $iconSet = $item[4] ?? 'fas';
                                $active  = cx_is_active($url);
                            ?>
                            <a href="<?= site_url($url); ?>" class="cx-link <?= $active ? 'is-active' : ''; ?>">
                                <span class="cx-link__icon"><i class="<?= $iconSet; ?> <?= $icon; ?>"></i></span>
                                <span class="cx-link__label"><?= $label; ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php endif; ?>

            <?php if ($isCustomer): ?>

                <?php foreach ($customerGroups as $group):
                    $groupItems = array_filter($group['items'], function ($item) use ($CI) {
                        return $item[0] === null || $CI->auth->can($item[0]);
                    });
                    if (empty($groupItems)) continue;
                    $open = cx_group_has_active($group);
                ?>
                <div class="cx-section <?= $open ? 'is-open' : ''; ?>" data-cx-section>
                    <button type="button" class="cx-section__toggle" data-cx-toggle aria-expanded="<?= $open ? 'true' : 'false'; ?>">
                        <span class="cx-section__icon"><i class="fas <?= $group['icon']; ?>"></i></span>
                        <span class="cx-section__label"><?= $group['label']; ?></span>
                        <i class="fas fa-chevron-right cx-section__chevron"></i>
                    </button>
                    <div class="cx-section__panel">
                        <div class="cx-section__inner">
                            <?php foreach ($groupItems as $item):
                                $perm   = $item[0];
                                $url    = $item[1];
                                $icon   = $item[2];
                                $label  = $item[3];
                                $active = cx_is_active($url);
                            ?>
                            <a href="<?= site_url($url); ?>" class="cx-link <?= $active ? 'is-active' : ''; ?>">
                                <span class="cx-link__icon"><i class="fas <?= $icon; ?>"></i></span>
                                <span class="cx-link__label"><?= $label; ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="cx-divider"></div>

           

                <a href="<?= site_url('profile'); ?>" class="cx-link <?= cx_is_active('profile') ? 'is-active' : ''; ?>">
                    <span class="cx-link__icon"><i class="fas fa-user-circle"></i></span>
                    <span class="cx-link__label">My Profile</span>
                </a>


            <?php endif; ?>

        </nav>
    </div>
</aside>

<style>
/* =========================================================================
   PaymentPortal — Sidebar v3
   Palette: neutral charcoal surface, single restrained emerald accent
   (the "accent rail") that marks the active page.
   ========================================================================= */
:root {
    --cx-bg:        #16181D;
    --cx-surface:   #1E2126;
    --cx-surface-2: #262A31;
    --cx-border:    #2E323A;
    --cx-text:      #EDEEF0;
    --cx-text-dim:  #9498A0;
    --cx-text-faint:#5E6169;
    --cx-accent:      #34D399;
    --cx-accent-dark: #059669;
    --cx-accent-soft: rgba(52, 211, 153, 0.14);
    --cx-danger:    #F2665C;
    --cx-radius:    12px;
    --cx-ease:      cubic-bezier(0.22, 1, 0.36, 1);
}

/* Skin only — width, height and positioning stay owned by AdminLTE's
   .app-sidebar grid area. Never hardcode those here or the layout grid
   (.app-wrapper) breaks. */
.app-sidebar.cx-sidebar {
    background: var(--cx-bg) !important;
    border-inline-end: 1px solid var(--cx-border);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Brand -------------------------------------------------------------- */
.cx-brand {
    height: 70px;
    display: flex;
    align-items: center;
    padding: 0 22px;
    border-bottom: 1px solid var(--cx-border);
    flex-shrink: 0;
}

.cx-brand__link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none !important;
}

.cx-brand__mark {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--cx-accent), var(--cx-accent-dark));
    color: #0B1210;
    font-size: 1rem;
    box-shadow: 0 4px 14px rgba(52, 211, 153, 0.28);
}

.cx-brand__text {
    font-size: 1.08rem;
    font-weight: 500;
    letter-spacing: -0.2px;
    color: var(--cx-text);
}
.cx-brand__text b { font-weight: 700; color: var(--cx-accent); }

/* Scroll area ---------------------------------------------------------- */
.cx-scroll {
    padding: 16px 12px 24px;
    scrollbar-width: thin;
    scrollbar-color: var(--cx-border) transparent;
}
.cx-scroll::-webkit-scrollbar { width: 6px; }
.cx-scroll::-webkit-scrollbar-thumb { background: var(--cx-border); border-radius: 8px; }

.cx-nav { display: flex; flex-direction: column; gap: 3px; }

.cx-divider {
    height: 1px;
    background: var(--cx-border);
    margin: 12px 6px;
}

/* Plain links (Dashboard, Profile, Logout, group items) ---------------- */
.cx-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    color: var(--cx-text-dim);
    text-decoration: none !important;
    font-size: 0.9rem;
    font-weight: 500;
    transition: background-color .18s var(--cx-ease), color .18s var(--cx-ease), padding-left .18s var(--cx-ease);
}

.cx-link__icon {
    width: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    color: var(--cx-text-faint);
    transition: color .18s var(--cx-ease), transform .18s var(--cx-ease);
}

.cx-link:hover {
    background: var(--cx-surface-2);
    color: var(--cx-text);
    padding-left: 16px;
}
.cx-link:hover .cx-link__icon { color: var(--cx-accent); transform: translateY(-1px); }

.cx-link.is-active {
    background: var(--cx-accent-soft);
    color: var(--cx-accent);
}
.cx-link.is-active .cx-link__icon { color: var(--cx-accent); }

/* the "accent rail" — signature active marker */
.cx-link.is-active::before {
    content: '';
    position: absolute;
    left: -12px;
    top: 8px;
    bottom: 8px;
    width: 3px;
    border-radius: 0 4px 4px 0;
    background: var(--cx-accent);
    box-shadow: 0 0 10px rgba(52, 211, 153, 0.5);
}

.cx-link--pinned { margin-bottom: 10px; font-size: 0.92rem; }

.cx-link--danger { color: #C9788F; margin-top: 2px; }
.cx-link--danger .cx-link__icon { color: #C9788F; }
.cx-link--danger:hover { background: rgba(242, 102, 92, 0.1); color: var(--cx-danger); }
.cx-link--danger:hover .cx-link__icon { color: var(--cx-danger); }

/* Collapsible sections --------------------------------------------------- */
.cx-section { margin-top: 6px; }

.cx-section__toggle {
    all: unset;
    box-sizing: border-box;
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    cursor: pointer;
    color: var(--cx-text);
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    transition: background-color .18s var(--cx-ease), color .18s var(--cx-ease);
}
.cx-section__toggle:hover { background: var(--cx-surface); color: var(--cx-accent); }
.cx-section__toggle:focus-visible { outline: 2px solid var(--cx-accent); outline-offset: 2px; }

.cx-section__icon {
    width: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    color: var(--cx-text-dim);
}

.cx-section__label { flex: 1; text-align: left; }

.cx-section__chevron {
    font-size: 0.7rem;
    color: var(--cx-text-dim);
    transition: transform .28s var(--cx-ease);
}
.cx-section.is-open .cx-section__chevron { transform: rotate(90deg); color: var(--cx-accent); }
.cx-section.is-open > .cx-section__toggle { color: var(--cx-text); }

/* Height-animated panel using the grid-rows trick — no JS measuring needed */
.cx-section__panel {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows .32s var(--cx-ease);
}
.cx-section.is-open .cx-section__panel { grid-template-rows: 1fr; }

.cx-section__inner {
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 4px 0 6px 14px;
    border-left: 1px solid var(--cx-border);
    margin-left: 20px;
}
.cx-section__inner .cx-link { font-size: 0.86rem; padding: 8px 10px; }
.cx-section__inner .cx-link.is-active::before { left: -35px; }

@media (prefers-reduced-motion: reduce) {
    .cx-section__panel, .cx-section__chevron, .cx-link { transition: none !important; }
}
</style>

<script>
(function () {
    document.querySelectorAll('[data-cx-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var section = btn.closest('[data-cx-section]');
            var isOpen = section.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });
})();
</script>