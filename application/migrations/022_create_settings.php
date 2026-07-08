<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_settings extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('settings')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => FALSE,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('settings', TRUE);
        $this->db->query('ALTER TABLE `settings` ADD UNIQUE `uq_settings_setting_key` (`setting_key`)');
    }

    public function down()
    {
        $this->dbforge->drop_table('settings', TRUE);
    }
}
