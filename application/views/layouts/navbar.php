<nav class="app-header navbar navbar-expand bg-white border-bottom px-2 px-sm-3" style="min-height: 64px;">
    <div class="container-fluid d-flex align-items-center justify-content-between px-0">
        
        <!-- Left Side: Toggle & Title -->
        <div class="d-flex align-items-center gap-1 gap-sm-2">
            <a class="nav-link btn btn-light border-0 d-flex align-items-center justify-content-center" 
               data-lte-toggle="sidebar" 
               href="#" 
               style="width: 38px; height: 38px; border-radius: 8px; color: #475569;">
                <i class="fas fa-bars"></i>
            </a>
            <span class="fw-bold text-dark px-2" style="font-size: 1rem; letter-spacing: -0.01em;">
                Payment Portal
            </span>
        </div>

        <!-- Right Side: Navigation Utilities -->
        <ul class="navbar-nav align-items-center gap-2 gap-sm-3 ms-auto">
            
            <?php 
            // Get current user role
            $userRole = strtolower($this->session->userdata('role_name') ?? '');
            
            // Only render shopping cart container if the user is NOT an admin
            if ($userRole !== 'admin'): 
                $cart = $this->session->userdata('cart') ?? [];
                $cartCount = count($cart);
            ?>
                <!-- Cart Trigger Component -->
                <li class="nav-item">
                    <a class="nav-link btn btn-light border-0 d-flex align-items-center justify-content-center position-relative" 
                       href="<?= site_url('user/cart'); ?>"
                       style="width: 38px; height: 38px; border-radius: 8px; color: #475569; background-color: #f8fafc;">
                        <i class="fas fa-shopping-cart" style="font-size: 0.95rem;"></i>
                        <?php if ($cartCount > 0): ?>
                            <span class="position-absolute translate-middle badge rounded-circle bg-danger border border-white"
                                  style="top: 8px; right: -2px; font-size: 0.6rem; padding: 0.35em 0.5em; min-width: 17px; height: 17px;">
                                <?= $cartCount; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>

            <!-- User Info Display Card Component -->
            <li class="nav-item">
                <div class="d-flex align-items-center gap-2 px-2 py-1 bg-light rounded-3" style="border: 1px solid #e2e8f0; height: 38px;">
                    <div class="d-flex align-items-center justify-content-center bg-white text-secondary rounded-circle shadow-sm d-none d-sm-flex" style="width: 26px; height: 26px;">
                        <i class="fas fa-user" style="font-size: 0.8rem;"></i>
                    </div>
                    <span class="fw-semibold text-dark text-truncate style-user-name" style="font-size: 0.85rem; max-width: 90px;">
                        <?= html_escape($this->session->userdata('user_name')); ?>
                    </span>
                    <span class="badge text-uppercase px-2 py-1 style-role-badge" style="font-size: 0.6rem; font-weight: 700; border-radius: 4px;">
                        <?= html_escape($this->session->userdata('role_name')); ?>
                    </span>
                </div>
            </li>

            <!-- Subtle Responsive Separator Divider -->
            <div class="vr bg-secondary opacity-25" style="height: 20px;"></div>

            <!-- Action: Logout Icon Link Component -->
            <li class="nav-item">
                <a class="nav-link btn btn-light border-0 d-flex align-items-center justify-content-center text-danger hover-danger" 
                   href="<?= site_url('logout'); ?>"
                   title="Logout"
                   style="width: 38px; height: 38px; border-radius: 8px; transition: all 0.15s ease;">
                    <i class="fas fa-sign-out-alt" style="font-size: 0.95rem;"></i>
                </a>
            </li>
        </ul>

    </div>
</nav>

<style>
/* Base Theme Dynamic Overrides */
.app-header .btn-light:hover {
    background-color: #f1f5f9 !important;
    color: #0f172a !important;
}
.app-header .hover-danger:hover {
    background-color: #fef2f2 !important;
    color: #dc2626 !important;
}

/* Default Color Scheme Strategy */
.style-role-badge {
    background-color: #2563eb !important; /* Blue for regular roles */
    color: #ffffff;
}

/* Responsive CSS Query Rules for Breakpoints */
@media (max-width: 576px) {
    .style-user-name {
        max-width: 65px !important;
        font-size: 0.8rem !important;
    }
    .style-role-badge {
        font-size: 0.55rem !important;
        padding: 0.2em 0.4em !important;
    }
}
</style>