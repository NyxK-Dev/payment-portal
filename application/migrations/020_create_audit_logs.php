<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_audit_logs extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => TRUE,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
            ],
            'entity_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
            ],
            'entity_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => TRUE,
            ],
            'old_data' => [
                'type' => 'JSON',
                'null' => TRUE,
            ],
            'new_data' => [
                'type' => 'JSON',
                'null' => TRUE,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('entity_type');
        $this->dbforge->add_key('entity_id');
        $this->dbforge->add_key('action');

        $this->dbforge->create_table('audit_logs');
        $this->db->query("
        ALTER TABLE audit_logs
        ADD CONSTRAINT fk_audit_logs_user
        FOREIGN KEY(user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
    ");
    }

    public function down()
    {
        $this->dbforge->drop_table('audit_logs');
    }
}
