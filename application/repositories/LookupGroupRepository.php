<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/LookupGroupRepositoryInterface.php';

class LookupGroupRepository implements LookupGroupRepositoryInterface
{
    protected $model;

    public function __construct($model = null)
    {
        $this->model = $model ?: get_instance()->LookupGroup_model;
    }

    public function getAll()
    {
        return $this->model->getAll();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->model->update($id, $data);
    }
}
