<aside class="app-sidebar bg-body-secondary shadow">
    <?php
    $controller = $this->router->fetch_class();
    ?>
    <div class="sidebar-brand">
        <a href="<?= site_url(); ?>" class="brand-link">
            <span class="brand-text fw-light">
                Payment Portal
            </span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav>
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                <li class="nav-item">
                    <a href="<?= site_url() ?>"
                        class="nav-link <?= strtolower($controller) === 'home' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-gauge"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('admin/products') ?>"
                        class="nav-link <?= strtolower($controller) === 'products' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Products</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

</aside>