<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/LookupGroupRepositoryInterface.php';

class LookupGroupRepository implements LookupGroupRepositoryInterface
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('LookupGroup_model');
    }

    public function all()
    {
        return $this->CI->LookupGroup_model->getAll();
    }

    public function find($id)
    {
        return $this->CI->LookupGroup_model->find($id);
    }

    public function create(array $data)
    {
        return $this->CI->LookupGroup_model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->CI->LookupGroup_model->update($id, $data);
    }

    public function delete($id)
    {
        return $this->CI->LookupGroup_model->delete($id);
    }
}
