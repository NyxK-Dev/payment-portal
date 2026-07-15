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



    public function test_create_product()
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
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['name'] === 'Laptop' &&
                        $data['sku'] === 'LAP-001' &&
                        $data['price'] === 1200 &&
                        $data['stock_qty'] === 10 &&
                        $data['created_by'] === 5 &&
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





    public function test_update_product()
    {

        $request = [

            'name' => 'Phone',
            'description' => 'Smart phone',
            'sku' => 'PH-001',
            'price' => 500,
            'stock_qty' => 20,
            'category_lookup_id' => 2,
            'status_lookup_id' => 1

        ];



        $this->productRepository
            ->expects($this->once())
            ->method('update')
            ->with(
                10,
                $this->callback(function ($data) {

                    return
                        $data['name'] === 'Phone' &&
                        $data['sku'] === 'PH-001' &&
                        $data['price'] === 500 &&
                        isset($data['updated_at']);
                })
            )
            ->willReturn(true);



        $result =
            $this->service
            ->update(
                10,
                $request
            );



        $this->assertTrue(
            $result
        );
    }

    public function test_delete_product()
    {

        $product = (object)[

            'id' => 10,

            'name' => 'Laptop'

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



        $this->assertTrue($result);
    }
    public function test_delete_product_not_found()
    {

        $this->productRepository
            ->expects($this->once())
            ->method('find')
            ->with(99)
            ->willReturn(null);



        $result =
            $this->service
            ->delete(
                99
            );



        $this->assertFalse(
            $result
        );
    }





    public function test_get_create_data()
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
    public function test_create_product_failed()
    {
        $request = [
            'name' => 'Laptop'
        ];

        $this->productRepository
            ->expects($this->once())
            ->method('insert')
            ->willReturn(false);

        $result = $this->service->create($request, 5);

        $this->assertFalse($result);
    }
    public function test_update_product_failed()
    {
        $this->productRepository
            ->expects($this->once())
            ->method('update')
            ->with(
                10,
                $this->anything()
            )
            ->willReturn(false);

        $result = $this->service->update(
            10,
            [
                'name' => 'Phone',
                'description' => '',
                'sku' => '',
                'price' => 100,
                'stock_qty' => 1,
                'category_lookup_id' => 1,
                'status_lookup_id' => 1
            ]
        );

        $this->assertFalse($result);
    }
    public function test_delete_product_failed()
    {
        $product = (object)[
            'id' => 10,
            'name' => 'Laptop'
        ];

        $this->productRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($product);

        $this->productRepository
            ->expects($this->once())
            ->method('softDelete')
            ->willReturn(false);

        $result = $this->service->delete(10);

        $this->assertFalse($result);
    }
}
