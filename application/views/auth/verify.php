<div class="login-box">
    <div class="login-logo">
        <a href="<?= site_url(); ?>"><?= getenv('APP_NAME') ?: 'Payment Portal'; ?></a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Enter the verification code sent to <?= html_escape($user->email); ?></p>

            <?php $this->load->view('components/flash_message'); ?>

            <?= form_open('auth/verify/store', array('class' => 'js-verify-form')); ?>
                <input type="hidden" name="user_id" value="<?= (int)$user->id; ?>" />

                <div class="mb-3 js-field-group">
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control"
                            name="code"
                            placeholder="Verification code"
                        >
                        <div class="input-group-text">
                            <span class="fas fa-key"></span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Verify</button>
            <?= form_close(); ?>

            <div class="mt-3 text-center">
                <?= form_open('auth/verify/resend', ['class' => 'd-inline']); ?>
                    <input type="hidden" name="user_id" value="<?= (int)$user->id; ?>" />
                    <button type="submit" class="btn btn-link">Resend code</button>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
