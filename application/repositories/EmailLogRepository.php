<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/EmailLogRepositoryInterface.php';
class EmailLogRepository implements EmailLogRepositoryInterface
{

    protected $CI;

    protected $table = 'email_logs';


    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->database();
    }


    /**
     * Insert email log
     */
    public function create(array $data)
    {

        $this->CI->db->insert(
            $this->table,
            $data
        );


        return $this->CI->db->insert_id();
    }



    /**
     * Find single log
     */
    public function find($id)
    {

        return $this->CI->db
            ->where('id',$id)
            ->get($this->table)
            ->row();

    }



    /**
     * Get logs
     */
    public function all(array $filters = [])
    {


        if(isset($filters['user_id']))
        {
            $this->CI->db
                ->where(
                    'user_id',
                    $filters['user_id']
                );
        }


        return $this->CI->db
            ->order_by('id','DESC')
            ->get($this->table)
            ->result();

    }



    /**
     * Change email status
     */
    public function updateStatus(
        $id,
        $statusLookupId
    )
    {

        return $this->CI->db
            ->where('id',$id)
            ->update(
                $this->table,
                [
                    'status_lookup_id'=>$statusLookupId
                ]
            );

    }

}