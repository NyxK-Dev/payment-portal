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
    public function getByGroupCode($groupCode)
    {
        return $this->CI->Lookup_model->getByGroupCode($groupCode);
    }

    // public function findByGroupAndCode(
    //     int $groupId,
    //     string $code
    // ) {

    //     return $this->CI->db
    //         ->where([
    //             'group_id' => $groupId,
    //             'code' => $code
    //         ])
    //         ->get('lookups')
    //         ->row();
    // }
     public function findByGroupAndCode(
        int $groupId,
        string $code
    )
    {

        return $this->CI->db
            ->select([
                'lookups.id',
                'lookups.code',
                'lookups.value',
                'lookups.description'
            ])
            ->from('lookups')
            ->where(
                'lookups.group_id',
                $groupId
            )
            ->where(
                'lookups.code',
                $code
            )
            ->where(
                'lookups.is_active',
                1
            )
            ->limit(1)
            ->get()
            ->row();

    }
     public function findByCode(
    string $groupCode,
    string $code
)
{

    return $this->CI->db
        ->select([
            'lookups.id',
            'lookups.group_id',
            'lookups.code'
        ])
        ->from('lookups')
        ->join(
            'lookup_groups',
            'lookup_groups.id = lookups.group_id'
        )
        ->where(
            'lookup_groups.code',
            $groupCode
        )
        ->where(
            'lookups.code',
            $code
        )
        ->limit(1)
        ->get()
        ->row();

}


    /**
     * Get user status
     *
     * Example:
     * pending
     * active
     * blocked
     */
    public function getStatusLookup(string $status)
    {
        return $this->findByCode(
            'user_status',
            $status
        );
    }

      /**
     * Get all lookup values by group
     */
 
}
