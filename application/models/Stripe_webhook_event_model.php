<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Stripe_webhook_event_model extends CI_Model
{


    protected $table =
        'stripe_webhook_events';



    public function insert($data)
    {

        $this->db->insert(
            $this->table,
            $data
        );


        return $this->db->insert_id();

    }



public function where(
    $column,
    $value
)
{
    $this->db->where(
        $column,
        $value
    );

    return $this;
}



public function count_all_results()
{
    return $this->db
        ->from($this->table)
        ->count_all_results();
}



    public function update(
        $id,
        $data
    )
    {

        return $this->db
        ->where(
            'id',
            $id
        )
        ->update(
            $this->table,
            $data
        );

    }


}