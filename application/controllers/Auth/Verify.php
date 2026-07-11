<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verify extends MY_Controller
{
    protected $verificationService;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('auth');
        $this->load->repository('UserRepository');

        require_once APPPATH . 'services/Verification_service.php';
        $this->verificationService = new Verification_service();
    }

    public function index($userId = null)
    {
        if (empty($userId)) {
            show_404();
            return;
        }

        $user = $this->userrepository->findById($userId);
        if (! $user) {
            show_404();
            return;
        }

        $this->render_auth('auth/verify', ['user' => $user]);
    }

    public function store()
    {
        $userId = $this->input->post('user_id', TRUE);
        $code = $this->input->post('code', TRUE);

        if (empty($userId) || empty($code)) {
            $this->session->set_flashdata('error', 'Invalid verification data.');
            return redirect('register');
        }

        // record attempt and enforce limits
        list($allowed, $remaining) = $this->verificationService->recordAttempt($userId, $this->input->ip_address());
        if (! $allowed) {
            $this->session->set_flashdata('error', 'Too many verification attempts. Try again later.');
            return redirect('auth/verify/index/' . $userId);
        }

        $ok = $this->verificationService->verifyCode($userId, $code);

        if (! $ok) {
            $this->session->set_flashdata('error', 'Invalid or expired verification code.');
            return redirect('auth/verify/index/' . $userId);
        }

        // reset attempts on success
        $this->verificationService->resetAttempts($userId, $this->input->ip_address());

        // log the user in
        $user = $this->userrepository->findById($userId);
        if ($user) {
            $this->auth->login($user);
        }

        $this->session->set_flashdata('success', 'Email verified successfully.');
        return redirect('user/products');
    }

    public function resend($userId = null)
    {
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }

        $userId = $this->input->post('user_id', TRUE) ?: $userId;
        if (empty($userId)) {
            show_404();
            return;
        }

        $result = $this->verificationService->resendCode($userId, getenv('VERIF_TTL_MINUTES') ? (int)getenv('VERIF_TTL_MINUTES') : 60);

        if (! $result[0]) {
            $reason = $result[1];
            if ($reason === 'cooldown') {
                $this->session->set_flashdata('error', 'Please wait before requesting another code.');
            } elseif ($reason === 'daily_limit') {
                $this->session->set_flashdata('error', 'You have reached the maximum number of resends for today.');
            } else {
                $this->session->set_flashdata('error', 'Unable to send verification code.');
            }
            return redirect('auth/verify/index/' . $userId);
        }

        $this->session->set_flashdata('success', 'A new verification code has been sent to your email.');
        return redirect('auth/verify/index/' . $userId);
    }
}
