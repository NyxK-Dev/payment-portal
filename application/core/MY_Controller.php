<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('auth');
        $this->load->model('Permission_model');


        /*
        |--------------------------------------------------------------------------
        | CHANGE START
        |
        | Automatic RBAC permission detection
        |
        | Admin:
        | admin/invoices
        |        |
        |        ↓
        | manage_invoices
        |
        | Customer:
        | user/invoices
        |        |
        |        ↓
        | view_own_invoices
        |
        |--------------------------------------------------------------------------
        */


        $directory = strtolower(
            $this->router->fetch_directory()
        );


        $controller = strtolower(
            $this->router->fetch_class()
        );


        /*
        |--------------------------------------------------------------------------
        | Admin Permission Check
        |--------------------------------------------------------------------------
        */

        if ($directory === 'admin') {


            $permission = 'manage_' . $controller;


            if ($this->Permission_model->exists($permission)) {


                $this->auth->deny($permission);
            }
        }



        /*
        |--------------------------------------------------------------------------
        | Customer/User Permission Check
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | user/invoices
        |
        | becomes:
        |
        | view_own_invoices
        |
        */

        if ($directory === 'user') {


            $permission =
                'view_own_' . $controller;


            if ($this->Permission_model->exists($permission)) {


                $this->auth->deny($permission);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CHANGE END
        |--------------------------------------------------------------------------
        */
    }



    protected function render($content, $data = array())
    {
        $data['content'] = $content;

        $this->load->view(
            'layouts/app_layout',
            $data
        );
    }

    protected function render_auth($content, $data = array())
    {
        $data['content'] = $content;

        $this->load->view(
            'layouts/auth_layout',
            $data
        );
    }



    /**
     * Ensure logged in user
     */
    protected function require_auth()
    {
        $this->load->library('auth');


        if (!$this->auth->check()) {


            $this->session->set_flashdata(
                'error',
                'Please log in first.'
            );


            redirect('login');
        }
    }



    /**
     * Ensure admin role
     */
    protected function require_admin()
    {
        $this->require_auth();


        if (!$this->auth->isAdmin()) {


            $this->session->set_flashdata(
                'error',
                'You do not have permission to access admin panel.'
            );


            redirect('user/products');
        }
    }



    /**
     * Ensure customer role
     */
    protected function require_customer()
    {
        $this->require_auth();


        if (!$this->auth->isCustomer()) {


            $this->session->set_flashdata(
                'error',
                'You do not have permission to access this page.'
            );


            redirect('admin/users');
        }
    }



    /**
     * Resource ownership check
     *
     * Admin:
     *      allowed
     *
     * Customer:
     *      only own records
     */
    protected function require_owner($resourceUserId)
    {

        $this->load->library('auth');


        /*
        |--------------------------------------------------------------------------
        | Admin bypass
        |--------------------------------------------------------------------------
        */

        if ($this->auth->isAdmin()) {

            return;
        }



        /*
        |--------------------------------------------------------------------------
        | Customer ownership check
        |--------------------------------------------------------------------------
        */

        if (
            (int)$resourceUserId !==
            (int)$this->auth->id()
        ) {

            $this->deny_resource_access();
        }
    }



    protected function deny_resource_access()
    {
        show_404();
    }



    /**
     * Redirect after login
     */
    protected function redirect_by_role()
    {

        $this->load->library('auth');


        if (!$this->auth->check()) {

            redirect('login');
        }



        if ($this->auth->isAdmin()) {


            redirect(
                'admin/users'
            );
        }



        if ($this->auth->isCustomer()) {


            redirect(
                'user/products'
            );
        }



        $this->session->set_flashdata(
            'error',
            'Your account role is not allowed.'
        );


        redirect('login');
    }




    protected function redirect_with_validation_errors($redirectUrl)
    {

        $this->session->set_flashdata(
            'field_errors',
            $this->form_validation->error_array()
        );


        $this->session->set_flashdata(
            'old_input',
            $this->input->post(NULL, TRUE) ?: array()
        );


        return redirect($redirectUrl);
    }
}
