<?php

class Seeder
{
    protected $CI;


    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }


    public function run()
    {

        $exists = $this->CI->db
            ->where('email','admin@example.com')
            ->get('users')
            ->row();


        if(!$exists){

            $this->CI->db->insert('roles',[
                'name'=>'admin'
            ]);


            $this->CI->db->insert('users',[
                'name'=>'Administrator',
                'email'=>'admin@example.com',
                'password'=>password_hash(
                    'password',
                    PASSWORD_DEFAULT
                ),
                'role_id'=>1
            ]);

        }

    }
}