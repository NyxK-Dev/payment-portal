<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/LookupRepositoryInterface.php';

class LookupRepository implements LookupRepositoryInterface
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Lookup_model');
    }

    public function getByGroup($groupId)
    {
        return $this->CI->Lookup_model->getByGroup($groupId);
    }

    public function getAllWithGroup()
    {
        return $this->CI->Lookup_model->getAllWithGroup();
    }

    public function find($id)
    {
        return $this->CI->Lookup_model->find($id);
    }

    public function create(array $data)
    {
        return $this->CI->Lookup_model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->CI->Lookup_model->update($id, $data);
    }

    public function delete($id)
    {
        return $this->CI->Lookup_model->delete($id);
    }

    public function countByGroup($groupId)
    {
        return $this->CI->Lookup_model->countByGroup($groupId);
    }
    public function findOrderStatusByCode($code)
    {
        return $this->CI
            ->Lookup_model
            ->findByCode(
                $code,
                'order_status'
            );
    }
}
