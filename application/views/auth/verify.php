<?php
$email = $user->email;

$parts = explode('@', $email);

$username = $parts[0];
$domain   = $parts[1];

$visible = substr($username, 0, 2);
$maskedEmail = $visible . str_repeat('*', max(strlen($username) - 2, 4)) . '@' . $domain;
?>
<div class="d-flex justify-content-center align-items-center min-vh-100 py-5" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: linear-gradient(135deg, #141e30 0%, #243b55 100%);
    z-index: 1000;
    overflow-y: auto;
">

    <div class="card w-100 border-0 m-3" style="
        max-width: 450px; 
        background: rgba(255, 255, 255, 0.15); 
        backdrop-filter: blur(12px); 
        -webkit-backdrop-filter: blur(12px); 
        border-radius: 16px; 
        border: 1px solid rgba(255, 255, 255, 0.25); 
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
    ">
        <div class="card-body p-4 p-md-5 text-white">

            <div class="text-center mb-4">
                <a href="<?= site_url(); ?>" class="text-decoration-none fs-2 fw-bold text-white tracking-tight" style="text-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                    <?= getenv('APP_NAME') ?: 'Payment Portal'; ?>
                </a>
                <p class="text-white-50 small mt-1">
                    Enter the verification code sent to
                    <strong><?= html_escape($maskedEmail); ?></strong>
                </p>
            </div>

            <?php $this->load->view('components/flash_message'); ?>

            <?= form_open('auth/verify/store', array('class' => 'js-verify-form')); ?>
            <input type="hidden" name="user_id" value="<?= (int)$user->id; ?>" />

            <div class="mb-4 js-field-group">
                <div class="input-group input-group-lg glass-input-group">
                    <span class="input-group-text bg-transparent text-white border-end-0 border-white-25">
                        <i class="fas fa-key"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control bg-transparent text-white ps-2 border-white-25 custom-placeholder py-2.5"
                        name="code"
                        placeholder="Verification code"
                        autocomplete="off">
                </div>
            </div>

            <button type="submit" class="btn btn-light w-100 py-2.5 fw-semibold shadow-sm text-primary mb-3">Verify</button>
            <?= form_close(); ?>

            <div class="mt-2 text-center">
                <?= form_open('auth/verify/resend', ['class' => 'd-inline']); ?>
                <input type="hidden" name="user_id" value="<?= (int)$user->id; ?>" />
                <button type="submit" class="btn btn-link text-white fw-semibold text-decoration-underline p-0 small bg-transparent border-0 opacity-75 hover-opacity-100">Resend code</button>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling borders & interactions for inputs inside the Glass Design */
    .border-white-25 {
        border-color: rgba(255, 255, 255, 0.25) !important;
    }

    .glass-input-group .form-control:focus {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        border-color: rgba(255, 255, 255, 0.6) !important;
        box-shadow: none;
    }

    /* Changes placeholder text color to accommodate dark glass contrast */
    .custom-placeholder::placeholder {
        color: rgba(255, 255, 255, 0.6) !important;
    }

    /* Dynamic adjustments for standard bootstrap handling */
    .glass-input-group .form-control.is-invalid {
        border-color: #ff8080 !important;
        background-image: none !important;
    }

    .hover-opacity-100:hover {
        opacity: 1 !important;
    }
</style>