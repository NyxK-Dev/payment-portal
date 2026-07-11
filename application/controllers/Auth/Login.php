<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller
{
    protected $authService;
    protected $recaptchaService;


    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('auth');
        $this->load->library('RequestValidator');

        $this->load->helper('form');


        require_once APPPATH.'services/Auth_Service.php';
        require_once APPPATH.'services/Recaptcha_service.php';


        $this->authService = new Auth_service();

        $this->recaptchaService = new Recaptcha_service();
    }



    public function index()
    {
        if($this->auth->check())
        {
            return $this->redirect_by_role();
        }


        $this->render_auth(
            'auth/login',
            [
                'title'=>'Login',
                'recaptchaSiteKey'=>getenv('RECAPTCHA_SITE_KEY')
            ]
        );
    }



    public function authenticate()
    {
        // echo "<pre>";
        // print_r($this->input->post());
        // echo "</pre>";
        // die();

        if(!$this->requestvalidator->validate(
            'Login',
            'authenticate'
        ))
        {
            return $this->redirect_with_validation_errors('login');
        }



        if(
            !$this->recaptchaService->verify(
                $this->input->post('g-recaptcha-response'),
                $this->input->ip_address()
            )
        )
        {
            $this->session->set_flashdata(
                'error',
                'Captcha verification failed.'
            );

            return redirect('login');
        }



        $result=$this->authService->attempt(
            $this->input->post('email',TRUE),
            $this->input->post('password')
        );



        if(!$result['success'])
        {
            $this->session->set_flashdata(
                'error',
                $result['message']
            );

            return redirect('login');
        }



        return $this->redirect_by_role();
    }



    protected function redirect_by_role()
    {

        if($this->auth->isAdmin())
        {
            return redirect('admin/users');
        }


        return redirect('user/products');
    }



    public function logout()
    {
        $this->authService->logout();

        return redirect('login');
    }
}