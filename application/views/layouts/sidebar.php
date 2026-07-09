<aside class="app-sidebar bg-body-secondary shadow">

    <div class="sidebar-brand">
        <a href="<?= site_url(); ?>" class="brand-link">
            <span class="brand-text fw-light">
                Payment Portal
            </span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav>
            <ul class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="menu">
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-gauge"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
<!-- Roles -->
                  <li class="nav-item">

                    <a href="<?= site_url('admin/roles'); ?>"
                       class="nav-link">

                        <i class="nav-icon fas fa-user-shield"></i>

                        <p>
                            Roles
                        </p>

                    </a>

                </li>

                <!-- Permission -->
                  <li class="nav-item">

                    <a href="<?= site_url('admin/permissions'); ?>"
                       class="nav-link">

                        <i class="nav-icon fas fa-user-shield"></i>

                        <p>
                            Permission
                        </p>

                    </a>

                </li>

            </ul>
        </nav>
    </div>

</aside>