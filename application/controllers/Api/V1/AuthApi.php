<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Api_Controller.php';
require_once APPPATH . 'libraries/ApiException.php';

class AuthApi extends MY_Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service('ApiAuthService');
    }

    public function register()
    {
        $this->only(['POST']);

        $input = $this->getJsonInput();

        $this->validateRequest('Auth', 'register', $input);

        try {
            $result = $this->apiauthservice->register($input);

            $this->sendResponse(
                $result,
                'Registration successful. A verification code has been sent to your email.',
                201
            );
        } catch (ApiException $e) {

            $this->sendError(
                $e->getMessage(),
                [],
                $e->status()
            );
        } catch (Exception $e) {

            log_message('error', $e->getMessage());

            $this->sendError(
                'Server error processing registration.',
                [],
                500
            );
        }
    }


    public function login()
    {
        $this->only(['POST']);

        $input = $this->getJsonInput();

        $this->validateRequest('Auth', 'login', $input);

        try {

            $result = $this->apiauthservice->login(
                $input['email'],
                $input['password']
            );


            $this->sendResponse(
                $result,
                'Login successful.'
            );
        } catch (ApiException $e) {

            $this->sendError(
                $e->getMessage(),
                [],
                $e->status()
            );
        } catch (Exception $e) {

            log_message('error', $e->getMessage());

            $this->sendError(
                'Server error during authentication.',
                [],
                500
            );
        }
    }


    public function verifyCode()
    {
        $this->only(['POST']);

        $input = $this->getJsonInput();


        if (empty($input['user_id']) || empty($input['code'])) {

            return $this->sendError(
                'User ID and verification code are required.',
                [],
                400
            );
        }


        try {

            $this->apiauthservice->verifyRegistrationCode(
                (int)$input['user_id'],
                trim($input['code']),
                $this->input->ip_address()
            );


            $this->sendResponse(
                [],
                'Your email address has been successfully verified. You can now log in.'
            );
        } catch (ApiException $e) {

            $this->sendError(
                $e->getMessage(),
                [],
                $e->status()
            );
        } catch (Exception $e) {

            log_message('error', $e->getMessage());

            $this->sendError(
                'An internal error occurred during verification.',
                [],
                500
            );
        }
    }


    public function resendCode()
    {
        $this->only(['POST']);

        $input = $this->getJsonInput();


        if (empty($input['email'])) {

            return $this->sendError(
                'Email address is required.',
                [],
                400
            );
        }


        try {

            $this->apiauthservice->resendRegistrationCode(
                trim($input['email'])
            );


            $this->sendResponse(
                [],
                'A fresh verification code has been sent to your email address.'
            );
        } catch (ApiException $e) {

            $this->sendError(
                $e->getMessage(),
                [],
                $e->status()
            );
        } catch (Exception $e) {

            log_message('error', $e->getMessage());

            $this->sendError(
                'Could not process resend request.',
                [],
                500
            );
        }
    }


    public function refresh()
    {
        $this->only(['POST']);

        $input = $this->getJsonInput();


        if (empty($input['refresh_token'])) {

            return $this->sendError(
                'Refresh token field is missing.',
                [],
                400
            );
        }


        try {

            $result = $this->apiauthservice->refresh(
                $input['refresh_token']
            );


            $this->sendResponse(
                $result,
                'Tokens successfully rotated.'
            );
        } catch (ApiException $e) {

            $this->sendError(
                $e->getMessage(),
                [],
                $e->status()
            );
        } catch (Exception $e) {

            log_message('error', $e->getMessage());

            $this->sendError(
                'Session invalid or expired.',
                [],
                401
            );
        }
    }


    public function logout()
    {
        $this->only(['POST']);

        $input = $this->getJsonInput();


        if (!empty($input['refresh_token'])) {

            try {

                $this->apiauthservice->logout(
                    $input['refresh_token']
                );
            } catch (Exception $e) {

                log_message(
                    'error',
                    'Logout tracking failure: ' . $e->getMessage()
                );
            }
        }


        $this->sendResponse(
            [],
            'Logged out successfully.'
        );
    }
}
