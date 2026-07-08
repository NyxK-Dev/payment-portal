<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_lookup_groups extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('lookup_groups')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => FALSE,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('lookup_groups', TRUE);
        $this->db->query('ALTER TABLE `lookup_groups` ADD UNIQUE `uq_lookup_groups_code` (`code`)');
    }

    public function down()
    {
        $this->dbforge->drop_table('lookup_groups', TRUE);
    }
}
