<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property Lookup_model $lookup_model
 */
class LookupService
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Lookup_model', 'lookup_model');
    }

    public function getByGroup($groupId)
    {
        return $this->CI->lookup_model->getByGroup($groupId);
    }
    public function getAllWithGroup()
    {
        return $this->CI->lookup_model->getAllWithGroup();
    }

    public function find($id)
    {
        return $this->CI->lookup_model->find($id);
    }

    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->CI->lookup_model->create($data);
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->CI->lookup_model->update($id, $data);
    }

    public function delete($id)
    {
        return $this->CI->lookup_model->delete($id);
    }

    public function countByGroup($groupId)
    {
        return $this->CI
            ->lookup_model
            ->countByGroup($groupId);
    }
}
