<?php
defined('BASEPATH') or exit('No direct script access allowed');


class RoleService
{

    protected $roleRepository;



    public function __construct(
        RoleRepositoryInterface $roleRepository
    )
    {
        $this->roleRepository = $roleRepository;
    }




    public function getRoles()
    {
        return $this->roleRepository
            ->getAll();
    }




    public function getRole($id)
    {

        if(
            !is_numeric($id)
            ||
            $id <= 0
        )
        {
            throw new InvalidArgumentException(
                'Invalid role id'
            );
        }


        return $this->roleRepository
            ->find($id);
    }





    public function create($data)
    {

        if(
            empty($data['name'])
            ||
            !is_string($data['name'])
        )
        {
            throw new InvalidArgumentException(
                'Role name required'
            );
        }



        if(
            $this->roleRepository
                 ->existsName(
                    $data['name']
                 )
        )
        {
            throw new Exception(
                'Role already exists'
            );
        }



        $data['created_at'] =
            date('Y-m-d H:i:s');



        return $this->roleRepository
            ->create($data);

    }





    public function update(
        $id,
        $data
    )
    {


        if(
            !is_numeric($id)
            ||
            $id <=0
        )
        {
            throw new InvalidArgumentException(
                'Invalid role id'
            );
        }



        if(
            empty($data['name'])
            ||
            !is_string($data['name'])
        )
        {
            throw new InvalidArgumentException(
                'Role name required'
            );
        }





        if(
            $this->roleRepository
                 ->existsName(
                    $data['name'],
                    $id
                 )
        )
        {
            throw new Exception(
                'Role already exists'
            );
        }



        $data['updated_at'] =
            date('Y-m-d H:i:s');



        return $this->roleRepository
            ->update(
                $id,
                $data
            );

    }





    public function delete($id)
    {

        if(
            !is_numeric($id)
            ||
            $id <=0
        )
        {
            throw new InvalidArgumentException(
                'Invalid role id'
            );
        }



        return $this->roleRepository
            ->delete($id);

    }


}