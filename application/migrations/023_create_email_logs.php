<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_email_logs extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('email_logs')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'user_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
                'null'       => TRUE,
            ],
            'email_to' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => TRUE,
            ],
            'response' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->create_table('email_logs', TRUE);
        $this->db->query('ALTER TABLE `email_logs` ADD CONSTRAINT `fk_email_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE SET NULL');
    }

    public function down()
    {
        $this->dbforge->drop_table('email_logs', TRUE);
    }
}
