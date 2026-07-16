<?php

use PHPUnit\Framework\TestCase;


class AuditLogServiceTest extends TestCase
{
    private $repository;
    private $service;


    protected function setUp(): void
    {
        $this->repository = $this->createMock(
            AuditLogRepositoryInterface::class
        );


        $this->service = new AuditLogService(
            $this->repository
        );
    }


    public function testLogSuccess()
    {
        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['action'] === 'CREATE'
                        &&
                        $data['entity_type'] === 'PERMISSION'
                        &&
                        $data['entity_id'] === 1
                        &&
                        isset($data['created_at']);
                })
            )
            ->willReturn(1);


        $result = $this->service->log(
            'create',
            'PERMISSION',
            1,
            null,
            [
                'code' => 'USER_CREATE'
            ]
        );


        $this->assertEquals(
            1,
            $result
        );
    }


    public function testLogWithOldAndNewData()
    {
        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['old_data']
                        === json_encode([
                            'name' => 'Old'
                        ])

                        &&

                        $data['new_data']
                        === json_encode([
                            'name' => 'New'
                        ]);
                })
            )
            ->willReturn(1);


        $result = $this->service->log(
            'UPDATE',
            'PERMISSION',
            1,
            [
                'name' => 'Old'
            ],
            [
                'name' => 'New'
            ]
        );


        $this->assertEquals(
            1,
            $result
        );
    }


    public function testGetHistory()
    {
        $logs = [
            (object)[
                'id' => 1,
                'action' => 'CREATE'
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


        $result = $this->service->getHistory();


        $this->assertEquals(
            $logs,
            $result
        );
    }


    public function testGetHistoryWithFilter()
    {
        $filters = [
            'entity_type' => 'PERMISSION'
        ];


        $this->repository
            ->expects($this->once())
            ->method('getLogs')
            ->with(
                $filters,
                50,
                10
            )
            ->willReturn([]);


        $result = $this->service->getHistory(
            $filters,
            50,
            10
        );


        $this->assertEquals(
            [],
            $result
        );
    }
    public function testLogWithoutOldAndNewData()
    {
        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['old_data'] === null &&
                        $data['new_data'] === null;
                })
            )
            ->willReturn(1);

        $result = $this->service->log(
            'DELETE',
            'PERMISSION',
            1
        );

        $this->assertEquals(1, $result);
    }
    public function testLogFailure()
    {
        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(false);

        $this->assertFalse(
            $this->service->log(
                'CREATE',
                'PERMISSION'
            )
        );
    }
}
