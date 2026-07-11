<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class IdempotencyRepository
{


    protected $CI;



    public function __construct()
    {

        $this->CI =& get_instance();

    }





    public function find($key)
    {

        return $this->CI->db
            ->where(
                'idempotency_key',
                $key
            )
            ->get(
                'idempotency_keys'
            )
            ->row();

    }






    public function create($data)
    {

        return $this->CI->db
            ->insert(
                'idempotency_keys',
                $data
            );

    }







    public function complete(
        $key,
        $response,
        $code=200
    )
    {

        return $this->CI->db
            ->where(
                'idempotency_key',
                $key
            )
            ->update(
                'idempotency_keys',
                [

                    'status'=>'completed',

                    'response_code'=>$code,

                    'response_data'=>
                        json_encode($response),

                    'updated_at'=>
                        date('Y-m-d H:i:s')

                ]
            );

    }







    public function fail(
        $key,
        $message
    )
    {

        return $this->CI->db
            ->where(
                'idempotency_key',
                $key
            )
            ->update(
                'idempotency_keys',
                [

                    'status'=>'failed',

                    'response_code'=>500,

                    'response_data'=>json_encode([
                        'error'=>$message
                    ]),

                    'updated_at'=>
                        date('Y-m-d H:i:s')

                ]
            );

    }



}