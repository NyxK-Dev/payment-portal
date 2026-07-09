<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/LookupRepositoryInterface.php';
require_once APPPATH . 'repositories/LookupRepository.php';

class LookupService
{
    /**
     * @var LookupRepositoryInterface
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = new LookupRepository();
    }

    public function getByGroup($groupId)
    {
        return $this->repository->getByGroup($groupId);
    }

    public function getAllWithGroup()
    {
        return $this->repository->getAllWithGroup();
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

    public function countByGroup($groupId)
    {
        return $this->repository->countByGroup($groupId);
    }
}
