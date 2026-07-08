<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_lookups extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('lookups')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'group_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => TRUE,
            ],
            'value' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => FALSE,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => FALSE,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('group_id');
        $this->dbforge->create_table('lookups', TRUE);
        $this->db->query('ALTER TABLE `lookups` ADD CONSTRAINT `fk_lookups_group` FOREIGN KEY (`group_id`) REFERENCES `lookup_groups` (`id`) ON UPDATE CASCADE ON DELETE CASCADE');
    }

    public function down()
    {
        $this->dbforge->drop_table('lookups', TRUE);
    }
}
