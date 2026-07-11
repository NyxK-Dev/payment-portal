<?php


class IdempotencyService
{


    protected $CI;



    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->repository(
            'IdempotencyRepository'
        );

    }






    public function start(
        $key,
        $userId,
        $payload
    )
    {


        $hash =
            hash(
                'sha256',
                json_encode($payload)
            );



        $existing =
            $this->CI
                ->idempotencyrepository
                ->find($key);





        if($existing)
        {



            if(
                $existing->request_hash !== $hash
            )
            {

                throw new Exception(
                    'Idempotency key reused with different request'
                );

            }






            if(
                $existing->status === 'completed'
            )
            {


                return [

                    'duplicate'=>true,

                    'response'=>
                    json_decode(
                        $existing->response_data,
                        true
                    )

                ];

            }






            if(
                $existing->status === 'processing'
            )
            {

                throw new Exception(
                    'Request already processing'
                );

            }






            if(
                $existing->status === 'failed'
            )
            {

                throw new Exception(
                    'Previous request failed'
                );

            }


        }





        try
        {


            $this->CI
                ->idempotencyrepository
                ->create([


                    'user_id'=>$userId,


                    'idempotency_key'=>$key,


                    'request_hash'=>$hash,


                    'status'=>'processing',


                    'locked_at'=>
                        date('Y-m-d H:i:s'),


                    'expires_at'=>
                        date(
                            'Y-m-d H:i:s',
                            strtotime('+1 day')
                        ),


                    'created_at'=>
                        date('Y-m-d H:i:s'),


                    'updated_at'=>
                        date('Y-m-d H:i:s')

                ]);



        }
        catch(Exception $e)
        {


            /*
              Another request inserted first
            */


            $existing =
                $this->CI
                ->idempotencyrepository
                ->find($key);



            if($existing)
            {


                if(
                    $existing->status === 'completed'
                )
                {

                    return [

                        'duplicate'=>true,

                        'response'=>
                        json_decode(
                            $existing->response_data,
                            true
                        )

                    ];

                }


            }



            throw $e;

        }





        return [

            'duplicate'=>false

        ];


    }

}