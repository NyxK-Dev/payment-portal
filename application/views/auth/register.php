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
                <a href="<?= site_url(); ?>" class="text-decoration-none fs-2 fw-bold text-white tracking-tight" style="text-shadow: 0 2px 4px rgba(0,0,0,0.15);">Payment Portal</a>
                <p class="text-white-50 small mt-1">Create your customer account</p>
            </div>

            <?php $this->load->view('components/flash_message'); ?>

            <?php
            $old_input = $this->session->flashdata('old_input') ?: array();
            $field_errors = $this->session->flashdata('field_errors') ?: array();
            ?>

            <?= form_open('register/store', array('id' => 'registerForm', 'class' => 'js-validate-form', 'novalidate' => 'novalidate')); ?>
                
                <div class="mb-3 js-field-group">
                    <div class="input-group<?= !empty($field_errors['name']) ? ' has-validation' : ''; ?> glass-input-group">
                        <span class="input-group-text bg-transparent text-white border-end-0 border-white-25">
                            <i class="fas fa-user"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control bg-transparent text-white ps-2 border-white-25 custom-placeholder <?= !empty($field_errors['name']) ? 'is-invalid' : ''; ?>"
                            name="name"
                            value="<?= html_escape(isset($old_input['name']) ? $old_input['name'] : ''); ?>"
                            placeholder="Name"
                        >
                    </div>
                    <?php $this->load->view('components/field_error', array('message' => isset($field_errors['name']) ? $field_errors['name'] : '')); ?>
                </div>

                <div class="mb-3 js-field-group">
                    <div class="input-group<?= !empty($field_errors['email']) ? ' has-validation' : ''; ?> glass-input-group">
                        <span class="input-group-text bg-transparent text-white border-end-0 border-white-25">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input
                            type="email"
                            class="form-control bg-transparent text-white ps-2 border-white-25 custom-placeholder <?= !empty($field_errors['email']) ? 'is-invalid' : ''; ?>"
                            name="email"
                            value="<?= html_escape(isset($old_input['email']) ? $old_input['email'] : ''); ?>"
                            placeholder="Email"
                        >
                    </div>
                    <?php $this->load->view('components/field_error', array('message' => isset($field_errors['email']) ? $field_errors['email'] : '')); ?>
                </div>

                <div class="mb-3 js-field-group">
                    <div class="input-group<?= !empty($field_errors['password']) ? ' has-validation' : ''; ?> glass-input-group">
                        <span class="input-group-text bg-transparent text-white border-end-0 border-white-25">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input
                            type="password"
                            id="password"
                            class="form-control bg-transparent text-white ps-2 border-end-0 border-white-25 custom-placeholder <?= !empty($field_errors['password']) ? 'is-invalid' : ''; ?>"
                            name="password"
                            placeholder="Password"
                        >
                        <span class="input-group-text bg-transparent text-white border-start-0 border-white-25 toggle-password" style="cursor: pointer;" onclick="togglePasswordVisibility('password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <?php $this->load->view('components/field_error', array('message' => isset($field_errors['password']) ? $field_errors['password'] : '')); ?>
                </div>

                <div class="mb-4 js-field-group">
                    <div class="input-group<?= !empty($field_errors['password_confirm']) ? ' has-validation' : ''; ?> glass-input-group">
                        <span class="input-group-text bg-transparent text-white border-end-0 border-white-25">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input
                            type="password"
                            id="password_confirm"
                            class="form-control bg-transparent text-white ps-2 border-end-0 border-white-25 custom-placeholder <?= !empty($field_errors['password_confirm']) ? 'is-invalid' : ''; ?>"
                            name="password_confirm"
                            placeholder="Confirm password"
                        >
                        <span class="input-group-text bg-transparent text-white border-start-0 border-white-25 toggle-password" style="cursor: pointer;" onclick="togglePasswordVisibility('password_confirm', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <?php $this->load->view('components/field_error', array('message' => isset($field_errors['password_confirm']) ? $field_errors['password_confirm'] : '')); ?>
                </div>
                
                <p class="text-center small text-white-50" style="font-size: 11px;">
                    This site is protected by **reCAPTCHA** and the Google 
                    <a href="https://policies.google.com/privacy" class="text-white">Privacy Policy</a> and 
                    <a href="https://policies.google.com/terms" class="text-white">Terms of Service</a> apply.
                </p>

                <button type="submit" class="btn btn-light w-100 py-2 fw-semibold shadow-sm text-primary mb-3">Create Account</button>
            <?= form_close(); ?>

            <?php $recaptchaKey = getenv('RECAPTCHA_SITE_KEY') ?: ''; ?>
            <?php if (!empty($recaptchaKey)): ?>
                <script src="https://www.google.com/recaptcha/api.js?render=<?= $recaptchaKey; ?>"></script>
                <script>
                    (function(){
                        var form = document.getElementById('registerForm');
                        if (!form) return;
                        form.addEventListener('submit', function(e){
                            e.preventDefault();
                            grecaptcha.ready(function(){
                                grecaptcha.execute('<?= $recaptchaKey; ?>', {action: 'register'}).then(function(token){
                                    var oldInput = form.querySelector('input[name="g-recaptcha-response"]');
                                    if(oldInput) oldInput.remove();

                                    var input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'g-recaptcha-response';
                                    input.value = token;
                                    form.appendChild(input);
                                    form.submit();
                                });
                            });
                        });
                    })();
                </script>
            <?php endif; ?>

            <div class="mt-3 text-center">
                <p class="mb-0 small text-white-50">Already have an account? <a href="<?= site_url('login'); ?>" class="text-white fw-semibold text-decoration-underline">Sign In</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling borders & interactions for inputs inside the Glass Design */
    .border-white-25 {
        border-color: rgba(255, 255, 255, 0.35) !important; /* Slightly increased visibility */
    }
    
    /* Input Form Enhancements */
    .glass-input-group .form-control {
        color: #ffffff !important; /* Forces typed text to be clean white */
        font-weight: 500;
    }
    
    .glass-input-group .form-control:focus {
        background-color: rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.7) !important;
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
    }
    
    /* Fixes input placeholder text visibility problem */
    .glass-input-group .form-control::placeholder,
    .custom-placeholder::placeholder {
        color: rgba(255, 255, 255, 0.75) !important; /* Made text brighter and readable */
        opacity: 1 !important;
    }
    
    /* Dynamic adjustments for standard bootstrap handling */
    .glass-input-group .form-control.is-invalid {
        border-color: #ff8080 !important;
        background-image: none !important; 
    }
</style>

<script>
function togglePasswordVisibility(fieldId, element) {
    var passwordField = document.getElementById(fieldId);
    var icon = element.querySelector('i');
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>