<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends MY_Controller
{
    protected $authService;
    protected $recaptchaService;
    protected $verificationService;
    protected $emailLogService;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('email');
        $this->load->library('auth');
        $this->load->helper('form');
        $this->load->model('User_model');
        $this->load->file(APPPATH . 'services/Auth_Service.php', true);

        $this->authService = new Auth_service();
        // services
        require_once APPPATH . 'services/Recaptcha_service.php';
        require_once APPPATH . 'services/Verification_service.php';
        $this->recaptchaService = new Recaptcha_service();
        $this->verificationService = new Verification_service();

       require_once APPPATH . 'services/EmailLogService.php';
       $this->emailLogService = new EmailLogService();

        
    }

    public function index()
    {
        if ($this->auth->check()) {
            return $this->redirectByRole();
        }

        $this->render_auth('auth/register', array(
            'title' => 'Register',
        ));
    }

    public function store()
    {
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[150]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() === FALSE) {
            return $this->redirect_with_validation_errors('register');
        }

        $role = $this->User_model->getRoleByName('customer');

        if (!$role) {
            $this->session->set_flashdata('error', 'Customer role is missing. Please run the role migration/seed first.');
            return redirect('register');
        }

        $now = date('Y-m-d H:i:s');

        // set initial status to 'inactive' until email verified
        $statusLookup = $this->db
            ->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'user_status')
            ->where('lookups.code', 'inactive')
            ->get()
            ->row();

        $userId = $this->User_model->create(array(
            'role_id' => $role->id,
            'name' => $this->input->post('name', TRUE),
            'email' => $this->input->post('email', TRUE),
            'password' => $this->authService->hashPassword($this->input->post('password')),
            'status_lookup_id' => $statusLookup ? $statusLookup->id : null,
            'created_at' => $now,
            'updated_at' => $now,
        ));

        $user = $this->User_model->findById($userId);

        // verify reCAPTCHA if configured
        $recaptchaToken = $this->input->post('g-recaptcha-response', TRUE);
        if (! $this->recaptchaService->isConfigured()) {
            // allow bypass in non-production for developer convenience
            if (getenv('APP_ENV') === 'production') {
                $this->session->set_flashdata('error', 'reCAPTCHA is not configured. Please contact the administrator.');
                return redirect('register');
            }
        } else {
            if (! $this->recaptchaService->verify($recaptchaToken, $this->input->ip_address())) {
                $err = $this->recaptchaService->getLastError();
                $msg = 'reCAPTCHA verification failed.';
                if ($err === 'token_missing') {
                    $msg = 'reCAPTCHA token missing. Please enable JavaScript and try again.';
                } elseif ($err === 'secret_missing') {
                    $msg = 'reCAPTCHA secret is not configured on the server.';
                } elseif (!empty($err)) {
                    $msg = 'reCAPTCHA verification failed (' . html_escape($err) . ').';
                }

                $this->session->set_flashdata('error', $msg);
                return redirect('register');
            }
        }

        // generate verification code and email to user (use service which enforces resend limits)
        $code = $this->verificationService->generateCode($userId, getenv('VERIF_TTL_MINUTES') ? (int)getenv('VERIF_TTL_MINUTES') : 60);

        $from = getenv('SUPPORT_EMAIL') ?: 'support@example.com';

        $this->email->from($from, getenv('APP_NAME') ?: 'Payment Portal');
        $this->email->to($user->email);
        $this->email->subject('Verify your email address');
        $body = $this->load->view('emails/verification', ['code' => $code, 'user' => $user], true);
        $this->email->message($body);
        $this->email->set_mailtype('html');
        // $sent = $this->email->send();
        $sent = $this->emailLogService->sendHtmlEmail(
            $user->email,
            'Verify your email address',
            $body,
            $userId
        );

        // Log generated code and email send result in development
        if (getenv('APP_ENV') !== 'production') {
            if (function_exists('log_message')) {
                log_message('debug', 'Verification code generated for user ' . $userId . ': ' . $code);
                log_message('debug', 'Register email send result: ' . var_export($sent, true));
                log_message('debug', 'Email debugger: ' . $this->email->print_debugger(array('headers')));
            } else {
                error_log('Verification code generated for user ' . $userId . ': ' . $code);
            }
        }

        // redirect user to verification page
        $this->session->set_flashdata('info', 'A verification code has been sent to your email.');
        return redirect('auth/verify/index/' . $userId);
    }

    protected function redirectByRole()
    {
        if ($this->auth->isAdmin()) {
            return redirect('admin/users');
        }

        return redirect('user/products');
    }
}
