<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/LookupGroupRepositoryInterface.php';
require_once APPPATH . 'repositories/LookupGroupRepository.php';

class LookupGroupService
{
    /**
     * @var LookupGroupRepositoryInterface
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = new LookupGroupRepository();
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
