<?php
defined('BASEPATH') or exit('No direct script access allowed');


function apiResponse(
    $success,
    $message,
    $data = [],
    $status = 200
) {

    http_response_code($status);


    return json_encode(

        [
            'success' => $success,

            'message' => $message,

            'data' => $data

        ]

    );
}
