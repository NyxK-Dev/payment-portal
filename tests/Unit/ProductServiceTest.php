<?php

use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{


    protected $service;

    protected $productRepository;

    protected $auditService;



    protected function setUp(): void
    {


        $this->productRepository =
            $this->createMock(
                ProductInterface::class
            );



        $this->auditService =
            $this->createMock(
                AuditLogService::class
            );



        $this->service =
            new Product_Service(
                $this->productRepository,
                $this->auditService
            );

    }





    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */


    public function test_create_product_success()
    {


        $request=[

            'name'=>'Laptop',

            'description'=>'Gaming Laptop',

            'sku'=>'LAP-001',

            'price'=>1200,

            'stock_qty'=>10,

            'category_lookup_id'=>1,

            'status_lookup_id'=>1

        ];



        $this->productRepository
            ->expects($this->once())
            ->method('insert')
            ->with(
                $this->callback(function($data){

                    return

                    $data['name']=='Laptop'
                    &&
                    $data['sku']=='LAP-001'
                    &&
                    $data['price']==1200
                    &&
                    $data['stock_qty']==10
                    &&
                    $data['created_by']==5
                    &&
                    isset($data['created_at']);

                })
            )
            ->willReturn(100);



        $result =
            $this->service
                ->create(
                    $request,
                    5
                );



        $this->assertEquals(
            100,
            $result
        );

    }







    public function test_update_product_success()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('update')
            ->with(
                10,
                $this->callback(function($data){

                    return

                    $data['name']=='Phone'
                    &&
                    $data['price']==500
                    &&
                    isset($data['updated_at']);

                })
            )
            ->willReturn(true);



        $result =
            $this->service
                ->update(
                    10,
                    [

                    'name'=>'Phone',
                    'description'=>'Smart phone',
                    'sku'=>'PH001',
                    'price'=>500,
                    'stock_qty'=>20,
                    'category_lookup_id'=>1,
                    'status_lookup_id'=>1

                    ]
                );



        $this->assertTrue(
            $result
        );

    }







    public function test_delete_product_success()
    {


        $product=(object)[

            'id'=>10,

            'name'=>'Laptop'

        ];



        $this->productRepository
            ->expects($this->once())
            ->method('find')
            ->with(10)
            ->willReturn($product);



        $this->productRepository
            ->expects($this->once())
            ->method('softDelete')
            ->with(10)
            ->willReturn(true);



        $result =
            $this->service
                ->delete(10);



        $this->assertTrue(
            $result
        );

    }








    public function test_get_create_data_success()
    {


        $this->productRepository
            ->expects($this->exactly(2))
            ->method('getLookupsByGroup')
            ->willReturn([]);



        $result =
            $this->service
                ->getCreateData();



        $this->assertArrayHasKey(
            'categories',
            $result
        );


        $this->assertArrayHasKey(
            'statuses',
            $result
        );

    }






    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */



    public function test_create_product_failed()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('insert')
            ->willReturn(false);



        $result =
            $this->service
                ->create(
                    [
                        'name'=>'Laptop'
                    ],
                    5
                );



        $this->assertFalse(
            $result
        );

    }







    public function test_update_product_failed()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('update')
            ->willReturn(false);



        $result =
            $this->service
                ->update(
                    1,
                    [

                    'name'=>'Phone',
                    'description'=>'',
                    'sku'=>'',
                    'price'=>100,
                    'stock_qty'=>1,
                    'category_lookup_id'=>1,
                    'status_lookup_id'=>1

                    ]
                );



        $this->assertFalse(
            $result
        );

    }







    public function test_delete_product_not_found()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('find')
            ->with(99)
            ->willReturn(null);



        $this->productRepository
            ->expects($this->never())
            ->method('softDelete');



        $result =
            $this->service
                ->delete(99);



        $this->assertFalse(
            $result
        );

    }








    public function test_repository_exception_create()
    {


        $this->productRepository
            ->method('insert')
            ->willThrowException(
                new Exception(
                    "Database error"
                )
            );



        $this->expectException(
            Exception::class
        );



        $this->service
            ->create(
                [
                    'name'=>'Laptop'
                ],
                1
            );

    }








    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */


    public function test_create_missing_name()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('insert')
            ->with(
                $this->callback(function($data){

                    return $data['name']===null;

                })
            )
            ->willReturn(1);



        $result =
            $this->service
                ->create(
                    [],
                    1
                );



        $this->assertEquals(
            1,
            $result
        );

    }







    public function test_create_invalid_user_id()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('insert')
            ->with(
                $this->callback(function($data){

                    return $data['created_by']==0;

                })
            )
            ->willReturn(1);



        $result =
            $this->service
                ->create(
                    [
                        'name'=>'Laptop'
                    ],
                    0
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


    public function test_default_stock_quantity()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('insert')
            ->with(
                $this->callback(function($data){

                    return
                    $data['stock_qty']==0;

                })
            )
            ->willReturn(1);



        $this->service
            ->create(
                [
                    'name'=>'Mouse'
                ],
                1
            );

    }







    public function test_optional_fields_null()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('insert')
            ->with(
                $this->callback(function($data){

                    return

                    $data['description']===null
                    &&
                    $data['sku']===null;

                })
            )
            ->willReturn(1);



        $this->service
            ->create(
                [
                    'name'=>'Keyboard'
                ],
                1
            );

    }







    /*
    |--------------------------------------------------------------------------
    | REPOSITORY INTERACTION RULES
    |--------------------------------------------------------------------------
    */



    public function test_create_calls_insert_once()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('insert')
            ->willReturn(1);



        $this->service
            ->create(
                [
                    'name'=>'Laptop'
                ],
                1
            );

    }







    public function test_delete_checks_product_before_delete()
    {


        $product=(object)[
            'id'=>1
        ];



        $this->productRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($product);



        $this->productRepository
            ->expects($this->once())
            ->method('softDelete')
            ->with(1)
            ->willReturn(true);



        $this->service
            ->delete(1);

    }








 public function test_failed_create_should_not_audit()
{

    $request = [

        'name' => 'Laptop',
        'description' => 'Gaming Laptop',
        'sku' => 'LAP-001',
        'price' => 1200,
        'stock_qty' => 10,
        'category_lookup_id' => 1,
        'status_lookup_id' => 1

    ];



    $this->productRepository
        ->expects($this->once())
        ->method('insert')
        ->willReturn(false);



    $result =
        $this->service
             ->create(
                $request,
                5
             );



    $this->assertFalse(
        $result
    );

}



    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC RULES
    |--------------------------------------------------------------------------
    */



    public function test_created_by_current_user()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('insert')
            ->with(
                $this->callback(function($data){

                    return
                    $data['created_by']==10;

                })
            )
            ->willReturn(1);



        $this->service
            ->create(
                [
                    'name'=>'Laptop'
                ],
                10
            );

    }







    public function test_update_does_not_change_created_by()
    {


        $this->productRepository
            ->expects($this->once())
            ->method('update')
            ->with(
                1,
                $this->callback(function($data){

                    return
                    !isset($data['created_by']);

                })
            )
            ->willReturn(true);



        $this->service
            ->update(
                1,
                [

                'name'=>'New Laptop',
                'description'=>'',
                'sku'=>'NEW',
                'price'=>100,
                'stock_qty'=>1,
                'category_lookup_id'=>1,
                'status_lookup_id'=>1

                ]
            );

    }






    public function test_delete_is_soft_delete()
    {


        $product=(object)[
            'id'=>5
        ];



        $this->productRepository
            ->method('find')
            ->willReturn($product);



        $this->productRepository
            ->expects($this->once())
            ->method('softDelete')
            ->with(5)
            ->willReturn(true);



        $this->service
            ->delete(5);

    }


}