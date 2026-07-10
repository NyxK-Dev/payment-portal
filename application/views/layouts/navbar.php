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

    <?php
$cart = $this->session->userdata('cart') ?? [];

                <span class="nav-link d-flex align-items-center text-truncate">

<li class="nav-item ">

                    <span class="d-none d-md-inline">
                        <?= html_escape($this->session->userdata('user_name')); ?>
                    </span>

    <i class="fas fa-shopping-cart"></i>

    <?php if ($cartCount > 0): ?>
        <span class="position-absolute badge rounded-pill bg-danger"
              style="
                  top: 2px;
                  right: -4px;
                  font-size: 0.6rem;
                  padding: 0.2em 0.4em;
              ">
            <?= $cartCount; ?>
        </span>
    <?php endif; ?>

</a>

</li>

    <li class="nav-item">

        <span class="nav-link">

                <a class="nav-link text-danger d-flex align-items-center"

            <?= html_escape($this->session->userdata('user_name')); ?>

            <span class="badge bg-primary ms-1">

                    <span class="ms-1 d-none d-sm-inline">Logout</span>

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