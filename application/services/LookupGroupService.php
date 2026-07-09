<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/LookupGroupRepositoryInterface.php';
require_once APPPATH . 'repositories/LookupGroupRepository.php';

/**
 * @property LookupGroup_model $lookupgroup_model
 */
class LookupGroupService
{
    protected $repository;

    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->model('LookupGroup_model');

        $this->repository = new LookupGroupRepository($CI->LookupGroup_model);
    }

    public function getAll()
    {
        return $this->repository->getAll();
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
}
