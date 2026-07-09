<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserSeeder
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function run()
    {
        $adminRole = $this->CI->db->get_where('roles', ['name' => 'admin'])->row();
        $customerRole = $this->CI->db->get_where('roles', ['name' => 'customer'])->row();
        $activeStatus = $this->CI->db
            ->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'user_status')
            ->where('lookups.code', 'active')
            ->get()
            ->row();

        $now = date('Y-m-d H:i:s');

        $this->CI->db->insert_batch('users', [
            [
                'role_id' => $adminRole->id,
                'name' => 'Platform Admin',
                'email' => 'admin@example.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'status_lookup_id' => $activeStatus ? $activeStatus->id : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => $customerRole->id,
                'name' => 'Example Customer',
                'email' => 'customer@example.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'status_lookup_id' => $activeStatus ? $activeStatus->id : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
