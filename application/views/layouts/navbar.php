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

    <?php
$cart = $this->session->userdata('cart') ?? [];

$cartCount = count($cart);
?>

<li class="nav-item ">

    <a class="nav-link position-relative"
   href="<?= site_url('user/cart'); ?>">

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