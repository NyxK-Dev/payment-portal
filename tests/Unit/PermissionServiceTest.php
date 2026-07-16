<?php

use PHPUnit\Framework\TestCase;


class PermissionServiceTest extends TestCase
{
    private $repository;
    private $auditService;
    private $service;


    protected function setUp(): void
    {
        $this->repository = $this->createMock(
            PermissionRepositoryInterface::class
        );


        $this->auditService = $this->createMock(
            AuditLogService::class
        );


        $this->service = new PermissionService(
            $this->repository,
            $this->auditService
        );
    }


    public function testGetPermissions()
    {
        $permissions = [
            (object)[
                'id' => 1,
                'code' => 'USER_CREATE',
                'name' => 'Create User'
            ]
        ];


        $this->repository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($permissions);


        $this->assertEquals(
            $permissions,
            $this->service->getPermissions()
        );
    }


    public function testGetPermission()
    {
        $permission = (object)[
            'id' => 1,
            'code' => 'USER_CREATE',
            'name' => 'Create User'
        ];


        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($permission);


        $this->assertEquals(
            $permission,
            $this->service->getPermission(1)
        );
    }


    public function testCreateSuccess()
    {
        $this->repository
            ->expects($this->once())
            ->method('existsCode')
            ->with('USER_CREATE')
            ->willReturn(false);


        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(1);


        $this->auditService
            ->expects($this->once())
            ->method('log')
            ->willReturn(true);


        $result = $this->service->create([
            'code' => 'USER_CREATE',
            'name' => 'Create User'
        ]);


        $this->assertEquals(
            1,
            $result
        );
    }


    public function testCreateRequiresCode()
    {
        $this->expectException(Exception::class);

        $this->expectExceptionMessage(
            'Permission code is required'
        );


        $this->service->create([
            'name' => 'Create User'
        ]);
    }


    public function testCreateRequiresName()
    {
        $this->expectException(Exception::class);

        $this->expectExceptionMessage(
            'Permission name is required'
        );


        $this->service->create([
            'code' => 'USER_CREATE'
        ]);
    }


    public function testCreateDuplicateCode()
    {
        $this->repository
            ->expects($this->once())
            ->method('existsCode')
            ->with('USER_CREATE')
            ->willReturn(true);


        $this->expectException(Exception::class);

        $this->expectExceptionMessage(
            'Permission code already exists'
        );


        $this->service->create([
            'code' => 'USER_CREATE',
            'name' => 'Create User'
        ]);
    }


    public function testUpdateSuccess()
    {
        $oldPermission = (object)[
            'id' => 1,
            'code' => 'OLD_CODE',
            'name' => 'Old Name',
            'description' => null
        ];


        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($oldPermission);


        $this->repository
            ->expects($this->once())
            ->method('existsCode')
            ->with(
                'NEW_CODE',
                1
            )
            ->willReturn(false);


        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willReturn(true);


        $this->auditService
            ->expects($this->once())
            ->method('log')
            ->willReturn(true);


        $result = $this->service->update(
            1,
            [
                'code' => 'NEW_CODE',
                'name' => 'New Name'
            ]
        );


        $this->assertTrue($result);
    }


    public function testUpdateNotFound()
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);


        $this->assertFalse(
            $this->service->update(
                1,
                [
                    'name' => 'Test'
                ]
            )
        );
    }


    public function testDeleteSuccess()
    {
        $permission = (object)[
            'id' => 1,
            'code' => 'USER_CREATE'
        ];


        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($permission);


        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);


        $this->auditService
            ->expects($this->once())
            ->method('log')
            ->willReturn(true);


        $this->assertTrue(
            $this->service->delete(1)
        );
    }


    public function testDeleteNotFound()
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);


        $this->assertFalse(
            $this->service->delete(1)
        );
    }
    public function testGetPermissionNotFound()
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(99)
            ->willReturn(null);

        $this->assertNull(
            $this->service->getPermission(99)
        );
    }
    public function testUpdateDuplicateCode()
    {
        $permission = (object)[
            'id' => 1,
            'code' => 'OLD',
            'name' => 'Old',
            'description' => null
        ];

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->willReturn($permission);

        $this->repository
            ->expects($this->once())
            ->method('existsCode')
            ->with('NEW_CODE', 1)
            ->willReturn(true);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Permission code already exists'
        );

        $this->service->update(
            1,
            [
                'code' => 'NEW_CODE'
            ]
        );
    }
    public function testUpdateFail()
    {
        $permission = (object)[
            'id' => 1,
            'code' => 'OLD',
            'name' => 'Old',
            'description' => null
        ];

        $this->repository
            ->method('find')
            ->willReturn($permission);

        $this->repository
            ->method('existsCode')
            ->willReturn(false);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willReturn(false);

        $this->assertFalse(
            $this->service->update(
                1,
                ['name' => 'New']
            )
        );
    }
    public function testDeleteFail()
    {
        $permission = (object)[
            'id' => 1,
            'code' => 'USER_CREATE'
        ];

        $this->repository
            ->method('find')
            ->willReturn($permission);

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $this->assertFalse(
            $this->service->delete(1)
        );
    }
}
