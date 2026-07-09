<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->load->view('layouts/header', [
    'title' => $title ?? 'Payment Portal'
]);
?>

<!-- Navbar Header Integration -->
<?php $this->load->view('layouts/navbar'); ?>

<!-- Control Sidebar Navigation Component -->
<?php $this->load->view('layouts/sidebar'); ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center py-2">
                <div class="col-sm-6">
                    <h2 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.5px;">
                        <?= $title ?? 'Dashboard'; ?>
                    </h2>
                </div>
                <div class="col-sm-6 d-flex justify-content-sm-end mt-2 mt-sm-0">
                    <?php $this->load->view('components/breadcrumb'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <?php $this->load->view('components/flash_message'); ?>
            <?php $this->load->view('components/alerts'); ?>

            <?php
            if (!empty($content)) {
                $this->load->view($content, $data ?? []);
            }
            ?>
        </div>
    </div>
</main>

<?php $this->load->view('layouts/footer'); ?>
<!-- Dynamic JavaScript Core Engines Hooked Below Footer to prevent UI interaction locks -->
<?php $this->load->view('layouts/scripts'); ?>