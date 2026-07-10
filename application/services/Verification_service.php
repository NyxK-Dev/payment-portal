<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'repositories/VerificationRepository.php';

class Verification_service
{
    protected $CI;
    protected $repo;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('User_model');
        $this->CI->load->library('email');

        $this->repo = new VerificationRepository();
    }

    public function generateCode($userId, $ttlMinutes = 60)
    {
        $code = random_int(100000, 999999);
        $ttl = $ttlMinutes * 60;

        $this->repo->create([
            'user_id' => $userId,
            'code' => (string)$code,
            'ttl' => $ttl,
        ]);

        // Log generated code in non-production for debugging
        if (getenv('APP_ENV') !== 'production') {
            if (function_exists('log_message')) {
                log_message('debug', 'Verification code generated for user ' . $userId . ': ' . $code);
            } else {
                error_log('Verification code generated for user ' . $userId . ': ' . $code);
            }
        }

        return (string)$code;
    }

    public function resendCode($userId, $ttlMinutes = 60)
    {
        $cooldown = getenv('VERIF_RESEND_COOLDOWN') ? (int)getenv('VERIF_RESEND_COOLDOWN') : 60;
        $dailyLimit = getenv('VERIF_MAX_RESENDS_PER_DAY') ? (int)getenv('VERIF_MAX_RESENDS_PER_DAY') : 5;

        list($ok, $reason) = $this->repo->canResend($userId, $cooldown, $dailyLimit);
        if (! $ok) {
            return [false, $reason];
        }

        $code = $this->generateCode($userId, $ttlMinutes);

        // record resend
        $this->repo->recordResend($userId, $cooldown);

        // send email
        $user = $this->CI->User_model->findById($userId);
        if (! $user) return [false, 'no_user'];

        $from = getenv('SUPPORT_EMAIL') ?: 'support@example.com';

        $this->CI->email->from($from, getenv('APP_NAME') ?: 'Payment Portal');
        $this->CI->email->to($user->email);
        $this->CI->email->subject('Verify your email address');
        $body = $this->CI->load->view('emails/verification', ['code' => $code, 'user' => $user], true);
        $this->CI->email->message($body);
        $this->CI->email->set_mailtype('html');
        $sent = $this->CI->email->send();

        // Log email send result and debug output
        if (function_exists('log_message')) {
            log_message('debug', 'Verification email send for user ' . $userId . ': ' . var_export($sent, true));
            log_message('debug', 'Email debugger: ' . $this->CI->email->print_debugger(array('headers')));
        } else {
            error_log('Verification email send for user ' . $userId . ': ' . var_export($sent, true));
        }

        return [true, 'sent'];
    }

    public function recordAttempt($userId, $ip)
    {
        $maxAttempts = getenv('VERIF_MAX_ATTEMPTS') ? (int)getenv('VERIF_MAX_ATTEMPTS') : 5;
        $window = getenv('VERIF_ATTEMPT_WINDOW_MINUTES') ? (int)getenv('VERIF_ATTEMPT_WINDOW_MINUTES') * 60 : 3600;

        $count = $this->repo->incrementAttempt($userId, $ip, $window);
        if ($count === null) {
            // redis unavailable — fallback to allowing but log
            return [true, $maxAttempts - 0];
        }

        if ($count > $maxAttempts) {
            return [false, $maxAttempts - $count];
        }

        return [true, $maxAttempts - $count];
    }

    public function resetAttempts($userId, $ip)
    {
        return $this->repo->resetAttempts($userId, $ip);
    }

    public function verifyCode($userId, $code)
    {
        $record = $this->repo->findByCode($userId, $code);

        if (!$record) {
            return false;
        }

        // mark as verified in redis and activate user
        $this->repo->markVerified($userId);

        $activeLookup = $this->CI->db
            ->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'user_status')
            ->where('lookups.code', 'active')
            ->get()
            ->row();

        if ($activeLookup) {
            $this->CI->User_model->update($userId, ['status_lookup_id' => $activeLookup->id, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        return true;
    }
}
