<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'repositories/EmailLogRepository.php';
class EmailLogService
{
    protected $CI;
    protected $repository;


    public function __construct()
    {
        $this->CI =& get_instance();

        $this->repository =
            new EmailLogRepository();
        $this->CI->load->library('email');
        $this->CI->load->database();
    }

    /**
     * Sends an HTML email and automatically writes to the migration's email_logs table
     */
    public function sendHtmlEmail($to, $subject, $body, $userId = NULL) 
    {
        $fromEmail = getenv('SUPPORT_EMAIL') ?: 'support@example.com';
        $appName = getenv('APP_NAME') ?: 'Payment Portal';

        // 1. Configure the core CI Email library
        $this->CI->email->from($fromEmail, $appName);
        $this->CI->email->to($to);
        $this->CI->email->subject($subject);
        $this->CI->email->message($body);
        $this->CI->email->set_mailtype('html');

        // 2. Execute the send engine
        $sent = $this->CI->email->send();

        // 3. Map out log payload according to your email_logs schema
        $logData = [
            'user_id'          => $userId, // Dynamically tracks the registered user
            'email_to'         => $to,
            'subject'          => $subject,
            'status_lookup_id' => $sent ? 1 : 0, // Assumes 1 = Sent/Success, 0 = Pending/Failed in your lookups
            'response'         => $sent ? 'Message queued successfully' : $this->CI->email->print_debugger(['headers']),
            'sent_at'          => date('Y-m-d H:i:s')
        ];

        // 4. Record into your database table
        $this->CI->db->insert('email_logs', $logData);

        return $sent;
    }
    public function getLogs($filters=[])
    {
        return $this->repository
            ->all($filters);
    }
}