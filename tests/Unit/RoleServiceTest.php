<?php


use PHPUnit\Framework\TestCase;

class RoleServiceTest extends TestCase
{
    private $repository;
    private $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RoleRepositoryInterface::class);

        $this->service = new RoleService($this->repository);
    }

    public function testGetRoles()
    {
        $roles = [
            (object)['id' => 1, 'name' => 'Admin']
        ];

        $this->repository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($roles);

        $this->assertEquals($roles, $this->service->getRoles());
    }

    public function testGetRole()
    {
        $role = (object)[
            'id' => 1,
            'name' => 'Admin'
        ];

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($role);

        $this->assertEquals($role, $this->service->getRole(1));
    }
    public function testCreateSuccess()
    {
        $this->repository
            ->expects($this->once())
            ->method('existsName')
            ->with('Admin')
            ->willReturn(false);


        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(1);


        $result = $this->service->create([
            'name' => 'Admin'
        ]);


        $this->assertEquals(1, $result);
    }

    public function testCreateThrowsException()
    {
        $this->repository
            ->expects($this->once())
            ->method('existsName')
            ->willReturn(true);

        $this->expectException(Exception::class);

        $this->service->create([
            'name' => 'Admin'
        ]);
    }

    public function testUpdate()
    {
        $this->repository
            ->expects($this->once())
            ->method('existsName')
            ->willReturn(false);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willReturn(true);

        $this->assertTrue(
            $this->service->update(
                1,
                ['name' => 'Admin']
            )
        );
    }

    public function testDelete()
    {
        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        $this->assertTrue(
            $this->service->delete(1)
        );
    }
}
