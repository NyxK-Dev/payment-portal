<?php

use PHPUnit\Framework\TestCase;


class AuditLogServiceTest extends TestCase
{

    protected $repository;
    protected $service;



    protected function setUp(): void
    {

        global $CI;


        /*
        |--------------------------------------------------------------------------
        | Mock CI
        |--------------------------------------------------------------------------
        */


        $session = $this->getMockBuilder(stdClass::class)
            ->addMethods([
                'userdata'
            ])
            ->getMock();


        $session
            ->method('userdata')
            ->with('user_id')
            ->willReturn(10);



        $input = $this->getMockBuilder(stdClass::class)
            ->addMethods([
                'ip_address',
                'user_agent'
            ])
            ->getMock();



        $input
            ->method('ip_address')
            ->willReturn(
                '127.0.0.1'
            );


        $input
            ->method('user_agent')
            ->willReturn(
                'PHPUnit'
            );



        $CI = new stdClass();


        $CI->session = $session;

        $CI->input = $input;


        $GLOBALS['CI'] = $CI;




        /*
        |--------------------------------------------------------------------------
        | Repository
        |--------------------------------------------------------------------------
        */


        $this->repository =
            $this->createMock(
                AuditLogRepositoryInterface::class
            );




        $this->service =
            new AuditLogService(
                $this->repository
            );

    }





    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */


    public function test_log_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['action'] === 'CREATE'
                        &&
                        $data['entity_type'] === 'USER'
                        &&
                        $data['entity_id'] === 1
                        &&
                        $data['user_id'] === 10
                        &&
                        $data['ip_address'] === '127.0.0.1'
                        &&
                        $data['user_agent'] === 'PHPUnit'
                        &&
                        isset($data['created_at']);

                })
            )
            ->willReturn(1);



        $result =
            $this->service->log(
                'create',
                'USER',
                1,
                null,
                [
                    'name'=>'Admin'
                ]
            );



        $this->assertEquals(
            1,
            $result
        );

    }





    public function test_log_with_old_and_new_data()
    {


        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['old_data']
                        === json_encode([
                            'name'=>'Old'
                        ])

                        &&

                        $data['new_data']
                        === json_encode([
                            'name'=>'New'
                        ]);

                })
            )
            ->willReturn(1);



        $result =
            $this->service->log(
                'UPDATE',
                'USER',
                1,
                [
                    'name'=>'Old'
                ],
                [
                    'name'=>'New'
                ]
            );



        $this->assertEquals(
            1,
            $result
        );

    }






    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */


    public function test_repository_create_failure()
    {


        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(false);



        $result =
            $this->service->log(
                'CREATE',
                'USER'
            );



        $this->assertFalse(
            $result
        );

    }






    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */


    public function test_action_is_converted_to_uppercase()
    {


        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['action']
                        ===
                        'DELETE';

                })
            )
            ->willReturn(1);



        $this->service->log(
            'delete',
            'USER'
        );

    }





    public function test_log_without_entity_id()
    {


        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['entity_id'] === null;

                })
            )
            ->willReturn(1);



        $result =
            $this->service->log(
                'CREATE',
                'SYSTEM'
            );



        $this->assertEquals(
            1,
            $result
        );

    }







    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */


    public function test_empty_old_and_new_data_saved_as_null()
    {


        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['old_data'] === null
                        &&
                        $data['new_data'] === null;

                })
            )
            ->willReturn(1);



        $this->service->log(
            'DELETE',
            'USER',
            5,
            [],
            []
        );

    }







    public function test_large_payload_is_json_encoded()
    {


        $largeData = [

            'description'=>str_repeat(
                'A',
                1000
            )

        ];



        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        is_string(
                            $data['new_data']
                        );

                })
            )
            ->willReturn(1);



        $this->service->log(
            'UPDATE',
            'USER',
            1,
            null,
            $largeData
        );

    }






    /*
    |--------------------------------------------------------------------------
    | REPOSITORY INTERACTION RULES
    |--------------------------------------------------------------------------
    */


    public function test_repository_called_only_once()
    {


        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(1);



        $this->service->log(
            'CREATE',
            'USER'
        );

    }





    public function test_get_history_default_parameters()
    {


        $logs = [

            (object)[
                'id'=>1
            ]

        ];



        $this->repository
            ->expects($this->once())
            ->method('getLogs')
            ->with(
                [],
                100,
                0
            )
            ->willReturn($logs);



        $result =
            $this->service->getHistory();



        $this->assertEquals(
            $logs,
            $result
        );

    }






    public function test_get_history_with_filters()
    {

        $filters=[

            'entity_type'=>'USER'

        ];



        $this->repository
            ->expects($this->once())
            ->method('getLogs')
            ->with(
                $filters,
                20,
                5
            )
            ->willReturn([]);



        $result =
            $this->service->getHistory(
                $filters,
                20,
                5
            );



        $this->assertEquals(
            [],
            $result
        );

    }





    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC RULES
    |--------------------------------------------------------------------------
    */


    public function test_update_action_stores_before_after_values()
    {


        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){


                    return
                        $data['action']=='UPDATE'
                        &&
                        $data['old_data']
                        !== null
                        &&
                        $data['new_data']
                        !== null;


                })
            )
            ->willReturn(1);



        $this->service->log(
            'UPDATE',
            'ROLE',
            2,
            [
                'name'=>'User'
            ],
            [
                'name'=>'Admin'
            ]
        );

    }

}