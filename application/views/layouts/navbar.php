<nav class="app-header navbar navbar-expand-lg bg-body border-bottom">

    <div class="container-fluid">

        <ul class="navbar-nav flex-row align-items-center">

            <li class="nav-item">

                <a class="nav-link d-flex align-items-center" data-lte-toggle="sidebar" href="#" aria-label="Toggle sidebar">

                    <i class="fas fa-bars"></i>

                </a>

            </li>

            <li class="nav-item">

                <span class="nav-link fw-semibold d-none d-sm-inline">

                    Payment Portal

                </span>

                <span class="nav-link fw-semibold d-sm-none">

                    PP

                </span>

            </li>

        </ul>

        <ul class="navbar-nav ms-auto flex-wrap align-items-center justify-content-end gap-2">

            <li class="nav-item">

                <span class="nav-link d-flex align-items-center text-truncate">

                    <i class="fas fa-user-circle me-1"></i>

                    <span class="d-none d-md-inline">
                        <?= html_escape($this->session->userdata('user_name')); ?>
                    </span>

                    <span class="badge bg-primary ms-1">

                        <?= html_escape($this->session->userdata('role_name')); ?>

                    </span>

                </span>

            </li>

            <li class="nav-item">

                <a class="nav-link text-danger d-flex align-items-center"

                    href="<?= site_url('logout'); ?>">

                    <i class="fas fa-sign-out-alt"></i>

                    <span class="ms-1 d-none d-sm-inline">Logout</span>

                </a>

            </li>

        </ul>

    </div>

</nav>