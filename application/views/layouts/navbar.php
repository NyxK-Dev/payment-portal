<nav class="app-header navbar navbar-expand bg-body">

    <div class="container-fluid">

        <ul class="navbar-nav">

            <li class="nav-item">

                <a class="nav-link" data-lte-toggle="sidebar" href="#">

                    <i class="fas fa-bars"></i>

                </a>

            </li>

            <li class="nav-item">

                <span class="nav-link fw-semibold">

                    Payment Portal

                </span>

            </li>

        </ul>

        <ul class="navbar-nav ms-auto">

            <li class="nav-item">

                <span class="nav-link">

                    <i class="fas fa-user-circle me-1"></i>

                    <?= html_escape($this->session->userdata('user_name')); ?>

                    <span class="badge bg-primary ms-1">

                        <?= html_escape($this->session->userdata('role_name')); ?>

                    </span>

                </span>

            </li>

            <li class="nav-item">

                <a class="nav-link text-danger"

                    href="<?= site_url('logout'); ?>">

                    <i class="fas fa-sign-out-alt"></i>

                    Logout

                </a>

            </li>

        </ul>

    </div>

</nav>